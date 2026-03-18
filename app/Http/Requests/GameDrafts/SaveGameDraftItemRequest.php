<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;

class SaveGameDraftItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'slot' => ['required', 'string', 'max:60'],
            'rarity' => ['required', 'string', 'max:40'],
            'tier_level' => ['nullable', 'string', 'max:20'],
            'score' => ['nullable', 'integer'],
            'attack' => ['nullable', 'integer'],
            'defense' => ['nullable', 'integer'],
            'utility' => ['nullable', 'integer'],
            'accuracy' => ['nullable', 'integer'],
            'mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['required', 'integer', 'distinct', 'exists:game_draft_categories,id'],
            'item_slots' => ['nullable', 'array'],
            'item_slots.*.slot_key' => ['required', 'string', 'distinct', 'max:60'],
            'item_slots.*.slot_label' => ['required', 'string', 'max:80'],
            'item_slots.*.accepted_modifier_type' => ['nullable', 'string', 'max:60'],
            'item_slots.*.sort_order' => ['nullable', 'integer', 'min:1', 'max:32767'],
            'item_slots.*.is_required' => ['nullable', 'boolean'],
            'item_slots.*.metadata' => ['nullable', 'array'],
        ];
    }
}
