<?php

namespace App\Http\Requests;

class StoreEquipmentModifierRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'name' => ['required', 'string', 'max:140'],
            'modifier_type' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string'],
            'delta_score' => ['required', 'integer'],
            'delta_attack' => ['nullable', 'integer'],
            'delta_defense' => ['nullable', 'integer'],
            'delta_utility' => ['nullable', 'integer'],
            'delta_accuracy' => ['nullable', 'integer'],
            'delta_mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
