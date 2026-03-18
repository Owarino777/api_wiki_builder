<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\RequestUserHeader;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRequestUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->attributes->get('request_user');

        if (! $user instanceof User) {
            $userId = RequestUserHeader::extractUserId($request);

            if ($userId !== null) {
                $user = User::query()->find($userId);
            }

            if ($user instanceof User) {
                $request->attributes->set('request_user', $user);
            }
        }

        if (! $user instanceof User) {
            return ApiResponse::error('Authenticated user context is required.', 401);
        }

        return $next($request);
    }
}
