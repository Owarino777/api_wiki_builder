<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildProfileRequest;
use App\Http\Requests\UpdateBuildProfileRequest;
use App\Http\Resources\BuildProfileResource;
use App\Models\BuildProfile;
use App\Support\ApiResponse;

class BuildProfileController extends Controller
{
    public function store(StoreBuildProfileRequest $request)
    {
        $data = $request->validated();
        $exists = BuildProfile::query()
            ->where('game_id', $data['game_id'])
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return ApiResponse::error('An archetype with this name already exists for the game.', 422);
        }

        $archetype = BuildProfile::query()->create($data);

        return ApiResponse::success((new BuildProfileResource($archetype))->toArray($request), 'Archetype created.', 201);
    }

    public function update(UpdateBuildProfileRequest $request, int $id)
    {
        $archetype = BuildProfile::query()->find($id);
        if ($archetype === null) {
            return ApiResponse::error('Archetype not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $archetype->game_id;
        $newName = $data['name'] ?? $archetype->name;

        $exists = BuildProfile::query()
            ->where('game_id', $newGameId)
            ->where('name', $newName)
            ->where('id', '!=', $archetype->id)
            ->exists();

        if ($exists) {
            return ApiResponse::error('An archetype with this name already exists for the game.', 422);
        }

        $archetype->update($data);

        return ApiResponse::success((new BuildProfileResource($archetype->fresh()))->toArray($request), 'Archetype updated.');
    }

    public function destroy(int $id)
    {
        $archetype = BuildProfile::query()->find($id);
        if ($archetype === null) {
            return ApiResponse::error('Archetype not found.', 404);
        }

        $archetype->delete();

        return ApiResponse::success(null, 'Archetype deleted.');
    }
}
