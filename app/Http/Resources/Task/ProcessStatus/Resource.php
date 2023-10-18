<?php

namespace App\Http\Resources\Task\ProcessStatus;

use App\Http\Resources\Task\Resource as TaskResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'task' => new TaskResource($this->whenLoaded('task'))
        ];
    }
}
