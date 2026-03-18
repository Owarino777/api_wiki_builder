<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;

class StoreGameDraftRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'category_key' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }
}
