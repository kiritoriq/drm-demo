<?php

namespace App\Http\Resources\User\Auth\Contractor;

use Domain\Shared\Foundation\Resources\JsonResource;
use App\Http\Resources\User\Contractor\Resource as ContractorResource;
use Domain\Shared\User\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin  User
 */
class Resource extends ContractorResource
{
    public function with($request): array
    {
        return [
            'success' => true,
            'token' => $this->createToken('User Access Token')->plainTextToken,
        ];
    }
}
