<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\GameDraft;
use App\Models\User;
use App\Support\RequestUserHeader;
use Illuminate\Http\Request;

abstract class OwnedGameDraftController extends Controller
{
    protected function requestUser(Request $request): ?User
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

    protected function resolveDraft(Request $request, int $draftId): ?GameDraft
    {
        $user = $this->requestUser($request);
        if ($user === null) {
            return null;
        }

        return GameDraft::query()
            ->when($user->role !== UserRole::Admin, fn ($query) => $query->where('owner_user_id', $user->id))
            ->find($draftId);
    }
}
