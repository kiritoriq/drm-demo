<?php

namespace App\Http\Requests\Auth;

use Domain\Shared\Foundation\Requests\FormRequest;
use Illuminate\Validation\Rule;

class LogoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'player_id' => [
                'nullable',
                'max:255',
                'string'
            ]
        ];
    }
}
