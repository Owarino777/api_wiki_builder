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
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
