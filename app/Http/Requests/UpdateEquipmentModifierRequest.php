<?php

namespace App\Http\Requests;

class UpdateEquipmentModifierRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'name' => ['sometimes', 'string', 'max:140'],
            'modifier_type' => ['sometimes', 'string', 'max:60'],
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
