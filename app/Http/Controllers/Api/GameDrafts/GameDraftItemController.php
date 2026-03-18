<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Http\Requests\GameDrafts\SaveGameDraftItemRequest;
use App\Http\Resources\GameDrafts\GameDraftItemResource;
use App\Models\GameDraftItem;
use App\Support\ApiResponse;
use App\Services\GameDrafts\GameDraftProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GameDraftItemController extends OwnedGameDraftController
{
    public function __construct(
        private readonly GameDraftProgressService $progressService
    ) {
    }

    public function index(Request $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $items = $draft->items()
            ->with(['categories', 'itemSlots', 'modifiers'])
            ->orderBy('name')
            ->get();

        return ApiResponse::success(GameDraftItemResource::collection($items));
    }

    public function store(SaveGameDraftItemRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->items()->where('slug', $slug)->exists()) {
            return ApiResponse::error('An item with this slug already exists.', 422);
        }

        $scopeError = $this->validateItemPayload($draft, $data);
        if ($scopeError !== null) {
            return ApiResponse::error($scopeError, 422);
        }

        $item = $draft->items()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'slot' => $data['slot'],
            'rarity' => $data['rarity'],
            'tier_level' => $data['tier_level'] ?? null,
            'score' => $data['score'] ?? 0,
            'attack' => $data['attack'] ?? null,
            'defense' => $data['defense'] ?? null,
            'utility' => $data['utility'] ?? null,
            'accuracy' => $data['accuracy'] ?? null,
            'mobility' => $data['mobility'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        $this->syncItemRelations($item, $data);
        $this->progressService->refresh($draft->fresh(), 'items');

        return ApiResponse::success((new GameDraftItemResource($item->load(['categories', 'itemSlots', 'modifiers'])))->toArray($request), 'Draft item created.', 201);
    }

    public function update(SaveGameDraftItemRequest $request, int $id, int $itemId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $item = $draft->items()->find($itemId);
        if ($item === null) {
            return ApiResponse::error('Draft item not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->items()->where('slug', $slug)->whereKeyNot($item->id)->exists()) {
            return ApiResponse::error('An item with this slug already exists.', 422);
        }

        $scopeError = $this->validateItemPayload($draft, $data);
        if ($scopeError !== null) {
            return ApiResponse::error($scopeError, 422);
        }

        $item->update([
            'name' => $data['name'],
            'slug' => $slug,
            'slot' => $data['slot'],
            'rarity' => $data['rarity'],
            'tier_level' => $data['tier_level'] ?? null,
            'score' => $data['score'] ?? 0,
            'attack' => $data['attack'] ?? null,
            'defense' => $data['defense'] ?? null,
            'utility' => $data['utility'] ?? null,
            'accuracy' => $data['accuracy'] ?? null,
            'mobility' => $data['mobility'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        $this->syncItemRelations($item, $data);
        $this->progressService->refresh($draft->fresh(), 'items');

        return ApiResponse::success((new GameDraftItemResource($item->load(['categories', 'itemSlots', 'modifiers'])))->toArray($request), 'Draft item updated.');
    }

    public function destroy(Request $request, int $id, int $itemId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $item = $draft->items()->find($itemId);
        if ($item === null) {
            return ApiResponse::error('Draft item not found.', 404);
        }

        $item->modifiers()->detach();
        $item->delete();

        $this->progressService->refresh($draft->fresh(), 'items');

        return ApiResponse::success(null, 'Draft item deleted.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateItemPayload($draft, array $data): ?string
    {
        $settings = is_array($draft->settings) ? $draft->settings : [];
        $allowedItemSlots = Arr::wrap($settings['allowed_item_slots'] ?? []);
        $allowedModifierTypes = Arr::wrap($settings['allowed_modifier_types'] ?? []);
        $supportsModifiers = (bool) ($settings['supports_modifiers'] ?? true);

        if ($allowedItemSlots !== [] && ! in_array($data['slot'], $allowedItemSlots, true)) {
            return 'Item slot is not enabled in the current draft structure.';
        }

        $categoryIds = collect(Arr::wrap($data['category_ids'] ?? []))->map(fn ($value) => (int) $value)->filter()->values();
        if ($categoryIds->isNotEmpty()
            && $draft->categories()->whereIn('id', $categoryIds)->count() !== $categoryIds->count()) {
            return 'Every category attached to an item must belong to the same draft.';
        }

        $itemSlots = Arr::wrap($data['item_slots'] ?? []);
        if (! $supportsModifiers && $itemSlots !== []) {
            return 'Attachment slots cannot be configured when modifiers are disabled in the draft structure.';
        }

        foreach ($itemSlots as $slot) {
            $modifierType = $slot['accepted_modifier_type'] ?? null;
            if ($modifierType !== null && $allowedModifierTypes !== [] && ! in_array($modifierType, $allowedModifierTypes, true)) {
                return 'Item slot uses a modifier type that is not enabled in the draft structure.';
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncItemRelations(GameDraftItem $item, array $data): void
    {
        $item->categories()->sync(Arr::wrap($data['category_ids'] ?? []));

        $item->itemSlots()->delete();
        foreach (Arr::wrap($data['item_slots'] ?? []) as $index => $slot) {
            $item->itemSlots()->create([
                'slot_key' => $slot['slot_key'],
                'slot_label' => $slot['slot_label'],
                'accepted_modifier_type' => $slot['accepted_modifier_type'] ?? null,
                'sort_order' => $slot['sort_order'] ?? ($index + 1),
                'is_required' => (bool) ($slot['is_required'] ?? false),
                'metadata' => $slot['metadata'] ?? null,
            ]);
        }
    }
}
