<?php

namespace App\Http\Resources\Task\IssueReport;

use App\Http\Resources\Task\Resource as TaskResource;
use App\Http\Resources\User\Resource as UserResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Ticket\Models\IssueReport;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issue_report' => $this->issue_report,
            // 'description' => $this->description,
            'created_at' => $this->created_at,
            'attachments' => \App\Http\Resources\Media\Resource::collection($this->getMedia(IssueReport::COLLECTION_NAME)),
            'assignee' => new UserResource($this->assignee),
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
