<?php

namespace App\Http\Requests;

class UpdateBuildProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'name' => ['sometimes', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
        ];
    }
}
