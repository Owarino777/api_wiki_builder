<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuildProfileResource;
use App\Http\Resources\EquipmentItemResource;
use App\Http\Resources\EquipmentModifierResource;
use App\Http\Resources\GameResource;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Models\Game;
use App\Models\User;
use App\Support\ApiResponse;
use App\Support\RequestUserHeader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $games = Game::query()
            ->where('visibility', 'public')
            ->orderBy('id')
            ->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(GameResource::collection($games)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }
        if (! $this->canViewGame($request, $game)) {
            return ApiResponse::error('Forbidden. Private game.', 403);
        }

        return ApiResponse::success((new GameResource($game))->toArray($request));
    }

    public function archetypes(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }
        if (! $this->canViewGame($request, $game)) {
            return ApiResponse::error('Forbidden. Private game.', 403);
        }

        $perPage = (int) $request->query('per_page', 15);
        $archetypes = $game->archetypes()->orderBy('id')->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(BuildProfileResource::collection($archetypes)->response()->getData(true));
    }

    public function buildProfiles(Request $request, int $id)
    {
        return $this->archetypes($request, $id);
    }

    public function items(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }
        if (! $this->canViewGame($request, $game)) {
            return ApiResponse::error('Forbidden. Private game.', 403);
        }

        $query = EquipmentItem::query()
            ->with(['categories', 'itemSlots'])
            ->where('game_id', $game->id);

        $this->applyItemFilters($request, $query);

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentItemResource::collection($items)->response()->getData(true));
    }

    public function equipmentItems(Request $request, int $id)
    {
        return $this->items($request, $id);
    }

    public function modifiers(Request $request, int $id)
    {
        $game = Game::query()->find($id);
        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }
        if (! $this->canViewGame($request, $game)) {
            return ApiResponse::error('Forbidden. Private game.', 403);
        }

        $query = EquipmentModifier::query()
            ->with(['items:id,game_id,name,slug,slot,rarity,tier_level,score'])
            ->where('game_id', $game->id);

        $this->applyModifierFilters($request, $query);

        $perPage = (int) $request->query('per_page', 15);
        $modifiers = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(EquipmentModifierResource::collection($modifiers)->response()->getData(true));
    }

    public function equipmentModifiers(Request $request, int $id)
    {
        return $this->modifiers($request, $id);
    }

    public function myIndex(Request $request)
    {
        $user = $this->requestUser($request);
        if ($user === null) {
            return ApiResponse::error('Authenticated user context is required.', 401);
        }

        $query = Game::query()->orderByDesc('published_at')->orderByDesc('updated_at');

        if ($user->role !== UserRole::Admin || ! $request->boolean('all')) {
            $query->where('owner_user_id', $user->id);
        }

        $perPage = (int) $request->query('per_page', 15);
        $games = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(GameResource::collection($games)->response()->getData(true));
    }

    public function myShow(Request $request, int $id)
    {
        $user = $this->requestUser($request);
        if ($user === null) {
            return ApiResponse::error('Authenticated user context is required.', 401);
        }

        $game = Game::query()
            ->when($user->role !== UserRole::Admin, fn ($query) => $query->where('owner_user_id', $user->id))
            ->find($id);

        if ($game === null) {
            return ApiResponse::error('Game not found.', 404);
        }

        return ApiResponse::success((new GameResource($game))->toArray($request));
    }

    private function applyItemFilters(Request $request, Builder $query): void
    {
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
    }

    private function applyModifierFilters(Request $request, Builder $query): void
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

        $allowedSort = ['id', 'name', 'slug', 'modifier_type', 'delta_score', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);
    }

    private function requestUser(Request $request): ?User
    {
        $user = $request->attributes->get('request_user');

        if ($user instanceof User) {
            return $user;
        }

        $userId = RequestUserHeader::extractUserId($request);
        if ($userId === null) {
            return null;
        }

        $user = User::query()->find($userId);
        if ($user instanceof User) {
            $request->attributes->set('request_user', $user);
        }

        return $user;
    }

    private function canViewGame(Request $request, Game $game): bool
    {
        if ($game->visibility === 'public') {
            return true;
        }

        $user = $this->requestUser($request);

        return $user !== null && ($user->role === UserRole::Admin || $game->owner_user_id === $user->id);
    }
}
