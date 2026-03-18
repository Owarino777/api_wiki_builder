<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\RequestUserHeader;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->attributes->get('request_user');

        if (! $user instanceof User) {
            $sessionUser = $request->user();
            if ($sessionUser instanceof User) {
                $user = $sessionUser;
            }
        }

        if (! $user instanceof User) {
            $userId = RequestUserHeader::extractUserId($request);
            if ($userId !== null) {
                $user = User::query()->find($userId);
            }
        }

        if (! $user instanceof User || $user->role->value !== $role) {
            return ApiResponse::error('Forbidden. Insufficient role.', 403);
        }

        $request->attributes->set('request_user', $user);

        return $next($request);
    }
}
