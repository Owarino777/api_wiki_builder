<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $userId = $request->header('X-USER-ID');

        if (! is_string($userId) || ! ctype_digit($userId)) {
            return ApiResponse::error('Forbidden. Missing or invalid X-USER-ID header.', 403);
        }

        $user = User::query()->find((int) $userId);
        if ($user === null || $user->role !== $role) {
            return ApiResponse::error('Forbidden. Insufficient role.', 403);
        }

        $request->attributes->set('request_user', $user);

        return $next($request);
    }
}
