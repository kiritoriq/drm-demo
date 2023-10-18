<?php

namespace App\Http\Requests\User;

use Domain\Shared\Foundation\Requests\FormRequest;

class UploadProfilePictureRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'profile_image' => [
                'required',
                'image',
            ],
        ];
    }
}
