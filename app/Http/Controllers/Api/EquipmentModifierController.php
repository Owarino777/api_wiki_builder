<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipmentModifierRequest;
use App\Http\Requests\UpdateEquipmentModifierRequest;
use App\Http\Resources\EquipmentModifierResource;
use App\Models\EquipmentModifier;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentModifierController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentModifier::query()->with([
            'items:id,game_id,name,slug,slot,rarity,tier_level,score',
        ]);

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }
        if ($request->filled('modifier_type')) {
            $query->where('modifier_type', $request->query('modifier_type'));
        }
        if ($request->filled('score_min')) {
            $query->where('delta_score', '>=', (int) $request->query('score_min'));
        }
        if ($request->filled('score_max')) {
            $query->where('delta_score', '<=', (int) $request->query('score_max'));
        }

        $allowedSort = ['id', 'name', 'slug', 'modifier_type', 'delta_score', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 15);
        $modifiers = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentModifierResource::collection($modifiers)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $modifier = EquipmentModifier::query()
            ->with(['items:id,game_id,name,slug,slot,rarity,tier_level,score'])
            ->find($id);

        if ($modifier === null) {
            return ApiResponse::error('Modifier not found.', 404);
        }

        return ApiResponse::success((new EquipmentModifierResource($modifier))->toArray($request));
    }

    public function store(StoreEquipmentModifierRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $exists = EquipmentModifier::query()
            ->where('game_id', $data['game_id'])
            ->where('slug', $data['slug'])
            ->exists();

        if ($exists) {
            return ApiResponse::error('A modifier with this slug already exists for the game.', 422);
        }

        $modifier = EquipmentModifier::query()->create($data);

        return ApiResponse::success((new EquipmentModifierResource($modifier))->toArray($request), 'Modifier created.', 201);
    }

    public function update(UpdateEquipmentModifierRequest $request, int $id)
    {
        $modifier = EquipmentModifier::query()->find($id);
        if ($modifier === null) {
            return ApiResponse::error('Modifier not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $modifier->game_id;
        $newSlug = $data['slug'] ?? ($data['name'] ?? null ? Str::slug($data['name']) : $modifier->slug);
        $data['slug'] = $newSlug;

        $exists = EquipmentModifier::query()
            ->where('game_id', $newGameId)
            ->where('slug', $newSlug)
            ->where('id', '!=', $modifier->id)
            ->exists();

        if ($exists) {
            return ApiResponse::error('A modifier with this slug already exists for the game.', 422);
        }

        $modifier->update($data);

        return ApiResponse::success((new EquipmentModifierResource($modifier->fresh()))->toArray($request), 'Modifier updated.');
    }

    public function destroy(int $id)
    {
        $modifier = EquipmentModifier::query()->find($id);
        if ($modifier === null) {
            return ApiResponse::error('Modifier not found.', 404);
        }

        $modifier->delete();

        return ApiResponse::success(null, 'Modifier deleted.');
    }
}
