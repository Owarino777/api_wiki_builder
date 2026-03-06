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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EquipmentItemController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentItem::query()->with(['categories', 'itemSlots']);

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }
        if ($request->filled('slot')) {
            $query->where('slot', $request->query('slot'));
        }
        if ($request->filled('rarity')) {
            $query->where('rarity', $request->query('rarity'));
        }
        if ($request->filled('tier_level')) {
            $query->where('tier_level', $request->query('tier_level'));
        }
        if ($request->filled('score_min')) {
            $query->where('score', '>=', (int) $request->query('score_min'));
        }
        if ($request->filled('score_max')) {
            $query->where('score', '<=', (int) $request->query('score_max'));
        }
        if ($request->filled('category')) {
            $category = $request->query('category');
            $query->whereHas('categories', function (Builder $categoryQuery) use ($category): void {
                $categoryQuery->where('id', $category)
                    ->orWhere('label', $category)
                    ->orWhere('slug', $category);
            });
        }

        $allowedSort = ['id', 'name', 'slug', 'score', 'slot', 'rarity', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentItemResource::collection($items)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $item = EquipmentItem::query()
            ->with(['categories', 'itemSlots', 'modifiers'])
            ->find($id);

        if ($item === null) {
            return ApiResponse::error('Item not found.', 404);
        }

        return ApiResponse::success((new EquipmentItemResource($item))->toArray($request));
    }

    public function store(StoreEquipmentItemRequest $request)
    {
        $data = $request->validated();
        $categoryIds = $data['category_ids'] ?? [];
        $itemSlots = $data['item_slots'] ?? [];
        unset($data['category_ids'], $data['item_slots']);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $exists = EquipmentItem::query()
            ->where('game_id', $data['game_id'])
            ->where('slug', $data['slug'])
            ->exists();

        if ($exists) {
            return ApiResponse::error('An item with this slug already exists for the game.', 422);
        }

        $item = DB::transaction(function () use ($data, $categoryIds, $itemSlots) {
            $item = EquipmentItem::query()->create($data);
            $item->categories()->sync($categoryIds);
            $this->syncItemSlots($item->id, $itemSlots);

            return $item;
        });

        $item->load(['categories', 'itemSlots', 'modifiers']);

        return ApiResponse::success((new EquipmentItemResource($item))->toArray($request), 'Item created.', 201);
    }

    public function update(UpdateEquipmentItemRequest $request, int $id)
    {
        $item = EquipmentItem::query()->find($id);
        if ($item === null) {
            return ApiResponse::error('Item not found.', 404);
        }

        $data = $request->validated();
        $categoryIdsProvided = array_key_exists('category_ids', $data);
        $itemSlotsProvided = array_key_exists('item_slots', $data);
        $categoryIds = $data['category_ids'] ?? [];
        $itemSlots = $data['item_slots'] ?? [];
        unset($data['category_ids'], $data['item_slots']);

        $newGameId = $data['game_id'] ?? $item->game_id;
        $newSlug = $data['slug'] ?? ($data['name'] ?? null ? Str::slug($data['name']) : $item->slug);
        $data['slug'] = $newSlug;

        $exists = EquipmentItem::query()
            ->where('game_id', $newGameId)
            ->where('slug', $newSlug)
            ->where('id', '!=', $item->id)
            ->exists();

        if ($exists) {
            return ApiResponse::error('An item with this slug already exists for the game.', 422);
        }

        DB::transaction(function () use ($item, $data, $categoryIdsProvided, $categoryIds, $itemSlotsProvided, $itemSlots): void {
            $item->update($data);

            if ($categoryIdsProvided) {
                $item->categories()->sync($categoryIds);
            }

            if ($itemSlotsProvided) {
                $this->syncItemSlots($item->id, $itemSlots);
            }
        });

        $item->refresh()->load(['categories', 'itemSlots', 'modifiers']);

        return ApiResponse::success((new EquipmentItemResource($item))->toArray($request), 'Item updated.');
    }

    public function destroy(int $id)
    {
        $item = EquipmentItem::query()->find($id);
        if ($item === null) {
            return ApiResponse::error('Item not found.', 404);
        }

        $item->delete();

        return ApiResponse::success(null, 'Item deleted.');
    }

    /**
     * @param  array<int, array{slot_key:string, slot_label:string, accepted_modifier_type?:string|null, sort_order?:int|null, is_required?:bool|null, metadata?:array<mixed>|null}>  $rows
     */
    private function syncItemSlots(int $itemId, array $rows): void
    {
        DB::table('item_slots')->where('item_id', $itemId)->delete();

        if ($rows === []) {
            return;
        }

        $payload = collect($rows)
            ->unique('slot_key')
            ->map(fn (array $row) => [
                'item_id' => $itemId,
                'slot_key' => $row['slot_key'],
                'slot_label' => $row['slot_label'],
                'accepted_modifier_type' => $row['accepted_modifier_type'] ?? null,
                'sort_order' => $row['sort_order'] ?? 1,
                'is_required' => $row['is_required'] ?? false,
                'metadata' => isset($row['metadata']) ? json_encode($row['metadata'], JSON_UNESCAPED_SLASHES) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        DB::table('item_slots')->insert($payload);
    }
}
