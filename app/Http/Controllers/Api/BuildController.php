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

class BuildController extends Controller
{
    public function __construct(
        private readonly BuildRuleService $buildRuleService,
        private readonly BuildSummaryService $buildSummaryService
    ) {
    }

    public function index(Request $request)
    {
        $query = Build::query()->with(['equipmentItems:id', 'equipmentModifiers:id']);

        if ($request->filled('game_id')) {
            $query->where('game_id', (int) $request->query('game_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
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
        $build = Build::query()
            ->with(['equipmentItems:id', 'equipmentModifiers:id'])
            ->find($id);

        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        return ApiResponse::success((new BuildResource($build))->toArray($request));
    }

    public function store(StoreBuildRequest $request)
    {
        $data = $request->validated();
        $equipmentItems = $this->normalizeItems($data['equipment_items']);
        $equipmentModifiers = $this->normalizeModifiers($data['equipment_modifiers'] ?? []);

        $errors = $this->buildRuleService->validate(
            (int) $data['game_id'],
            $data['build_profile_id'] ?? null,
            $equipmentItems,
            $equipmentModifiers
        );
        if ($errors !== []) {
            return ApiResponse::error('Business rule violation.', 422, $errors);
        }

        $build = DB::transaction(function () use ($data, $equipmentItems, $equipmentModifiers) {
            $build = Build::query()->create([
                'game_id' => $data['game_id'],
                'user_id' => $data['user_id'] ?? null,
                'build_profile_id' => $data['build_profile_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $this->syncBuildItems($build->id, $equipmentItems);
            $this->syncBuildModifiers($build->id, $equipmentModifiers);

            return $build;
        });

        $build->load(['equipmentItems:id', 'equipmentModifiers:id']);

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
        $finalBuildProfileId = $data['build_profile_id'] ?? $build->build_profile_id;
        $finalItems = array_key_exists('equipment_items', $data)
            ? $this->normalizeItems($data['equipment_items'])
            : $this->existingBuildItems($build->id);
        $finalModifiers = array_key_exists('equipment_modifiers', $data)
            ? $this->normalizeModifiers($data['equipment_modifiers'])
            : $this->existingBuildModifiers($build->id);

        $errors = $this->buildRuleService->validate(
            $finalGameId,
            $finalBuildProfileId !== null ? (int) $finalBuildProfileId : null,
            $finalItems,
            $finalModifiers
        );
        if ($errors !== []) {
            return ApiResponse::error('Business rule violation.', 422, $errors);
        }

        DB::transaction(function () use ($build, $data, $finalItems, $finalModifiers): void {
            $build->update([
                'game_id' => $data['game_id'] ?? $build->game_id,
                'user_id' => array_key_exists('user_id', $data) ? $data['user_id'] : $build->user_id,
                'build_profile_id' => array_key_exists('build_profile_id', $data) ? $data['build_profile_id'] : $build->build_profile_id,
                'title' => $data['title'] ?? $build->title,
                'description' => array_key_exists('description', $data) ? $data['description'] : $build->description,
            ]);

            $this->syncBuildItems($build->id, $finalItems);
            $this->syncBuildModifiers($build->id, $finalModifiers);
        });

        $build->refresh()->load(['equipmentItems:id', 'equipmentModifiers:id']);

        return ApiResponse::success((new BuildResource($build))->toArray($request), 'Build updated.');
    }

    public function destroy(int $id)
    {
        $build = Build::query()->find($id);
        if ($build === null) {
            return ApiResponse::error('Build not found.', 404);
        }

        DB::transaction(function () use ($build): void {
            DB::table('build_equipment_modifiers')->where('build_id', $build->id)->delete();
            DB::table('build_equipment_items')->where('build_id', $build->id)->delete();
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

        return ApiResponse::success($this->buildSummaryService->compare($left, $right));
    }

    /**
     * @param  array<int, array{equipment_item_id:mixed, position:mixed}>  $items
     * @return array<int, array{equipment_item_id:int, position:int|null}>
     */
    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(fn (array $row) => [
                'equipment_item_id' => (int) $row['equipment_item_id'],
                'position' => isset($row['position']) ? (int) $row['position'] : null,
            ])
            ->unique('equipment_item_id')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{equipment_item_id:mixed, equipment_modifier_id:mixed}>  $modifiers
     * @return array<int, array{equipment_item_id:int, equipment_modifier_id:int}>
     */
    private function normalizeModifiers(array $modifiers): array
    {
        return collect($modifiers)
            ->map(fn (array $row) => [
                'equipment_item_id' => (int) $row['equipment_item_id'],
                'equipment_modifier_id' => (int) $row['equipment_modifier_id'],
            ])
            ->unique(fn (array $row) => $row['equipment_item_id'].'-'.$row['equipment_modifier_id'])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{equipment_item_id:int, position:int|null}>
     */
    private function existingBuildItems(int $buildId): array
    {
        return DB::table('build_equipment_items')
            ->where('build_id', $buildId)
            ->orderBy('equipment_item_id')
            ->get(['equipment_item_id', 'position'])
            ->map(fn ($row) => [
                'equipment_item_id' => (int) $row->equipment_item_id,
                'position' => $row->position !== null ? (int) $row->position : null,
            ])
            ->all();
    }

    /**
     * @return array<int, array{equipment_item_id:int, equipment_modifier_id:int}>
     */
    private function existingBuildModifiers(int $buildId): array
    {
        return DB::table('build_equipment_modifiers')
            ->where('build_id', $buildId)
            ->orderBy('equipment_item_id')
            ->orderBy('equipment_modifier_id')
            ->get(['equipment_item_id', 'equipment_modifier_id'])
            ->map(fn ($row) => [
                'equipment_item_id' => (int) $row->equipment_item_id,
                'equipment_modifier_id' => (int) $row->equipment_modifier_id,
            ])
            ->all();
    }

    /**
     * @param  array<int, array{equipment_item_id:int, position:int|null}>  $rows
     */
    private function syncBuildItems(int $buildId, array $rows): void
    {
        DB::table('build_equipment_items')->where('build_id', $buildId)->delete();

        if ($rows === []) {
            return;
        }

        $payload = collect($rows)->map(fn (array $row) => [
            'build_id' => $buildId,
            'equipment_item_id' => $row['equipment_item_id'],
            'position' => $row['position'],
        ])->all();

        DB::table('build_equipment_items')->insert($payload);
    }

    /**
     * @param  array<int, array{equipment_item_id:int, equipment_modifier_id:int}>  $rows
     */
    private function syncBuildModifiers(int $buildId, array $rows): void
    {
        DB::table('build_equipment_modifiers')->where('build_id', $buildId)->delete();

        if ($rows === []) {
            return;
        }

        $payload = collect($rows)->map(fn (array $row) => [
            'build_id' => $buildId,
            'equipment_item_id' => $row['equipment_item_id'],
            'equipment_modifier_id' => $row['equipment_modifier_id'],
        ])->all();

        DB::table('build_equipment_modifiers')->insert($payload);
    }
}
