<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Http\Requests\GameDrafts\SaveGameDraftCategoryRequest;
use App\Http\Resources\GameDrafts\GameDraftCategoryResource;
use App\Support\ApiResponse;
use App\Services\GameDrafts\GameDraftProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameDraftCategoryController extends OwnedGameDraftController
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
            GameDraftCategoryResource::collection($draft->categories()->orderBy('label')->get())
        );
    }

    public function store(SaveGameDraftCategoryRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['label']);

        if ($draft->categories()->where('slug', $slug)->exists()) {
            return ApiResponse::error('A category with this slug already exists.', 422);
        }

        $category = $draft->categories()->create([
            'label' => $data['label'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        $this->progressService->refresh($draft->fresh(), 'categories');

        return ApiResponse::success((new GameDraftCategoryResource($category))->toArray($request), 'Draft category created.', 201);
    }

    public function update(SaveGameDraftCategoryRequest $request, int $id, int $categoryId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $category = $draft->categories()->find($categoryId);
        if ($category === null) {
            return ApiResponse::error('Draft category not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? Str::slug($data['label']);

        if ($draft->categories()->where('slug', $slug)->whereKeyNot($category->id)->exists()) {
            return ApiResponse::error('A category with this slug already exists.', 422);
        }

        $category->update([
            'label' => $data['label'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        $this->progressService->refresh($draft->fresh(), 'categories');

        return ApiResponse::success((new GameDraftCategoryResource($category))->toArray($request), 'Draft category updated.');
    }

    public function destroy(Request $request, int $id, int $categoryId)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $category = $draft->categories()->find($categoryId);
        if ($category === null) {
            return ApiResponse::error('Draft category not found.', 404);
        }

        $category->delete();

        $this->progressService->refresh($draft->fresh(), 'categories');

        return ApiResponse::success(null, 'Draft category deleted.');
    }
}
