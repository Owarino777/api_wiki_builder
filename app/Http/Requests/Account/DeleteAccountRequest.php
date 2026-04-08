<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'delete_password' => ['required', 'string', 'current_password:web'],
            'confirm_delete' => ['required', 'accepted'],
        ];
    }
}
