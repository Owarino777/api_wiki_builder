<?php

namespace App\Services\GameDrafts;

use App\Models\Game;
use App\Models\GameDraft;
use App\Models\GameDraftStep;
use Illuminate\Support\Arr;

class GameDraftProgressService
{
    private const STEP_KEYS = [
        'foundation',
        'structure',
        'archetypes',
        'categories',
        'items',
        'modifiers',
        'review',
    ];

    public function __construct(
        private readonly GameCategoryTemplateRegistry $templateRegistry
    ) {
    }

    public function refresh(GameDraft $draft, ?string $savedStep = null): GameDraft
    {
        $draft->loadMissing([
            'steps',
            'archetypes',
            'categories',
            'items.categories',
            'items.itemSlots',
            'modifiers.items',
        ]);

        $steps = $this->buildSteps($draft);
        $now = now();

        foreach ($steps as $stepKey => $step) {
            $record = GameDraftStep::query()->firstOrNew([
                'game_draft_id' => $draft->id,
                'step_key' => $stepKey,
            ]);

            $record->status = $step['status'];
            $record->blocking_errors_count = count($step['errors']);
            $record->warnings_count = count($step['warnings']);
            $record->payload_snapshot = array_merge($step['snapshot'], [
                'errors' => $step['errors'],
                'warnings' => $step['warnings'],
            ]);
            $record->last_validated_at = $now;

            if ($stepKey === $savedStep) {
                $record->last_saved_at = $now;
            }

            $record->save();
        }

        $completionPercent = (int) round(
            (collect($steps)->filter(fn (array $step) => $step['status'] === 'complete')->count() / count(self::STEP_KEYS)) * 100
        );

        $currentStep = collect(self::STEP_KEYS)->first(
            fn (string $stepKey) => $steps[$stepKey]['status'] !== 'complete'
        ) ?? 'review';

        $status = $draft->status === 'published'
            ? 'published'
            : ($steps['review']['status'] === 'complete' ? 'ready' : 'in_progress');

        $draft->forceFill([
            'current_step' => $currentStep,
            'completion_percent' => $completionPercent,
            'last_validated_at' => $now,
            'status' => $status,
        ])->save();

        return $draft->fresh([
            'steps',
            'archetypes',
            'categories',
            'items.categories',
            'items.itemSlots',
            'modifiers.items',
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function publishErrors(GameDraft $draft): array
    {
        return $this->buildSteps($draft)['review']['errors'];
    }

    /**
     * @return array<string, array{status:string,errors:array<int,string>,warnings:array<int,string>,snapshot:array<string,mixed>}>
     */
    private function buildSteps(GameDraft $draft): array
    {
        $template = $this->templateRegistry->find($draft->category_key);
        $settings = is_array($draft->settings) ? $draft->settings : [];
        $minimums = is_array($template['minimums'] ?? null) ? $template['minimums'] : [];

        $foundationErrors = [];
        if (blank($draft->name)) {
            $foundationErrors[] = 'Draft name is required.';
        }
        if (blank($draft->slug)) {
            $foundationErrors[] = 'Draft slug is required.';
        }
        if (blank($draft->description)) {
            $foundationErrors[] = 'Draft description is required before publication.';
        }
        if ($template === null) {
            $foundationErrors[] = 'Selected category template is unavailable.';
        }
        if (Game::query()
            ->where('slug', $draft->slug)
            ->when($draft->published_game_id !== null, fn ($query) => $query->whereKeyNot($draft->published_game_id))
            ->exists()) {
            $foundationErrors[] = 'Slug already exists on a published game.';
        }
        if (Game::query()
            ->where('name', $draft->name)
            ->when($draft->published_game_id !== null, fn ($query) => $query->whereKeyNot($draft->published_game_id))
            ->exists()) {
            $foundationErrors[] = 'Name already exists on a published game.';
        }

        $allowedItemSlots = array_values(array_filter(Arr::wrap($settings['allowed_item_slots'] ?? []), fn ($value) => is_string($value) && $value !== ''));
        $allowedModifierTypes = array_values(array_filter(Arr::wrap($settings['allowed_modifier_types'] ?? []), fn ($value) => is_string($value) && $value !== ''));
        $templateItemSlots = Arr::wrap($template['allowed_item_slots'] ?? []);
        $templateModifierTypes = Arr::wrap($template['allowed_modifier_types'] ?? []);
        $supportsModifiers = (bool) ($settings['supports_modifiers'] ?? $template['supports_modifiers'] ?? false);
        $supportsArchetypes = (bool) ($settings['supports_archetypes'] ?? $template['supports_archetypes'] ?? false);

        $structureErrors = [];
        if ($allowedItemSlots === []) {
            $structureErrors[] = 'At least one item slot must be enabled.';
        }
        foreach ($allowedItemSlots as $slot) {
            if (! in_array($slot, $templateItemSlots, true)) {
                $structureErrors[] = sprintf('Unsupported item slot: %s.', $slot);
            }
        }
        if ($supportsModifiers && $allowedModifierTypes === []) {
            $structureErrors[] = 'At least one modifier type must be enabled when modifiers are supported.';
        }
        foreach ($allowedModifierTypes as $type) {
            if (! in_array($type, $templateModifierTypes, true)) {
                $structureErrors[] = sprintf('Unsupported modifier type: %s.', $type);
            }
        }

        $archetypeErrors = [];
        if ($supportsArchetypes && $draft->archetypes->count() < (int) ($minimums['archetypes'] ?? 1)) {
            $archetypeErrors[] = 'At least one archetype is required.';
        }

        $categoryErrors = [];
        if ($draft->categories->count() < (int) ($minimums['categories'] ?? 2)) {
            $categoryErrors[] = 'At least two categories are required.';
        }

        $itemErrors = [];
        if ($draft->items->count() < (int) ($minimums['items'] ?? 1)) {
            $itemErrors[] = sprintf('At least %d items are required.', (int) ($minimums['items'] ?? 1));
        }
        foreach ($draft->items as $item) {
            if (! in_array($item->slot, $allowedItemSlots, true)) {
                $itemErrors[] = sprintf('Item "%s" uses a slot that is not enabled.', $item->name);
            }
            if ($item->categories->isEmpty()) {
                $itemErrors[] = sprintf('Item "%s" must belong to at least one category.', $item->name);
            }
            foreach ($item->itemSlots as $slot) {
                if ($slot->accepted_modifier_type !== null && ! in_array($slot->accepted_modifier_type, $allowedModifierTypes, true)) {
                    $itemErrors[] = sprintf('Item "%s" contains an unsupported modifier slot type.', $item->name);
                }
            }
        }

        $modifierErrors = [];
        if ($supportsModifiers) {
            if ($draft->modifiers->count() < (int) ($minimums['modifiers'] ?? 1)) {
                $modifierErrors[] = sprintf('At least %d modifiers are required.', (int) ($minimums['modifiers'] ?? 1));
            }
            foreach ($draft->modifiers as $modifier) {
                if (! in_array($modifier->modifier_type, $allowedModifierTypes, true)) {
                    $modifierErrors[] = sprintf('Modifier "%s" uses a type that is not enabled.', $modifier->name);
                }
                if ($modifier->items->isEmpty()) {
                    $modifierErrors[] = sprintf('Modifier "%s" must be attached to at least one compatible item.', $modifier->name);
                }
            }
        }

        $reviewErrors = array_values(array_merge(
            $foundationErrors,
            $structureErrors,
            $archetypeErrors,
            $categoryErrors,
            $itemErrors,
            $modifierErrors,
        ));

        return [
            'foundation' => $this->stepPayload(
                $foundationErrors,
                [],
                $this->hasAnyData([$draft->name, $draft->slug, $draft->description]),
                [
                    'name' => $draft->name,
                    'slug' => $draft->slug,
                    'visibility' => $draft->visibility,
                ],
            ),
            'structure' => $this->stepPayload(
                $structureErrors,
                [],
                $allowedItemSlots !== [] || $allowedModifierTypes !== [],
                [
                    'supports_modifiers' => $supportsModifiers,
                    'supports_archetypes' => $supportsArchetypes,
                    'allowed_item_slots' => $allowedItemSlots,
                    'allowed_modifier_types' => $allowedModifierTypes,
                ],
            ),
            'archetypes' => $this->stepPayload(
                $archetypeErrors,
                [],
                $draft->archetypes->isNotEmpty(),
                ['count' => $draft->archetypes->count()],
            ),
            'categories' => $this->stepPayload(
                $categoryErrors,
                [],
                $draft->categories->isNotEmpty(),
                ['count' => $draft->categories->count()],
            ),
            'items' => $this->stepPayload(
                $itemErrors,
                [],
                $draft->items->isNotEmpty(),
                ['count' => $draft->items->count()],
            ),
            'modifiers' => $this->stepPayload(
                $modifierErrors,
                [],
                $draft->modifiers->isNotEmpty() || $supportsModifiers === false,
                ['count' => $draft->modifiers->count()],
            ),
            'review' => $this->stepPayload(
                $reviewErrors,
                [],
                $this->hasAnyData([$draft->description, $allowedItemSlots, $draft->items->count(), $draft->modifiers->count()]),
                [
                    'blocking_errors' => count($reviewErrors),
                    'category' => $draft->category_key,
                    'published_game_id' => $draft->published_game_id,
                ],
            ),
        ];
    }

    /**
     * @param  array<int, string>  $errors
     * @param  array<int, string>  $warnings
     * @param  array<string, mixed>  $snapshot
     * @return array{status:string,errors:array<int,string>,warnings:array<int,string>,snapshot:array<string,mixed>}
     */
    private function stepPayload(array $errors, array $warnings, bool $hasData, array $snapshot): array
    {
        return [
            'status' => $errors === []
                ? 'complete'
                : ($hasData ? 'in_progress' : 'not_started'),
            'errors' => array_values(array_unique($errors)),
            'warnings' => array_values(array_unique($warnings)),
            'snapshot' => $snapshot,
        ];
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function hasAnyData(array $values): bool
    {
        foreach ($values as $value) {
            if (is_array($value) && $value !== []) {
                return true;
            }

            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }
}
