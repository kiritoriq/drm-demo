<?php

namespace App\Http\Requests\Task;

use Domain\Shared\Foundation\Requests\FormRequest;

class RejectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reject_reason' => [
                'required',
                'max:255'
            ],
            'description' => [
                'nullable'
            ]
        ];
    }
}
