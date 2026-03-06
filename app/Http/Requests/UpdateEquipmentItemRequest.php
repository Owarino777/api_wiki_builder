<?php

namespace App\Http\Requests;

class UpdateEquipmentItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'name' => ['sometimes', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'external_ref' => ['nullable', 'string', 'max:160'],
            'slot' => ['sometimes', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'rarity' => ['sometimes', 'string', 'max:40'],
            'tier_level' => ['nullable', 'string', 'max:20'],
            'score' => ['sometimes', 'integer'],
            'attack' => ['nullable', 'integer'],
            'defense' => ['nullable', 'integer'],
            'utility' => ['nullable', 'integer'],
            'accuracy' => ['nullable', 'integer'],
            'mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'item_slots' => ['nullable', 'array'],
            'item_slots.*.slot_key' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'item_slots.*.slot_label' => ['required', 'string', 'max:80'],
            'item_slots.*.accepted_modifier_type' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'item_slots.*.sort_order' => ['nullable', 'integer', 'min:1', 'max:32767'],
            'item_slots.*.is_required' => ['nullable', 'boolean'],
            'item_slots.*.metadata' => ['nullable', 'array'],
        ];
    }
}
