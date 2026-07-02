<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'password'          => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'role'              => ['required', 'string', 'exists:roles,name'],
            'transaction_limit' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'status'            => ['required', 'in:active,inactive,suspended'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min'        => 'Password must be at least 8 characters.',
            'transaction_limit.*' => 'Enter a valid transaction limit.',
        ];
    }
}
