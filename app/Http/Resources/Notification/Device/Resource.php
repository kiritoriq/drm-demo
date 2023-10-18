<?php

namespace App\Http\Resources\Notification\Device;

use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'identifier' => $this->identifier
        ];
    }
}