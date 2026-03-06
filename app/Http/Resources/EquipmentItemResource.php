<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'external_ref' => $this->external_ref,
            'slot' => $this->slot,
            'rarity' => $this->rarity,
            'tier_level' => $this->tier_level,
            'score' => $this->score,
            'attack' => $this->attack,
            'defense' => $this->defense,
            'utility' => $this->utility,
            'accuracy' => $this->accuracy,
            'mobility' => $this->mobility,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => TagResource::collection($this->whenLoaded('categories')),
            'item_slots' => EquipmentItemSlotResource::collection($this->whenLoaded('itemSlots')),
            'compatible_modifiers' => $this->whenLoaded('modifiers', function () {
                return $this->modifiers->map(fn ($modifier) => [
                    'id' => $modifier->id,
                    'game_id' => $modifier->game_id,
                    'name' => $modifier->name,
                    'slug' => $modifier->slug,
                    'modifier_type' => $modifier->modifier_type,
                    'delta_score' => $modifier->delta_score,
                    'delta_attack' => $modifier->delta_attack,
                    'delta_defense' => $modifier->delta_defense,
                    'delta_utility' => $modifier->delta_utility,
                    'delta_accuracy' => $modifier->delta_accuracy,
                    'delta_mobility' => $modifier->delta_mobility,
                ])->values();
            }),
        ];
    }
}
