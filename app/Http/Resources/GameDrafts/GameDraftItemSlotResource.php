<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftItemSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slot_key' => $this->slot_key,
            'slot_label' => $this->slot_label,
            'accepted_modifier_type' => $this->accepted_modifier_type,
            'sort_order' => $this->sort_order,
            'is_required' => $this->is_required,
            'metadata' => $this->metadata,
        ];
    }
}
