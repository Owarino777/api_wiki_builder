<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentItemSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'slot_key' => $this->slot_key,
            'slot_label' => $this->slot_label,
            'accepted_modifier_type' => $this->accepted_modifier_type,
            'sort_order' => $this->sort_order,
            'is_required' => $this->is_required,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
