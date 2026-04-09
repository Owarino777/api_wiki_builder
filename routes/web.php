<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\GameDrafts\GameDraftArchetypeController;
use App\Http\Controllers\Api\GameDrafts\GameDraftCategoryController;
use App\Http\Controllers\Api\GameDrafts\GameDraftController;
use App\Http\Controllers\Api\GameDrafts\GameDraftItemController;
use App\Http\Controllers\Api\GameDrafts\GameDraftModifierController;
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

    Route::prefix('/studio/api/v1')
        ->middleware(['throttle:60,1', 'resolve.request.user'])
        ->group(function (): void {
            Route::get('/game-drafts', [GameDraftController::class, 'index']);
            Route::post('/game-drafts', [GameDraftController::class, 'store']);
            Route::get('/game-drafts/{id}', [GameDraftController::class, 'show']);
            Route::delete('/game-drafts/{id}', [GameDraftController::class, 'destroy']);
            Route::patch('/game-drafts/{id}/foundation', [GameDraftController::class, 'foundation']);
            Route::patch('/game-drafts/{id}/structure', [GameDraftController::class, 'structure']);
            Route::post('/game-drafts/{id}/publish', [GameDraftController::class, 'publish']);

            Route::get('/game-drafts/{id}/archetypes', [GameDraftArchetypeController::class, 'index']);
            Route::post('/game-drafts/{id}/archetypes', [GameDraftArchetypeController::class, 'store']);
            Route::put('/game-drafts/{id}/archetypes/{archetypeId}', [GameDraftArchetypeController::class, 'update']);
            Route::delete('/game-drafts/{id}/archetypes/{archetypeId}', [GameDraftArchetypeController::class, 'destroy']);

            Route::get('/game-drafts/{id}/categories', [GameDraftCategoryController::class, 'index']);
            Route::post('/game-drafts/{id}/categories', [GameDraftCategoryController::class, 'store']);
            Route::put('/game-drafts/{id}/categories/{categoryId}', [GameDraftCategoryController::class, 'update']);
            Route::delete('/game-drafts/{id}/categories/{categoryId}', [GameDraftCategoryController::class, 'destroy']);

            Route::get('/game-drafts/{id}/items', [GameDraftItemController::class, 'index']);
            Route::post('/game-drafts/{id}/items', [GameDraftItemController::class, 'store']);
            Route::put('/game-drafts/{id}/items/{itemId}', [GameDraftItemController::class, 'update']);
            Route::delete('/game-drafts/{id}/items/{itemId}', [GameDraftItemController::class, 'destroy']);

            Route::get('/game-drafts/{id}/modifiers', [GameDraftModifierController::class, 'index']);
            Route::post('/game-drafts/{id}/modifiers', [GameDraftModifierController::class, 'store']);
            Route::put('/game-drafts/{id}/modifiers/{modifierId}', [GameDraftModifierController::class, 'update']);
            Route::delete('/game-drafts/{id}/modifiers/{modifierId}', [GameDraftModifierController::class, 'destroy']);
        });
});
