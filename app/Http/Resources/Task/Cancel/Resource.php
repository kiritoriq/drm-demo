<?php

namespace App\Http\Resources\Task\Cancel;

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
            'reject_reason' => $this->reject_reason,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->user),
            'task' => new TaskResource($this->whenLoaded('task'))
        ];
    }
}
