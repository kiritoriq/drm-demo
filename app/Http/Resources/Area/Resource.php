<?php

namespace App\Http\Resources\Area;

use Domain\Shared\Area\Models\Area;
use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin Area
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
