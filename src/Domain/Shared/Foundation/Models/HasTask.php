<?php

namespace Domain\Shared\Foundation\Models;

use Domain\Shared\Ticket\Models\Task;

trait HasTask
{
    public readonly Task $task;

    public function resolveTask(Task $task): static
    {
        $this->task = $task;

        return $this;
    }
}