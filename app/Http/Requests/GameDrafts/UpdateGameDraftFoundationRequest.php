<?php

namespace App\Http\Requests\GameDrafts;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateGameDraftFoundationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'slug' => ['sometimes', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['sometimes', 'nullable', 'string'],
            'visibility' => ['sometimes', 'string', Rule::in(['private', 'public'])],
        ];
    }
}
