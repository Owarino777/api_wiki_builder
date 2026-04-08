<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:80', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:190', Rule::unique('users', 'email')],
            'display_name' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'privacy_policy_acknowledged' => ['required', 'accepted'],
            'marketing_opt_in' => ['nullable', 'boolean'],
        ];
    }
}
