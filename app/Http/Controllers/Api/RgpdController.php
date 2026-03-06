<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class RgpdController extends Controller
{
    public function export(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        $builds = $user->builds()->with(['items:id,game_id,name,slug,slot'])->get();

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role->value,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'display_name' => $user->display_name,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date,
                'country_code' => $user->country_code,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'address_line_1' => $user->address_line_1,
                'address_line_2' => $user->address_line_2,
                'avatar_url' => $user->avatar_url,
                'is_anonymized' => $user->is_anonymized,
                'anonymized_at' => $user->anonymized_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'builds' => $builds->map(fn ($build) => [
                'id' => $build->id,
                'game_id' => $build->game_id,
                'archetype_id' => $build->archetype_id,
                'title' => $build->title,
                'description' => $build->description,
                'share_token' => $build->share_token,
                'is_public' => $build->is_public,
                'created_at' => $build->created_at,
            ]),
        ], 'User personal data exported.');
    }

    public function anonymize(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        DB::transaction(function () use ($user): void {
            $user->update([
                'username' => 'anonymized-user-'.$user->id,
                'email' => 'anonymized-user-'.$user->id.'@example.invalid',
                'first_name' => null,
                'last_name' => null,
                'display_name' => 'Anonymous User',
                'phone' => null,
                'birth_date' => null,
                'country_code' => null,
                'city' => null,
                'postal_code' => null,
                'address_line_1' => null,
                'address_line_2' => null,
                'avatar_url' => null,
                'is_anonymized' => true,
                'anonymized_at' => now(),
            ]);
        });

        return ApiResponse::success(null, 'User data anonymized.');
    }

    public function destroy(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        DB::transaction(function () use ($user): void {
            $user->builds()->update(['user_id' => null]);
            $user->delete();
        });

        return ApiResponse::success(null, 'User deleted.');
    }
}
