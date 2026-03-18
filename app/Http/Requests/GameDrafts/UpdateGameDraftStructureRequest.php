<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;

class UpdateGameDraftStructureRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'supports_modifiers' => ['sometimes', 'boolean'],
            'supports_archetypes' => ['sometimes', 'boolean'],
            'allowed_item_slots' => ['sometimes', 'array', 'min:1'],
            'allowed_item_slots.*' => ['required', 'string', 'distinct', 'max:60'],
            'allowed_modifier_types' => ['sometimes', 'array'],
            'allowed_modifier_types.*' => ['required', 'string', 'distinct', 'max:60'],
        ];
    }
}
