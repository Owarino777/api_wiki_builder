<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameStudioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyPolicyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/privacy', PrivacyPolicyController::class)->name('privacy');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::get('/account/export', [AccountController::class, 'export'])->name('account.export');
    Route::post('/account/anonymize', [AccountController::class, 'anonymize'])->name('account.anonymize');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/studio/games/create', GameStudioController::class)->name('studio.games.create');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
