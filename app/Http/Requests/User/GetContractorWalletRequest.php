<?php

namespace App\Http\Requests\User;

use Domain\Shared\Foundation\Requests\FormRequest;

class GetContractorWalletRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_from' => [
                'nullable',
                'date:Y-m-d'
            ],
            'date_to' => [
                'nullable',
                'date:Y-m-d'
            ]
        ];
    }
}