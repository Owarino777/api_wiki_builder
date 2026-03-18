<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\RequestUserHeader;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveRequestUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            $userId = RequestUserHeader::extractUserId($request);

            if ($userId !== null) {
                $user = User::query()->find($userId);
            }
        }

        if ($user !== null) {
            $request->attributes->set('request_user', $user);
        }

        return $next($request);
    }
}
