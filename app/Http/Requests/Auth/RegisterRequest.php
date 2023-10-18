<?php

namespace App\Http\Requests\Auth;

use Domain\Shared\Foundation\Requests\FormRequest;
use Domain\Shared\User\Enums\VendorType;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'min:8',
            ],
            'password_confirmation' => [
                'required',
                'same:password',
            ],
            'contact_number' => [
                'nullable',
                'numeric'
            ],
            'whatsapp_number' => [
                'nullable',
                'numeric'
            ],
            'vendor_type' => [
                'required',
                'string',
                Rule::in(array_map(fn (VendorType $vendorType) => $vendorType->value, VendorType::cases()))
            ],
            'business_logo' => [
                'nullable',
                'image'
            ],
            'business_name' => [
                'nullable'
            ],
            'business_description' => [
                'nullable'
            ],
            'business_address' => [
                'nullable'
            ],
            'services' => [
                'array'
            ],
            'location_coverages' => [
                'array'
            ]
        ];
    }
}
