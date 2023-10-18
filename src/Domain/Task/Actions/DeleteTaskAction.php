<?php

namespace Domain\Task\Actions;

use Domain\Shared\Ticket\Models\Task;
use Illuminate\Contracts\Auth\Authenticatable;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class DeleteTaskAction extends Action
{
    public function execute(Task $task): void
    {
        if ($task->assets()->exists()) {
            $task->assets()->detach();
        }

        if ($task->review()->exists()) {
            $task->review()->delete();
        }

        activity()
            ->causedBy(resolve(Authenticatable::class)->id)
            ->performedOn($task->ticket)
            ->event('task deleted')
            ->log(description: 'Task with number ' . $task->task_number . ' has been deleted');

        $task->delete();
    }
}