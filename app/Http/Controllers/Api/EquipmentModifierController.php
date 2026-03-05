<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipmentModifierRequest;
use App\Http\Requests\UpdateEquipmentModifierRequest;
use App\Http\Resources\EquipmentModifierResource;
use App\Models\EquipmentModifier;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class EquipmentModifierController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentModifier::query();

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

        $allowedSort = ['id', 'name', 'modifier_type', 'delta_score', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 15);
        $modifiers = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentModifierResource::collection($modifiers)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $modifier = EquipmentModifier::query()->find($id);
        if ($modifier === null) {
            return ApiResponse::error('Equipment modifier not found.', 404);
        }

        return ApiResponse::success((new EquipmentModifierResource($modifier))->toArray($request));
    }

    public function store(StoreEquipmentModifierRequest $request)
    {
        $data = $request->validated();
        $exists = EquipmentModifier::query()
            ->where('game_id', $data['game_id'])
            ->where('name', $data['name'])
            ->exists();
        if ($exists) {
            return ApiResponse::error('An equipment modifier with this name already exists for the game.', 422);
        }

        $modifier = EquipmentModifier::query()->create($data);

        return ApiResponse::success((new EquipmentModifierResource($modifier))->toArray($request), 'Equipment modifier created.', 201);
    }

    public function update(UpdateEquipmentModifierRequest $request, int $id)
    {
        $modifier = EquipmentModifier::query()->find($id);
        if ($modifier === null) {
            return ApiResponse::error('Equipment modifier not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $modifier->game_id;
        $newName = $data['name'] ?? $modifier->name;

        $exists = EquipmentModifier::query()
            ->where('game_id', $newGameId)
            ->where('name', $newName)
            ->where('id', '!=', $modifier->id)
            ->exists();
        if ($exists) {
            return ApiResponse::error('An equipment modifier with this name already exists for the game.', 422);
        }

        $modifier->update($data);

        return ApiResponse::success((new EquipmentModifierResource($modifier->fresh()))->toArray($request), 'Equipment modifier updated.');
    }

    public function destroy(int $id)
    {
        $modifier = EquipmentModifier::query()->find($id);
        if ($modifier === null) {
            return ApiResponse::error('Equipment modifier not found.', 404);
        }

        $modifier->delete();

        return ApiResponse::success(null, 'Equipment modifier deleted.');
    }
}
