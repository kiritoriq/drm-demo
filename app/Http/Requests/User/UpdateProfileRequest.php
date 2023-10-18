<?php

namespace App\Http\Requests\User;

use Domain\Shared\Foundation\Requests\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'unique:users,email,' . auth()->user()->id,
            ],
            'name' => [
                'required',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'numeric',
                'max_digits:30',
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
