<?php

namespace App\Http\Requests\Auth;

use Domain\Shared\Foundation\Requests\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                Rule::exists('users', 'email'),
            ],
        ];
    }
}
