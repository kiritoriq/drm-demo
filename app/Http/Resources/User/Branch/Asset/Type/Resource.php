<?php

namespace App\Http\Resources\User\Branch\Asset\Type;

use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\User\Models\Category;
use Illuminate\Http\Request;

/**
 * @mixin Category
 */
class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
