<?php

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
Route::pattern('equipmentItemId', '[0-9]+');
Route::pattern('equipmentModifierId', '[0-9]+');
Route::pattern('tagId', '[0-9]+');

Route::prefix('v1')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/games', [GameController::class, 'index']);
        Route::get('/games/{id}', [GameController::class, 'show']);
        Route::get('/games/{id}/build-profiles', [GameController::class, 'buildProfiles']);
        Route::get('/games/{id}/equipment-items', [GameController::class, 'equipmentItems']);
        Route::get('/games/{id}/equipment-modifiers', [GameController::class, 'equipmentModifiers']);
        Route::get('/tags', [TagController::class, 'index']);
        Route::get('/equipment-items', [EquipmentItemController::class, 'index']);
        Route::get('/equipment-items/{id}', [EquipmentItemController::class, 'show']);
        Route::get('/equipment-modifiers', [EquipmentModifierController::class, 'index']);
        Route::get('/equipment-modifiers/{id}', [EquipmentModifierController::class, 'show']);
        Route::get('/builds/{id}', [BuildController::class, 'show']);
        Route::get('/builds/{id}/summary', [BuildController::class, 'summary']);
        Route::post('/builds/compare', [BuildController::class, 'compare']);

        Route::middleware('check.api.key')->group(function (): void {
            Route::post('/builds', [BuildController::class, 'store']);
            Route::put('/builds/{id}', [BuildController::class, 'update']);
            Route::delete('/builds/{id}', [BuildController::class, 'destroy']);

            Route::middleware('check.role:admin')->group(function (): void {
                Route::post('/build-profiles', [BuildProfileController::class, 'store']);
                Route::put('/build-profiles/{id}', [BuildProfileController::class, 'update']);
                Route::delete('/build-profiles/{id}', [BuildProfileController::class, 'destroy']);

                Route::post('/equipment-items', [EquipmentItemController::class, 'store']);
                Route::put('/equipment-items/{id}', [EquipmentItemController::class, 'update']);
                Route::delete('/equipment-items/{id}', [EquipmentItemController::class, 'destroy']);

                Route::post('/equipment-modifiers', [EquipmentModifierController::class, 'store']);
                Route::put('/equipment-modifiers/{id}', [EquipmentModifierController::class, 'update']);
                Route::delete('/equipment-modifiers/{id}', [EquipmentModifierController::class, 'destroy']);

                Route::post(
                    '/equipment-items/{equipmentItemId}/compatible-modifiers/{equipmentModifierId}',
                    [EquipmentCompatibilityController::class, 'attach']
                );
                Route::delete(
                    '/equipment-items/{equipmentItemId}/compatible-modifiers/{equipmentModifierId}',
                    [EquipmentCompatibilityController::class, 'detach']
                );

                Route::post('/equipment-items/{equipmentItemId}/tags/{tagId}', [EquipmentItemTagController::class, 'attach']);
                Route::delete('/equipment-items/{equipmentItemId}/tags/{tagId}', [EquipmentItemTagController::class, 'detach']);

                // RGPD — Admin endpoints
                Route::get('/users/{id}/export', [RgpdController::class, 'export']);
                Route::post('/users/{id}/anonymize', [RgpdController::class, 'anonymize']);
                Route::delete('/users/{id}', [RgpdController::class, 'destroy']);
            });
        });
    });
