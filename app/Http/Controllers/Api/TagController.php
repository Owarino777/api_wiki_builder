<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Tag::query()
            ->with('items:id,game_id,name,slug,slot,rarity,tier_level,score')
            ->orderBy('game_id')
            ->orderBy('label');

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }

        $categories = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(TagResource::collection($categories)->response()->getData(true));
    }

    public function store(StoreTagRequest $request)
    {
        $data = $request->validated();

        $exists = Tag::query()
            ->where('game_id', $data['game_id'])
            ->where('slug', $data['slug'])
            ->exists();

        if ($exists) {
            return ApiResponse::error('A category with this slug already exists for the game.', 422);
        }

        $labelExists = Tag::query()
            ->where('game_id', $data['game_id'])
            ->where('label', $data['label'])
            ->exists();

        if ($labelExists) {
            return ApiResponse::error('A category with this label already exists for the game.', 422);
        }

        $category = Tag::query()->create($data);

        return ApiResponse::success((new TagResource($category))->toArray($request), 'Category created.', 201);
    }

    public function update(UpdateTagRequest $request, int $id)
    {
        $category = Tag::query()->find($id);
        if ($category === null) {
            return ApiResponse::error('Category not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $category->game_id;
        $newSlug = $data['slug'] ?? $category->slug;
        $newLabel = $data['label'] ?? $category->label;
        $data['slug'] = $newSlug;

        $exists = Tag::query()
            ->where('game_id', $newGameId)
            ->where('slug', $newSlug)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            return ApiResponse::error('A category with this slug already exists for the game.', 422);
        }

        $labelExists = Tag::query()
            ->where('game_id', $newGameId)
            ->where('label', $newLabel)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($labelExists) {
            return ApiResponse::error('A category with this label already exists for the game.', 422);
        }

        $category->update($data);

        return ApiResponse::success((new TagResource($category->fresh()))->toArray($request), 'Category updated.');
    }

    public function destroy(int $id)
    {
        $category = Tag::query()->find($id);
        if ($category === null) {
            return ApiResponse::error('Category not found.', 404);
        }

        $category->delete();

        return ApiResponse::success(null, 'Category deleted.');
    }
}
