<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\EquipmentModifier;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class EquipmentCompatibilityController extends Controller
{
    public function attach(int $itemId, int $modifierId)
    {
        $item = EquipmentItem::query()->find($itemId);
        $modifier = EquipmentModifier::query()->find($modifierId);

        if ($item === null || $modifier === null) {
            return ApiResponse::error('Item or modifier not found.', 404);
        }

        if ((int) $item->game_id !== (int) $modifier->game_id) {
            return ApiResponse::error('Item and modifier must belong to the same game.', 422);
        }

        DB::table('item_modifiers')->insertOrIgnore([
            'item_id' => $itemId,
            'modifier_id' => $modifierId,
        ]);

        return ApiResponse::success(null, 'Modifier compatibility attached to item.');
    }

    public function detach(int $itemId, int $modifierId)
    {
        DB::table('item_modifiers')
            ->where('item_id', $itemId)
            ->where('modifier_id', $modifierId)
            ->delete();

        return ApiResponse::success(null, 'Modifier compatibility detached from item.');
    }
}
