<?php

namespace App\Http\Requests;

class StoreBuildProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['required', 'integer', 'exists:games,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
        ];
    }
}
