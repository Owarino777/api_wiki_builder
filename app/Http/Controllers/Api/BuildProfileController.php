<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildProfileRequest;
use App\Http\Requests\UpdateBuildProfileRequest;
use App\Http\Resources\BuildProfileResource;
use App\Models\BuildProfile;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

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
            return ApiResponse::error('A build profile with this name already exists for the game.', 422);
        }

        $buildProfile = BuildProfile::query()->create($data);

        return ApiResponse::success((new BuildProfileResource($buildProfile))->toArray($request), 'Build profile created.', 201);
    }

    public function update(UpdateBuildProfileRequest $request, int $id)
    {
        $buildProfile = BuildProfile::query()->find($id);
        if ($buildProfile === null) {
            return ApiResponse::error('Build profile not found.', 404);
        }

        $data = $request->validated();
        $newGameId = $data['game_id'] ?? $buildProfile->game_id;
        $newName = $data['name'] ?? $buildProfile->name;

        $exists = BuildProfile::query()
            ->where('game_id', $newGameId)
            ->where('name', $newName)
            ->where('id', '!=', $buildProfile->id)
            ->exists();
        if ($exists) {
            return ApiResponse::error('A build profile with this name already exists for the game.', 422);
        }

        $buildProfile->update($data);

        return ApiResponse::success((new BuildProfileResource($buildProfile->fresh()))->toArray($request), 'Build profile updated.');
    }

    public function destroy(int $id)
    {
        $buildProfile = BuildProfile::query()->find($id);
        if ($buildProfile === null) {
            return ApiResponse::error('Build profile not found.', 404);
        }

        $buildProfile->delete();

        return ApiResponse::success(null, 'Build profile deleted.');
    }
}
