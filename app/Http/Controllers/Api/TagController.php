<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 50);
        $tags = Tag::query()
            ->with(['equipmentItems:id,game_id,name,slot,rarity,tier_level,score'])
            ->orderBy('label')
            ->paginate(max(1, min($perPage, 100)));

        return ApiResponse::success(TagResource::collection($tags)->response()->getData(true));
    }
}
