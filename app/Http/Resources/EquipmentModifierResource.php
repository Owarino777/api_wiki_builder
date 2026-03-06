<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentModifierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'external_ref' => $this->external_ref,
            'modifier_type' => $this->modifier_type,
            'description' => $this->description,
            'delta_score' => $this->delta_score,
            'delta_attack' => $this->delta_attack,
            'delta_defense' => $this->delta_defense,
            'delta_utility' => $this->delta_utility,
            'delta_accuracy' => $this->delta_accuracy,
            'delta_mobility' => $this->delta_mobility,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'compatible_items' => $this->whenLoaded('items', function () {
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
