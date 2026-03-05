<?php

namespace App\Http\Requests;

class CompareBuildRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'left_build_id' => ['required', 'integer', 'exists:builds,id', 'different:right_build_id'],
            'right_build_id' => ['required', 'integer', 'exists:builds,id'],
        ];
    }
}
