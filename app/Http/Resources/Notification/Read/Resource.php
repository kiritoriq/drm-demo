<?php

namespace App\Http\Resources\Notification\Read;

use App\Http\Resources\User\Contractor\Resource as ContractorResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'notification' => $this->whenLoaded(relationship: 'notification'),
            'read_at' => $this->read_at,
            'user' => new ContractorResource($this->whenLoaded(relationship: 'user')),
        ];        
    }
}