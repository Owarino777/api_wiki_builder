<?php

namespace App\Http\Requests;

class UpdateBuildRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'build_profile_id' => ['nullable', 'integer', 'exists:build_profiles,id'],
            'title' => ['sometimes', 'string', 'max:140'],
            'description' => ['nullable', 'string'],
            'equipment_items' => ['sometimes', 'array', 'min:1'],
            'equipment_items.*.equipment_item_id' => ['required', 'integer', 'distinct', 'exists:equipment_items,id'],
            'equipment_items.*.position' => ['nullable', 'integer', 'min:1', 'max:32767'],
            'equipment_modifiers' => ['sometimes', 'array'],
            'equipment_modifiers.*.equipment_item_id' => ['required', 'integer', 'exists:equipment_items,id'],
            'equipment_modifiers.*.equipment_modifier_id' => ['required', 'integer', 'exists:equipment_modifiers,id'],
        ];
    }
}
