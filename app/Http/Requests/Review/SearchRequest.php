<?php

namespace App\Http\Requests\Review;

use Domain\Shared\Foundation\Requests\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stars' => [
                'nullable',
                'integer',
                'min:0',
                'max:5'
            ]
        ];
    }
}
