<?php

namespace App\Http\Requests;

class StoreEquipmentItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'name' => ['required', 'string', 'max:140'],
            'slot' => ['required', 'string', 'max:60'],
            'rarity' => ['required', 'string', 'max:40'],
            'tier_level' => ['nullable', 'string', 'max:20'],
            'score' => ['required', 'integer'],
            'attack' => ['nullable', 'integer'],
            'defense' => ['nullable', 'integer'],
            'utility' => ['nullable', 'integer'],
            'accuracy' => ['nullable', 'integer'],
            'mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
