<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftModifierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_draft_id' => $this->game_draft_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'modifier_type' => $this->modifier_type,
            'description' => $this->description,
            'delta_score' => $this->delta_score,
            'delta_attack' => $this->delta_attack,
            'delta_defense' => $this->delta_defense,
            'delta_utility' => $this->delta_utility,
            'delta_accuracy' => $this->delta_accuracy,
            'delta_mobility' => $this->delta_mobility,
            'metadata' => $this->metadata,
            'compatible_item_ids' => $this->whenLoaded('items', fn () => $this->items->pluck('id')->values()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
