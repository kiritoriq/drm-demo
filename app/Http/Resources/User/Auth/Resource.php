<?php

namespace App\Http\Resources\User\Auth;

use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'phone' => $this->phone,
            'office_address' => $this->office_address,
        ];
    }

    public function with($request): array
    {
        return [
            'success' => true,
            'token' => $this->createToken('User Access Token')->plainTextToken,
        ];
    }
}
