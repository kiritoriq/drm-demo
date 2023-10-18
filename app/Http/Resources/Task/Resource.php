<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\Ticket\Resource as TicketResource;
use App\Http\Resources\Task\ProcessStatus\Resource as ProcessStatusResource;
use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Ticket\Models\Task;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_number' => $this->task_number,
            'status' => $this->status->value,
            'title' => $this->title,
            'description' => $this->description,
            'date_time' => $this->date_time,
            'due_date' => $this->due_date,
            'cost' => floatval(number_format($this->task_cost, 2, '.')),
            'accepted_at' => $this->accepted_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'completed_notes' => $this->completed_notes,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'images' => \App\Http\Resources\Media\Resource::collection($this->getMedia(Task::COLLECTION_NAME)),
            // 'site_visit_attachments' => \App\Http\Resources\Media\Resource::collection($this->getMedia(Task::SITE_VISIT_COLLECTION_NAME)),
            // 'after_fix_attachments' => \App\Http\Resources\Media\Resource::collection($this->getMedia(Task::AFTER_FIX_COLLECTION_NAME)),
            'assignee' => new UserResource($this->assignee),
            'process_statuses' => ProcessStatusResource::collection($this->whenLoaded('processStatuses')),
            'ticket' => new TicketResource($this->whenLoaded('ticket')),
            'issue_reports' => IssueReport\Resource::collection($this->whenLoaded('issueReports')),
            'costs' => Cost\Resource::collection($this->whenLoaded('costs')),
            'completed_reports' => CompletedReport\Resource::collection($this->whenLoaded('completedReports')),
            'cancelled_task' => new Cancel\Resource($this->whenLoaded('cancelledTask'))
        ];
    }
}
