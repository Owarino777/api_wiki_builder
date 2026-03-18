<?php

namespace App\Http\Requests;

class UpdateBuildRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'archetype_id' => ['nullable', 'integer', 'exists:archetypes,id'],
            'title' => ['sometimes', 'string', 'max:140'],
            'description' => ['nullable', 'string'],
            'share_token' => ['nullable', 'string', 'max:64'],
            'is_public' => ['nullable', 'boolean'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'items.*.position' => ['nullable', 'integer', 'min:1', 'max:32767'],
            'slots' => ['sometimes', 'array'],
            'slots.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'slots.*.item_slot_id' => ['required', 'integer', 'exists:item_slots,id'],
            'slots.*.modifier_id' => ['required', 'integer', 'exists:modifiers,id'],
        ];
    }
}
