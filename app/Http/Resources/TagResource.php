<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                    'id' => $item->id,
                    'game_id' => $item->game_id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'slot' => $item->slot,
                    'rarity' => $item->rarity,
                    'tier_level' => $item->tier_level,
                    'score' => $item->score,
                ])->values();
            }),
        ];
    }
}
