<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'token' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return back()->withErrors($validator->errors());
    }
}
