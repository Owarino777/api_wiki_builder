<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;

class SaveGameDraftCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string'],
        ];
    }
}
