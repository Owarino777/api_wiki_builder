<?php

namespace App\Services\GameDrafts;

use App\Models\BuildProfile;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Models\Game;
use App\Models\GameDraft;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class GameDraftPublicationService
{
    public function __construct(
        private readonly GameDraftProgressService $progressService,
        private readonly GameCategoryTemplateRegistry $templateRegistry
    ) {
    }

    public function publish(GameDraft $draft): Game
    {
        $draft->loadMissing([
            'archetypes',
            'categories',
            'items.categories',
            'items.itemSlots',
            'modifiers.items',
        ]);

        $errors = $this->progressService->publishErrors($draft);
        if ($errors !== []) {
            throw new \RuntimeException(implode(' ', $errors));
        }

        $template = $this->templateRegistry->find($draft->category_key);

        return DB::transaction(function () use ($draft, $template): Game {
            $locked = GameDraft::query()->lockForUpdate()->find($draft->id);

            if ($locked->published_game_id !== null) {
                throw new \RuntimeException('This draft has already been published.');
            }
            $now = now();

            $game = Game::query()->create([
                'owner_user_id' => $draft->owner_user_id,
                'name' => $draft->name,
                'slug' => $draft->slug,
                'genre' => $template['label'] ?? strtoupper($draft->category_key),
                'creation_category' => $draft->category_key,
                'source_type' => 'user',
                'visibility' => $draft->visibility,
                'published_at' => $now,
            ]);

            foreach ($draft->archetypes as $archetype) {
                BuildProfile::query()->create([
                    'game_id' => $game->id,
                    'name' => $archetype->name,
                    'description' => $archetype->description,
                ]);
            }

            $categoryMap = [];
            foreach ($draft->categories as $category) {
                $runtimeCategory = Tag::query()->create([
                    'game_id' => $game->id,
                    'label' => $category->label,
                    'slug' => $category->slug,
                    'source_type' => 'user',
                ]);

                $categoryMap[$category->id] = $runtimeCategory->id;
            }

            $itemMap = [];
            foreach ($draft->items as $item) {
                $runtimeItem = EquipmentItem::query()->create([
                    'game_id' => $game->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'external_ref' => null,
                    'slot' => $item->slot,
                    'rarity' => $item->rarity,
                    'tier_level' => $item->tier_level,
                    'score' => $item->score,
                    'attack' => $item->attack,
                    'defense' => $item->defense,
                    'utility' => $item->utility,
                    'accuracy' => $item->accuracy,
                    'mobility' => $item->mobility,
                    'metadata' => $item->metadata,
                ]);

                $itemMap[$item->id] = $runtimeItem->id;

                foreach ($item->itemSlots as $slot) {
                    $runtimeItem->itemSlots()->create([
                        'slot_key' => $slot->slot_key,
                        'slot_label' => $slot->slot_label,
                        'accepted_modifier_type' => $slot->accepted_modifier_type,
                        'sort_order' => $slot->sort_order,
                        'is_required' => $slot->is_required,
                        'metadata' => $slot->metadata,
                    ]);
                }

                if ($item->categories->isNotEmpty()) {
                    $runtimeItem->categories()->sync(
                        $item->categories
                            ->pluck('id')
                            ->map(fn (int $categoryId) => $categoryMap[$categoryId] ?? null)
                            ->filter()
                            ->values()
                            ->all()
                    );
                }
            }

            foreach ($draft->modifiers as $modifier) {
                $runtimeModifier = EquipmentModifier::query()->create([
                    'game_id' => $game->id,
                    'name' => $modifier->name,
                    'slug' => $modifier->slug,
                    'external_ref' => null,
                    'modifier_type' => $modifier->modifier_type,
                    'description' => $modifier->description,
                    'delta_score' => $modifier->delta_score,
                    'delta_attack' => $modifier->delta_attack,
                    'delta_defense' => $modifier->delta_defense,
                    'delta_utility' => $modifier->delta_utility,
                    'delta_accuracy' => $modifier->delta_accuracy,
                    'delta_mobility' => $modifier->delta_mobility,
                    'metadata' => $modifier->metadata,
                ]);

                $runtimeModifier->items()->sync(
                    $modifier->items
                        ->pluck('id')
                        ->map(fn (int $itemId) => $itemMap[$itemId] ?? null)
                        ->filter()
                        ->values()
                        ->all()
                );
            }

            $locked->forceFill([
                'published_game_id' => $game->id,
                'published_at' => $now,
                'status' => 'published',
                'current_step' => 'review',
                'completion_percent' => 100,
            ])->save();

            return $game;
        });
    }
}
