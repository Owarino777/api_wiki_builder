<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;

class EquipmentItemTagController extends Controller
{
    public function attach(int $itemId, int $categoryId)
    {
        $item = EquipmentItem::query()->find($itemId);
        $category = Tag::query()->find($categoryId);

        if ($item === null || $category === null) {
            return ApiResponse::error('Item or category not found.', 404);
        }

        DB::table('item_categories')->insertOrIgnore([
            'item_id' => $itemId,
            'category_id' => $categoryId,
        ]);

        return ApiResponse::success(null, 'Category attached to item.');
    }

    public function detach(int $itemId, int $categoryId)
    {
        DB::table('item_categories')
            ->where('item_id', $itemId)
            ->where('category_id', $categoryId)
            ->delete();

        return ApiResponse::success(null, 'Category detached from item.');
    }
}
