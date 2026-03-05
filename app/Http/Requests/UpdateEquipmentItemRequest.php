<?php

namespace App\Http\Requests;

class UpdateEquipmentItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'name' => ['sometimes', 'string', 'max:140'],
            'slot' => ['sometimes', 'string', 'max:60'],
            'rarity' => ['sometimes', 'string', 'max:40'],
            'tier_level' => ['nullable', 'string', 'max:20'],
            'score' => ['sometimes', 'integer'],
            'attack' => ['nullable', 'integer'],
            'defense' => ['nullable', 'integer'],
            'utility' => ['nullable', 'integer'],
            'accuracy' => ['nullable', 'integer'],
            'mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
