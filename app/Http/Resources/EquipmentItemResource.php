<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $metadata = $this->metadata;

        // Strip heavy nested data the frontend doesn't need
        if (is_array($metadata)) {
            $slim = [];
            if (isset($metadata['source']['image_url'])) {
                $slim['source']['image_url'] = $metadata['source']['image_url'];
            }
            if (isset($metadata['bo3']['weapon_class'])) {
                $slim['bo3']['weapon_class'] = $metadata['bo3']['weapon_class'];
            }
            if (isset($metadata['bo7']['category'])) {
                $slim['bo7']['category'] = $metadata['bo7']['category'];
            }
            $metadata = $slim ?: null;
        }

        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
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
            'metadata' => $metadata,
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
