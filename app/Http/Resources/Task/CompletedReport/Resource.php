<?php

namespace App\Http\Resources\Task\CompletedReport;

use App\Http\Resources\Task\Resource as TaskResource;
use App\Http\Resources\User\Resource as UserResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Ticket\Models\TaskCompletedReport;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'attachments' => \App\Http\Resources\Media\Resource::collection($this->getMedia(TaskCompletedReport::COLLECTION_NAME)),
            'assignee' => new UserResource($this->user),
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
