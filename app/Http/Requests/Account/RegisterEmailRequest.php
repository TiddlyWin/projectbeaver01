<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }
}
