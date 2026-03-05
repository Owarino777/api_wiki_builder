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
            'build_profile_id' => $this->build_profile_id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'equipment_items' => $this->whenLoaded('equipmentItems', function () {
                return $this->equipmentItems->map(fn ($item) => [
                    'equipment_item_id' => $item->id,
                    'position' => $item->pivot?->position,
                ])->values();
            }),
            'equipment_modifiers' => $this->whenLoaded('equipmentModifiers', function () {
                return $this->equipmentModifiers->map(fn ($modifier) => [
                    'equipment_modifier_id' => $modifier->id,
                    'equipment_item_id' => $modifier->pivot?->equipment_item_id,
                ])->values();
            }),
        ];
    }
}
