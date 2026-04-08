<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserPrivacyService;
use App\Support\ApiResponse;

class RgpdController extends Controller
{
    public function __construct(private readonly UserPrivacyService $privacyService)
    {
    }

    public function export(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        return ApiResponse::success(
            $this->privacyService->export($user),
            'User personal data exported.'
        );
    }

    public function anonymize(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        $this->privacyService->anonymize($user);

        return ApiResponse::success(null, 'User data anonymized.');
    }

    public function destroy(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        $this->privacyService->delete($user);

        return ApiResponse::success(null, 'User deleted.');
    }
}
