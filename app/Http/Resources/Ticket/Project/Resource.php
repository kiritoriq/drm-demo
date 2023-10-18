<?php

namespace App\Http\Resources\Ticket\Project;

use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Ticket\Models\Project;
use Illuminate\Http\Request;

/**
 *@mixin Project
 */
class Resource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
