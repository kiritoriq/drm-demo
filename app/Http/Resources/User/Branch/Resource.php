<?php

namespace App\Http\Resources\User\Branch;

use App\Http\Resources\User\Resource as UserResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\Branch;
use Illuminate\Http\Request;

/**
 * @mixin  Branch
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'assets' => Asset\Resource::collection($this->whenLoaded('assets'))
        ];
    }
}
