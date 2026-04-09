<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;

class UpdateTagRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'integer', 'exists:games,id'],
            'label' => ['sometimes', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('label')) {
            $this->merge(['slug' => Str::slug($this->input('label'))]);
        }
    }
}
