<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class RgpdController extends Controller
{
    /**
     * RGPD — Droit d'accès : exporter toutes les données personnelles d'un utilisateur.
     */
    public function export(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        $builds = $user->builds()->with(['equipmentItems:id', 'equipmentModifiers:id'])->get();

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role->value,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'builds' => $builds->map(fn ($build) => [
                'id' => $build->id,
                'game_id' => $build->game_id,
                'title' => $build->title,
                'description' => $build->description,
                'created_at' => $build->created_at,
            ]),
        ], 'User personal data exported.');
    }

    /**
     * RGPD — Droit à l'effacement : anonymiser les données utilisateur et dissocier les builds.
     */
    public function anonymize(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        DB::transaction(function () use ($user): void {
            // Dissocier les builds (conservation sans donnée personnelle)
            $user->builds()->update(['user_id' => null]);

            // Anonymiser les données personnelles
            $user->update([
                'username' => 'anonymous_' . $user->id,
                'email' => 'anonymous_' . $user->id . '@deleted.local',
            ]);
        });

        return ApiResponse::success(null, 'User data anonymized (RGPD right to be forgotten).');
    }

    /**
     * RGPD — Suppression complète : supprimer l'utilisateur et ses données associées.
     */
    public function destroy(int $id)
    {
        $user = User::query()->find($id);
        if ($user === null) {
            return ApiResponse::error('User not found.', 404);
        }

        DB::transaction(function () use ($user): void {
            // Dissocier les builds avant suppression
            $user->builds()->update(['user_id' => null]);
            $user->delete();
        });

        return ApiResponse::success(null, 'User deleted (RGPD right to erasure).');
    }
}
