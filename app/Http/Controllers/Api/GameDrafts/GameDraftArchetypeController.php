<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Http\Requests\GameDrafts\SaveGameDraftArchetypeRequest;
use App\Http\Resources\GameDrafts\GameDraftArchetypeResource;
use App\Support\ApiResponse;
use App\Services\GameDrafts\GameDraftProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameDraftArchetypeController extends OwnedGameDraftController
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

        return ApiResponse::success(
            GameDraftArchetypeResource::collection($draft->archetypes()->orderBy('name')->get())
        );
    }

    public function store(SaveGameDraftArchetypeRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }
        if (($draft->settings['supports_archetypes'] ?? true) === false) {
            return ApiResponse::error('Archetypes are disabled in this draft structure.', 409);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->archetypes()->where('slug', $slug)->exists()) {
            return ApiResponse::error('An archetype with this slug already exists.', 422);
        }

        $archetype = $draft->archetypes()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        $this->progressService->refresh($draft->fresh(), 'archetypes');

        return ApiResponse::success((new GameDraftArchetypeResource($archetype))->toArray($request), 'Draft archetype created.', 201);
    }

    public function update(SaveGameDraftArchetypeRequest $request, int $id, int $archetypeId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }
        if (($draft->settings['supports_archetypes'] ?? true) === false) {
            return ApiResponse::error('Archetypes are disabled in this draft structure.', 409);
        }

        $archetype = $draft->archetypes()->find($archetypeId);
        if ($archetype === null) {
            return ApiResponse::error('Draft archetype not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['name']);

        if ($draft->archetypes()->where('slug', $slug)->whereKeyNot($archetype->id)->exists()) {
            return ApiResponse::error('An archetype with this slug already exists.', 422);
        }

        $archetype->update([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        $this->progressService->refresh($draft->fresh(), 'archetypes');

        return ApiResponse::success((new GameDraftArchetypeResource($archetype))->toArray($request), 'Draft archetype updated.');
    }

    public function destroy(Request $request, int $id, int $archetypeId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $archetype = $draft->archetypes()->find($archetypeId);
        if ($archetype === null) {
            return ApiResponse::error('Draft archetype not found.', 404);
        }

        $archetype->delete();

        $this->progressService->refresh($draft->fresh(), 'archetypes');

        return ApiResponse::success(null, 'Draft archetype deleted.');
    }
}
