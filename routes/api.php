<?php

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Controllers\Api\BuildController;
use App\Http\Controllers\Api\BuildProfileController;
use App\Http\Controllers\Api\EquipmentCompatibilityController;
use App\Http\Controllers\Api\EquipmentItemController;
use App\Http\Controllers\Api\EquipmentItemTagController;
use App\Http\Controllers\Api\EquipmentModifierController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\RgpdController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+');
Route::pattern('itemId', '[0-9]+');
Route::pattern('modifierId', '[0-9]+');
Route::pattern('categoryId', '[0-9]+');
Route::pattern('shareToken', '[A-Za-z0-9]+');

Route::prefix('v1')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/', ApiHomeController::class);

        Route::get('/games', [GameController::class, 'index']);
        Route::get('/games/{id}', [GameController::class, 'show']);
        Route::get('/games/{id}/archetypes', [GameController::class, 'archetypes']);
        Route::get('/games/{id}/items', [GameController::class, 'items']);
        Route::get('/games/{id}/modifiers', [GameController::class, 'modifiers']);

        Route::get('/categories', [TagController::class, 'index']);
        Route::get('/items', [EquipmentItemController::class, 'index']);
        Route::get('/items/{id}', [EquipmentItemController::class, 'show']);
        Route::get('/modifiers', [EquipmentModifierController::class, 'index']);
        Route::get('/modifiers/{id}', [EquipmentModifierController::class, 'show']);
        Route::get('/builds', [BuildController::class, 'index']);
        Route::get('/builds/{id}', [BuildController::class, 'show']);
        Route::get('/builds/{id}/summary', [BuildController::class, 'summary']);
        Route::get('/builds/share/{shareToken}', [BuildController::class, 'share']);
        Route::post('/builds/compare', [BuildController::class, 'compare']);

        Route::middleware('check.api.key')->group(function (): void {
            Route::post('/builds', [BuildController::class, 'store']);
            Route::put('/builds/{id}', [BuildController::class, 'update']);
            Route::delete('/builds/{id}', [BuildController::class, 'destroy']);

            Route::middleware('check.role:admin')->group(function (): void {
                Route::post('/archetypes', [BuildProfileController::class, 'store']);
                Route::put('/archetypes/{id}', [BuildProfileController::class, 'update']);
                Route::delete('/archetypes/{id}', [BuildProfileController::class, 'destroy']);

                Route::post('/categories', [TagController::class, 'store']);
                Route::put('/categories/{id}', [TagController::class, 'update']);
                Route::delete('/categories/{id}', [TagController::class, 'destroy']);

                Route::post('/items', [EquipmentItemController::class, 'store']);
                Route::put('/items/{id}', [EquipmentItemController::class, 'update']);
                Route::delete('/items/{id}', [EquipmentItemController::class, 'destroy']);

                Route::post('/modifiers', [EquipmentModifierController::class, 'store']);
                Route::put('/modifiers/{id}', [EquipmentModifierController::class, 'update']);
                Route::delete('/modifiers/{id}', [EquipmentModifierController::class, 'destroy']);

                Route::post('/items/{itemId}/modifiers/{modifierId}', [EquipmentCompatibilityController::class, 'attach']);
                Route::delete('/items/{itemId}/modifiers/{modifierId}', [EquipmentCompatibilityController::class, 'detach']);

                Route::post('/items/{itemId}/categories/{categoryId}', [EquipmentItemTagController::class, 'attach']);
                Route::delete('/items/{itemId}/categories/{categoryId}', [EquipmentItemTagController::class, 'detach']);

                Route::get('/users/{id}/export', [RgpdController::class, 'export']);
                Route::post('/users/{id}/anonymize', [RgpdController::class, 'anonymize']);
                Route::delete('/users/{id}', [RgpdController::class, 'destroy']);
            });
        });
    });
