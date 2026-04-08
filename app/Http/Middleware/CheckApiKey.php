<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Support\ApiResponse;
use App\Support\RequestUserHeader;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('API-KEY');

        if (! is_string($key) || $key === '') {
            return ApiResponse::error('Missing API-KEY header.', 401);
        }

        $apiKey = ApiKey::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if ($apiKey === null) {
            return ApiResponse::error('Invalid or inactive API key.', 401);
        }

        $request->attributes->set('api_key', $apiKey);

        if ($apiKey->user !== null) {
            $headerUserId = RequestUserHeader::extractUserId($request);

            if ($headerUserId !== null && $headerUserId !== $apiKey->user_id) {
                return ApiResponse::error('API key does not match the provided user context.', 403);
            }

            $request->attributes->set('request_user', $apiKey->user);
        }

        return $next($request);
    }
}
