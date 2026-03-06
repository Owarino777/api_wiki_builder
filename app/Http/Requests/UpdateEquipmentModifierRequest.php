<?php

namespace App\Http\Requests;

class UpdateEquipmentModifierRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'name' => ['sometimes', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'external_ref' => ['nullable', 'string', 'max:160'],
            'modifier_type' => ['sometimes', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'description' => ['nullable', 'string'],
            'delta_score' => ['sometimes', 'integer'],
            'delta_attack' => ['nullable', 'integer'],
            'delta_defense' => ['nullable', 'integer'],
            'delta_utility' => ['nullable', 'integer'],
            'delta_accuracy' => ['nullable', 'integer'],
            'delta_mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
