<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_draft_id' => $this->game_draft_id,
            'name' => $this->name,
            'slug' => $this->slug,
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
            'category_ids' => $this->whenLoaded('categories', fn () => $this->categories->pluck('id')->values()),
            'modifier_ids' => $this->whenLoaded('modifiers', fn () => $this->modifiers->pluck('id')->values()),
            'categories' => $this->whenLoaded('categories', fn () => GameDraftCategoryResource::collection($this->categories)),
            'item_slots' => $this->whenLoaded('itemSlots', fn () => GameDraftItemSlotResource::collection($this->itemSlots)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
