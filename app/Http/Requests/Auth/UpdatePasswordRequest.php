<?php

namespace App\Http\Requests\Auth;

use Domain\Shared\Foundation\Requests\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => [
                'required'
            ],
            'password' => [
                'required',
                'min:8',
                'max:255',
            ],
            'password_confirmation' => [
                'required',
                'same:password',
            ],
        ];
    }
}
