<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipmentItemRequest;
use App\Http\Requests\UpdateEquipmentItemRequest;
use App\Http\Resources\EquipmentItemResource;
use App\Models\EquipmentItem;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EquipmentItemController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentItem::query()->with('tags');

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }
        if ($request->filled('slot')) {
            $query->where('slot', $request->query('slot'));
        }
        if ($request->filled('rarity')) {
            $query->where('rarity', $request->query('rarity'));
        }
        if ($request->filled('score_min')) {
            $query->where('score', '>=', (int) $request->query('score_min'));
        }
        if ($request->filled('score_max')) {
            $query->where('score', '<=', (int) $request->query('score_max'));
        }
        if ($request->filled('tag')) {
            $tag = $request->query('tag');
            $query->whereHas('tags', function (Builder $tagQuery) use ($tag): void {
                $tagQuery->where('id', $tag)->orWhere('label', $tag);
            });
        }

        $allowedSort = ['id', 'name', 'score', 'slot', 'rarity', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentItemResource::collection($items)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $item = EquipmentItem::query()->with('tags')->find($id);
        if ($item === null) {
            return ApiResponse::error('Equipment item not found.', 404);
        }

        return ApiResponse::success((new EquipmentItemResource($item))->toArray($request));
    }

    public function store(StoreEquipmentItemRequest $request)
    {
        $data = $request->validated();
        $exists = EquipmentItem::query()
            ->where('game_id', $data['game_id'])
            ->where('name', $data['name'])
            ->exists();
        if ($exists) {
            return ApiResponse::error('An equipment item with this name already exists for the game.', 422);
        }

        $item = EquipmentItem::query()->create($data);

        return ApiResponse::success((new EquipmentItemResource($item))->toArray($request), 'Equipment item created.', 201);
    }

    public function update(UpdateEquipmentItemRequest $request, int $id)
    {
        $item = EquipmentItem::query()->find($id);
        if ($item === null) {
            return ApiResponse::error('Equipment item not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $item->game_id;
        $newName = $data['name'] ?? $item->name;

        $exists = EquipmentItem::query()
            ->where('game_id', $newGameId)
            ->where('name', $newName)
            ->where('id', '!=', $item->id)
            ->exists();
        if ($exists) {
            return ApiResponse::error('An equipment item with this name already exists for the game.', 422);
        }

        $item->update($data);

        return ApiResponse::success((new EquipmentItemResource($item->fresh('tags')))->toArray($request), 'Equipment item updated.');
    }

    public function destroy(int $id)
    {
        $item = EquipmentItem::query()->find($id);
        if ($item === null) {
            return ApiResponse::error('Equipment item not found.', 404);
        }

        $item->delete();

        return ApiResponse::success(null, 'Equipment item deleted.');
    }
}
