<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'user_id' => $this->user_id,
            'archetype_id' => $this->archetype_id,
            'title' => $this->title,
            'description' => $this->description,
            'share_token' => $this->share_token,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'slot' => $item->slot,
                    'position' => $item->pivot?->position,
                    'item_slots' => $item->relationLoaded('itemSlots')
                        ? EquipmentItemSlotResource::collection($item->itemSlots)
                        : [],
                ])->values();
            }),
            'slots' => $this->slot_assignments ?? [],
        ];
    }
}
