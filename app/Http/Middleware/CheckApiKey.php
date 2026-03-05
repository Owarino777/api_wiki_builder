<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Support\ApiResponse;
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

        return $next($request);
    }
}
