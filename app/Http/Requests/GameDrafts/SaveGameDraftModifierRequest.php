<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;

class SaveGameDraftModifierRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:140'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'modifier_type' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string'],
            'delta_score' => ['nullable', 'integer'],
            'delta_attack' => ['nullable', 'integer'],
            'delta_defense' => ['nullable', 'integer'],
            'delta_utility' => ['nullable', 'integer'],
            'delta_accuracy' => ['nullable', 'integer'],
            'delta_mobility' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
            'compatible_item_ids' => ['nullable', 'array'],
            'compatible_item_ids.*' => ['required', 'integer', 'distinct', 'exists:game_draft_items,id'],
        ];
    }
}
