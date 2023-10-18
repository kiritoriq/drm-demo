<?php

namespace App\Http\Resources\Review;

use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\Task\Resource as TaskResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stars' => $this->stars,
            'text_review' => $this->review,
            'created_at' => $this->created_at,
            'customer' => new UserResource($this->customer),
            'contractor' => new UserResource($this->contractor),
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
