<?php

namespace App\Providers;

use App\Models\User;
use App\Support\RequestUserHeader;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $requestUser = $request->attributes->get('request_user');

            return Limit::perMinute(60)->by(
                ($request->user()?->id)
                ?? ($requestUser instanceof User ? $requestUser->id : null)
                ?? RequestUserHeader::extractUserId($request)
                ?? $request->ip()
            );
        });
    }
}
