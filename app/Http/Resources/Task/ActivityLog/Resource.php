<?php

namespace App\Http\Resources\Task\ActivityLog;

use Domain\Shared\Foundation\Resources\JsonResource;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Task;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Http\Request;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $task = Task::find($this->subject_id);

        return [
            'task_id' => $this->subject_id,
            'task_number' => $task->task_number,
            'task_title' => $task->title,
            'event' => $this->event,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'previous_status' => Str::headline(JobStatus::make($this->properties['old']['status'])->name),
            'changes_status' => Str::headline(JobStatus::make($this->properties['attributes']['status'])->name)
        ];
    }
}