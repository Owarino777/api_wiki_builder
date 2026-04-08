<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserPrivacyService
{
    public const POLICY_VERSION = '2026-04-08';

    public function export(User $user): array
    {
        $user->loadMissing([
            'apiKeys:id,user_id,label,is_active,created_at,updated_at',
            'builds.items:id,game_id,name,slug,slot',
            'ownedGames:id,owner_user_id,name,slug,visibility,creation_category,published_at,created_at,updated_at',
            'gameDrafts:id,owner_user_id,name,slug,status,current_step,visibility,completion_percent,updated_at,created_at',
        ]);

        return [
            'exported_at' => now()->toIso8601String(),
            'policy_version' => self::POLICY_VERSION,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role->value,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'display_name' => $user->display_name,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date?->toDateString(),
                'country_code' => $user->country_code,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'address_line_1' => $user->address_line_1,
                'address_line_2' => $user->address_line_2,
                'avatar_url' => $user->avatar_url,
                'privacy_policy_accepted_at' => $user->privacy_policy_accepted_at?->toIso8601String(),
                'privacy_policy_version' => $user->privacy_policy_version,
                'marketing_consent_at' => $user->marketing_consent_at?->toIso8601String(),
                'last_login_at' => $user->last_login_at?->toIso8601String(),
                'is_anonymized' => $user->is_anonymized,
                'anonymized_at' => $user->anonymized_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
                'updated_at' => $user->updated_at?->toIso8601String(),
            ],
            'api_keys' => $user->apiKeys->map(fn ($apiKey) => [
                'id' => $apiKey->id,
                'label' => $apiKey->label,
                'is_active' => $apiKey->is_active,
                'created_at' => $apiKey->created_at?->toIso8601String(),
                'updated_at' => $apiKey->updated_at?->toIso8601String(),
            ])->values()->all(),
            'builds' => $user->builds->map(fn ($build) => [
                'id' => $build->id,
                'game_id' => $build->game_id,
                'archetype_id' => $build->archetype_id,
                'title' => $build->title,
                'description' => $build->description,
                'share_token' => $build->share_token,
                'is_public' => $build->is_public,
                'created_at' => $build->created_at?->toIso8601String(),
                'updated_at' => $build->updated_at?->toIso8601String(),
                'items' => $build->items->map(fn ($item) => [
                    'id' => $item->id,
                    'game_id' => $item->game_id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'slot' => $item->slot,
                ])->values()->all(),
            ])->values()->all(),
            'owned_games' => $user->ownedGames->map(fn ($game) => [
                'id' => $game->id,
                'name' => $game->name,
                'slug' => $game->slug,
                'visibility' => $game->visibility,
                'creation_category' => $game->creation_category,
                'published_at' => $game->published_at?->toIso8601String(),
                'created_at' => $game->created_at?->toIso8601String(),
                'updated_at' => $game->updated_at?->toIso8601String(),
            ])->values()->all(),
            'drafts' => $user->gameDrafts->map(fn ($draft) => [
                'id' => $draft->id,
                'name' => $draft->name,
                'slug' => $draft->slug,
                'status' => $draft->status,
                'current_step' => $draft->current_step,
                'visibility' => $draft->visibility,
                'completion_percent' => $draft->completion_percent,
                'created_at' => $draft->created_at?->toIso8601String(),
                'updated_at' => $draft->updated_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    public function anonymize(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->apiKeys()->update(['is_active' => false]);
            $user->builds()->update(['user_id' => null]);
            $user->ownedGames()->update(['owner_user_id' => null]);
            $user->gameDrafts()->delete();

            $user->update([
                'username' => 'anonymized-user-'.$user->id,
                'email' => 'anonymized-user-'.$user->id.'@example.invalid',
                'password' => null,
                'first_name' => null,
                'last_name' => null,
                'display_name' => 'Utilisateur anonymise',
                'phone' => null,
                'birth_date' => null,
                'country_code' => null,
                'city' => null,
                'postal_code' => null,
                'address_line_1' => null,
                'address_line_2' => null,
                'avatar_url' => null,
                'remember_token' => null,
                'marketing_consent_at' => null,
                'is_anonymized' => true,
                'anonymized_at' => now(),
            ]);
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->apiKeys()->delete();
            $user->builds()->update(['user_id' => null]);
            $user->ownedGames()->update(['owner_user_id' => null]);
            $user->delete();
        });
    }
}
