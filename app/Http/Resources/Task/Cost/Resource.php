<?php

namespace App\Http\Resources\Task\Cost;

use App\Http\Resources\Task\Resource as TaskResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cost' => $this->cost,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
