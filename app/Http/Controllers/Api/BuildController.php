<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompareBuildRequest;
use App\Http\Requests\StoreBuildRequest;
use App\Http\Requests\UpdateBuildRequest;
use App\Http\Resources\BuildResource;
use App\Models\Build;
use App\Services\BuildRuleService;
use App\Services\BuildSummaryService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BuildController extends Controller
{
    public function __construct(
        private readonly BuildRuleService $buildRuleService,
        private readonly BuildSummaryService $buildSummaryService
    ) {
    }

    public function index(Request $request)
    {
        $query = Build::query()->with(['items:id,game_id,name,slug,slot']);

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->filled('is_public')) {
            $query->where('is_public', filter_var($request->query('is_public'), FILTER_VALIDATE_BOOL));
        }

        $allowedSort = ['id', 'title', 'created_at', 'updated_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSort, true) ? $request->query('sort_by') : 'id';
        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 15);
        $builds = $query->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(BuildResource::collection($builds)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $build = $this->loadDetailedBuild($id);
        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        return ApiResponse::success((new BuildResource($build))->toArray($request));
    }

    public function share(Request $request, string $shareToken)
    {
        $build = Build::query()
            ->where('share_token', $shareToken)
            ->where('is_public', true)
            ->first();

        if ($build === null) {
            return ApiResponse::error('Shared build not found.', 404);
        }

        $build = $this->loadDetailedBuild($build->id);

        return ApiResponse::success((new BuildResource($build))->toArray($request));
    }

    public function store(StoreBuildRequest $request)
    {
        $data = $request->validated();
        $items = $this->normalizeItems($data['items']);
        $slots = $this->normalizeSlots($data['slots'] ?? []);

        $errors = $this->buildRuleService->validate(
            (int) $data['game_id'],
            $data['archetype_id'] ?? null,
            $items,
            $slots
        );

        if ($errors !== []) {
            return ApiResponse::error('Business rule violation.', 422, $errors);
        }

        $build = DB::transaction(function () use ($data, $items, $slots) {
            $isPublic = (bool) ($data['is_public'] ?? false);

            $build = Build::query()->create([
                'game_id' => $data['game_id'],
                'user_id' => $data['user_id'] ?? null,
                'archetype_id' => $data['archetype_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'is_public' => $isPublic,
                'share_token' => $isPublic ? ($data['share_token'] ?? $this->generateShareToken()) : null,
            ]);

            $this->syncBuildItems($build->id, $items);
            $this->syncBuildSlots($build->id, $slots);

            return $build;
        });

        $build = $this->loadDetailedBuild($build->id);

        return ApiResponse::success((new BuildResource($build))->toArray($request), 'Build created.', 201);
    }

    public function update(UpdateBuildRequest $request, int $id)
    {
        $build = Build::query()->find($id);
        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        $data = $request->validated();

        $finalGameId = (int) ($data['game_id'] ?? $build->game_id);
        $finalArchetypeId = $data['archetype_id'] ?? $build->archetype_id;
        $finalItems = array_key_exists('items', $data)
            ? $this->normalizeItems($data['items'])
            : $this->existingBuildItems($build->id);
        $finalSlots = array_key_exists('slots', $data)
            ? $this->normalizeSlots($data['slots'])
            : $this->existingBuildSlots($build->id);

        $errors = $this->buildRuleService->validate(
            $finalGameId,
            $finalArchetypeId !== null ? (int) $finalArchetypeId : null,
            $finalItems,
            $finalSlots
        );

        if ($errors !== []) {
            return ApiResponse::error('Business rule violation.', 422, $errors);
        }

        DB::transaction(function () use ($build, $data, $finalItems, $finalSlots): void {
            $isPublic = array_key_exists('is_public', $data) ? (bool) $data['is_public'] : $build->is_public;
            $shareToken = array_key_exists('share_token', $data) ? $data['share_token'] : $build->share_token;
            if ($isPublic && empty($shareToken)) {
                $shareToken = $this->generateShareToken();
            }
            if (!$isPublic) {
                $shareToken = null;
            }

            $build->update([
                'game_id' => $data['game_id'] ?? $build->game_id,
                'user_id' => array_key_exists('user_id', $data) ? $data['user_id'] : $build->user_id,
                'archetype_id' => array_key_exists('archetype_id', $data) ? $data['archetype_id'] : $build->archetype_id,
                'title' => $data['title'] ?? $build->title,
                'description' => array_key_exists('description', $data) ? $data['description'] : $build->description,
                'is_public' => $isPublic,
                'share_token' => $shareToken,
            ]);

            $this->syncBuildItems($build->id, $finalItems);
            $this->syncBuildSlots($build->id, $finalSlots);
        });

        $build = $this->loadDetailedBuild($build->id);

        return ApiResponse::success((new BuildResource($build))->toArray($request), 'Build updated.');
    }

    public function destroy(int $id)
    {
        $build = Build::query()->find($id);
        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        DB::transaction(function () use ($build): void {
            DB::table('build_slots')->where('build_id', $build->id)->delete();
            DB::table('build_items')->where('build_id', $build->id)->delete();
            $build->delete();
        });

        return ApiResponse::success(null, 'Build deleted.');
    }

    public function summary(int $id)
    {
        $build = Build::query()->find($id);
        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        return ApiResponse::success($this->buildSummaryService->summary($build));
    }

    public function compare(CompareBuildRequest $request)
    {
        $data = $request->validated();
        $left = Build::query()->find($data['left_build_id']);
        $right = Build::query()->find($data['right_build_id']);

        if ($left === null || $right === null) {
            return ApiResponse::error('One or both builds were not found.', 404);
        }

        if ((int) $left->game_id !== (int) $right->game_id) {
            return ApiResponse::error('Builds must belong to the same game to be compared.', 422);
        }

        return ApiResponse::success($this->buildSummaryService->compare($left, $right));
    }

    /**
     * @param  array<int, array{item_id:mixed, position:mixed}>  $items
     * @return array<int, array{item_id:int, position:int|null}>
     */
    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(fn (array $row) => [
                'item_id' => (int) $row['item_id'],
                'position' => isset($row['position']) ? (int) $row['position'] : null,
            ])
            ->unique('item_id')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{item_id:mixed, item_slot_id:mixed, modifier_id:mixed}>  $slots
     * @return array<int, array{item_id:int, item_slot_id:int, modifier_id:int}>
     */
    private function normalizeSlots(array $slots): array
    {
        return collect($slots)
            ->map(fn (array $row) => [
                'item_id' => (int) $row['item_id'],
                'item_slot_id' => (int) $row['item_slot_id'],
                'modifier_id' => (int) $row['modifier_id'],
            ])
            ->unique(fn (array $row) => $row['item_id'].'-'.$row['item_slot_id'])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{item_id:int, position:int|null}>
     */
    private function existingBuildItems(int $buildId): array
    {
        return DB::table('build_items')
            ->where('build_id', $buildId)
            ->orderBy('item_id')
            ->get(['item_id', 'position'])
            ->map(fn ($row) => [
                'item_id' => (int) $row->item_id,
                'position' => $row->position !== null ? (int) $row->position : null,
            ])
            ->all();
    }

    /**
     * @return array<int, array{item_id:int, item_slot_id:int, modifier_id:int}>
     */
    private function existingBuildSlots(int $buildId): array
    {
        return DB::table('build_slots')
            ->where('build_id', $buildId)
            ->orderBy('item_id')
            ->orderBy('item_slot_id')
            ->get(['item_id', 'item_slot_id', 'modifier_id'])
            ->map(fn ($row) => [
                'item_id' => (int) $row->item_id,
                'item_slot_id' => (int) $row->item_slot_id,
                'modifier_id' => (int) $row->modifier_id,
            ])
            ->all();
    }

    /**
     * @param  array<int, array{item_id:int, position:int|null}>  $rows
     */
    private function syncBuildItems(int $buildId, array $rows): void
    {
        DB::table('build_items')->where('build_id', $buildId)->delete();

        if ($rows === []) {
            return;
        }

        $payload = collect($rows)->map(fn (array $row) => [
            'build_id' => $buildId,
            'item_id' => $row['item_id'],
            'position' => $row['position'],
        ])->all();

        DB::table('build_items')->insert($payload);
    }

    /**
     * @param  array<int, array{item_id:int, item_slot_id:int, modifier_id:int}>  $rows
     */
    private function syncBuildSlots(int $buildId, array $rows): void
    {
        DB::table('build_slots')->where('build_id', $buildId)->delete();

        if ($rows === []) {
            return;
        }

        $payload = collect($rows)->map(fn (array $row) => [
            'build_id' => $buildId,
            'item_id' => $row['item_id'],
            'item_slot_id' => $row['item_slot_id'],
            'modifier_id' => $row['modifier_id'],
        ])->all();

        DB::table('build_slots')->insert($payload);
    }

    private function loadDetailedBuild(int $id): ?Build
    {
        $build = Build::query()
            ->with(['items.categories', 'items.itemSlots'])
            ->find($id);

        if ($build === null) {
            return null;
        }

        $slotAssignments = DB::table('build_slots as bs')
            ->join('item_slots as is', 'is.id', '=', 'bs.item_slot_id')
            ->join('modifiers as m', 'm.id', '=', 'bs.modifier_id')
            ->where('bs.build_id', $build->id)
            ->orderBy('bs.item_id')
            ->orderBy('is.sort_order')
            ->get([
                'bs.item_id',
                'bs.item_slot_id',
                'bs.modifier_id',
                'is.slot_key',
                'is.slot_label',
                'is.accepted_modifier_type',
                'm.name as modifier_name',
                'm.slug as modifier_slug',
                'm.modifier_type',
                'm.delta_score',
                'm.delta_attack',
                'm.delta_defense',
                'm.delta_utility',
                'm.delta_accuracy',
                'm.delta_mobility',
            ])
            ->map(fn ($row) => [
                'item_id' => (int) $row->item_id,
                'item_slot_id' => (int) $row->item_slot_id,
                'slot_key' => $row->slot_key,
                'slot_label' => $row->slot_label,
                'accepted_modifier_type' => $row->accepted_modifier_type,
                'modifier' => [
                    'id' => (int) $row->modifier_id,
                    'name' => $row->modifier_name,
                    'slug' => $row->modifier_slug,
                    'modifier_type' => $row->modifier_type,
                    'delta_score' => $row->delta_score,
                    'delta_attack' => $row->delta_attack,
                    'delta_defense' => $row->delta_defense,
                    'delta_utility' => $row->delta_utility,
                    'delta_accuracy' => $row->delta_accuracy,
                    'delta_mobility' => $row->delta_mobility,
                ],
            ])
            ->values()
            ->all();

        $build->setAttribute('slot_assignments', $slotAssignments);

        return $build;
    }

    private function generateShareToken(): string
    {
        do {
            $token = Str::random(40);
        } while (Build::query()->where('share_token', $token)->exists());

        return $token;
    }
}
