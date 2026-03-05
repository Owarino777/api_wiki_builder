<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuildProfileResource;
use App\Http\Resources\EquipmentItemResource;
use App\Http\Resources\EquipmentModifierResource;
use App\Http\Resources\GameResource;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Models\Game;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $games = Game::query()->orderBy('id')->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(GameResource::collection($games)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }

        return ApiResponse::success((new GameResource($game))->toArray($request));
    }

    public function buildProfiles(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }

        $perPage = (int) $request->query('per_page', 15);
        $profiles = $game->buildProfiles()->orderBy('id')->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(BuildProfileResource::collection($profiles)->response()->getData(true));
    }

    public function equipmentItems(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }

        $query = EquipmentItem::query()
            ->with('tags')
            ->where('game_id', $game->id);

        $this->applyEquipmentItemFilters($request, $query);

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentItemResource::collection($items)->response()->getData(true));
    }

    public function equipmentModifiers(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }

        $query = EquipmentModifier::query()->where('game_id', $game->id);
        $this->applyEquipmentModifierFilters($request, $query);

        $perPage = (int) $request->query('per_page', 15);
        $modifiers = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentModifierResource::collection($modifiers)->response()->getData(true));
    }

    private function applyEquipmentItemFilters(Request $request, Builder $query): void
    {
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
    }

    private function applyEquipmentModifierFilters(Request $request, Builder $query): void
    {
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
    }
}
