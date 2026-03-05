<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class EquipmentItemTagController extends Controller
{
    public function attach(int $equipmentItemId, int $tagId)
    {
        $item = EquipmentItem::query()->find($equipmentItemId);
        $tag = Tag::query()->find($tagId);

        if ($item === null || $tag === null) {
            return ApiResponse::error('Equipment item or tag not found.', 404);
        }

        DB::table('equipment_item_tags')->insertOrIgnore([
            'equipment_item_id' => $equipmentItemId,
            'tag_id' => $tagId,
        ]);

        return ApiResponse::success(null, 'Tag attached to equipment item.', 201);
    }

    public function detach(int $equipmentItemId, int $tagId)
    {
        DB::table('equipment_item_tags')
            ->where('equipment_item_id', $equipmentItemId)
            ->where('tag_id', $tagId)
            ->delete();

        return ApiResponse::success(null, 'Tag detached from equipment item.');
    }
}
