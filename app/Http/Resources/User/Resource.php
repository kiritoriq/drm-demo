<?php

namespace App\Http\Resources\User;

use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin  User
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'brand_name' => $this->brand_name,
            'phone' => $this->phone,
            'office_address' => $this->office_address,
        ];
    }
}
