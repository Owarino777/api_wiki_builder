<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Http\Requests\GameDrafts\SaveGameDraftModifierRequest;
use App\Http\Resources\GameDrafts\GameDraftModifierResource;
use App\Support\ApiResponse;
use App\Services\GameDrafts\GameDraftProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GameDraftModifierController extends OwnedGameDraftController
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

        $modifiers = $draft->modifiers()
            ->with(['items'])
            ->orderBy('name')
            ->get();

        return ApiResponse::success(GameDraftModifierResource::collection($modifiers));
    }

    public function store(SaveGameDraftModifierRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->modifiers()->where('slug', $slug)->exists()) {
            return ApiResponse::error('A modifier with this slug already exists.', 422);
        }

        $scopeError = $this->validateModifierPayload($draft, $data);
        if ($scopeError !== null) {
            return ApiResponse::error($scopeError, 422);
        }

        $modifier = $draft->modifiers()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'modifier_type' => $data['modifier_type'],
            'description' => $data['description'] ?? null,
            'delta_score' => $data['delta_score'] ?? 0,
            'delta_attack' => $data['delta_attack'] ?? null,
            'delta_defense' => $data['delta_defense'] ?? null,
            'delta_utility' => $data['delta_utility'] ?? null,
            'delta_accuracy' => $data['delta_accuracy'] ?? null,
            'delta_mobility' => $data['delta_mobility'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        $modifier->items()->sync(Arr::wrap($data['compatible_item_ids'] ?? []));
        $this->progressService->refresh($draft->fresh(), 'modifiers');

        return ApiResponse::success((new GameDraftModifierResource($modifier->load('items')))->toArray($request), 'Draft modifier created.', 201);
    }

    public function update(SaveGameDraftModifierRequest $request, int $id, int $modifierId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $modifier = $draft->modifiers()->find($modifierId);
        if ($modifier === null) {
            return ApiResponse::error('Draft modifier not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->modifiers()->where('slug', $slug)->whereKeyNot($modifier->id)->exists()) {
            return ApiResponse::error('A modifier with this slug already exists.', 422);
        }

        $scopeError = $this->validateModifierPayload($draft, $data);
        if ($scopeError !== null) {
            return ApiResponse::error($scopeError, 422);
        }

        $modifier->update([
            'name' => $data['name'],
            'slug' => $slug,
            'modifier_type' => $data['modifier_type'],
            'description' => $data['description'] ?? null,
            'delta_score' => $data['delta_score'] ?? 0,
            'delta_attack' => $data['delta_attack'] ?? null,
            'delta_defense' => $data['delta_defense'] ?? null,
            'delta_utility' => $data['delta_utility'] ?? null,
            'delta_accuracy' => $data['delta_accuracy'] ?? null,
            'delta_mobility' => $data['delta_mobility'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        $modifier->items()->sync(Arr::wrap($data['compatible_item_ids'] ?? []));
        $this->progressService->refresh($draft->fresh(), 'modifiers');

        return ApiResponse::success((new GameDraftModifierResource($modifier->load('items')))->toArray($request), 'Draft modifier updated.');
    }

    public function destroy(Request $request, int $id, int $modifierId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $modifier = $draft->modifiers()->find($modifierId);
        if ($modifier === null) {
            return ApiResponse::error('Draft modifier not found.', 404);
        }

        $modifier->items()->detach();
        $modifier->delete();

        $this->progressService->refresh($draft->fresh(), 'modifiers');

        return ApiResponse::success(null, 'Draft modifier deleted.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateModifierPayload($draft, array $data): ?string
    {
        $settings = is_array($draft->settings) ? $draft->settings : [];
        $allowedModifierTypes = Arr::wrap($settings['allowed_modifier_types'] ?? []);
        $supportsModifiers = (bool) ($settings['supports_modifiers'] ?? true);

        if (! $supportsModifiers) {
            return 'Modifiers are disabled in the current draft structure.';
        }

        if ($allowedModifierTypes !== [] && ! in_array($data['modifier_type'], $allowedModifierTypes, true)) {
            return 'Modifier type is not enabled in the current draft structure.';
        }

        $itemIds = collect(Arr::wrap($data['compatible_item_ids'] ?? []))->map(fn ($value) => (int) $value)->filter()->values();
        if ($itemIds->isNotEmpty()
            && $draft->items()->whereIn('id', $itemIds)->count() !== $itemIds->count()) {
            return 'Every compatible item must belong to the same draft.';
        }

        return null;
    }
}
