<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class EquipmentCompatibilityController extends Controller
{
    public function attach(int $equipmentItemId, int $equipmentModifierId)
    {
        $item = EquipmentItem::query()->find($equipmentItemId);
        $modifier = EquipmentModifier::query()->find($equipmentModifierId);

        if ($item === null || $modifier === null) {
            return ApiResponse::error('Equipment item or modifier not found.', 404);
        }

        if ((int) $item->game_id !== (int) $modifier->game_id) {
            return ApiResponse::error('Item and modifier must belong to the same game.', 422);
        }

        DB::table('equipment_item_compatible_modifiers')->insertOrIgnore([
            'equipment_item_id' => $equipmentItemId,
            'equipment_modifier_id' => $equipmentModifierId,
        ]);

        return ApiResponse::success(null, 'Compatibility attached.', 201);
    }

    public function detach(int $equipmentItemId, int $equipmentModifierId)
    {
        DB::table('equipment_item_compatible_modifiers')
            ->where('equipment_item_id', $equipmentItemId)
            ->where('equipment_modifier_id', $equipmentModifierId)
            ->delete();

        return ApiResponse::success(null, 'Compatibility detached.');
    }
}
