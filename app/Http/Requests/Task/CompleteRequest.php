<?php

namespace App\Http\Requests\Task;

use Domain\Shared\Foundation\Requests\FormRequest;

class CompleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'completes' => [
                'array'
            ],
            'completes.*.notes' => [
                'nullable'
            ],
            'completes.*.images' => [
                'nullable'
            ],
            'completes.*.images.*' => [
                'max:3072'
            ]
        ];
    }
}
