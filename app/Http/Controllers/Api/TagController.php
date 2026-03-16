<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'label' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['label']);

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

    public function update(Request $request, int $id)
    {
        $category = Tag::query()->find($id);
        if ($category === null) {
            return ApiResponse::error('Category not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'label' => ['sometimes', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed.', 422, $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $newGameId = $data['game_id'] ?? $category->game_id;
        $newSlug = $data['slug'] ?? ($data['label'] ?? null ? Str::slug($data['label']) : $category->slug);
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
