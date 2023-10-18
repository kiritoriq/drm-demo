<?php

namespace App\Policies\Ticket\Task;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;

class TaskPolicy
{
    protected function checkAssignee(User $user, Task $task): bool
    {
        return $task->assignee_id === $user->id;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ! Role::exactlyCustomerRole();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return ! Role::exactlyCustomerRole();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::hasAny([Role::serviceManager]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return Role::hasAny([Role::serviceManager]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return Role::hasAny([Role::serviceManager]) && ! $task->isCompleted();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        //
    }

    public function accept(User $user, Task $task): bool
    {
        return $this->checkAssignee($user, $task);
    }

    public function reject(User $user, Task $task): bool
    {
        return $this->checkAssignee($user, $task);
    }

    public function start(User $user, Task $task): bool
    {
        return $this->checkAssignee($user, $task);
    }

    public function complete(User $user, Task $task): bool
    {
        return $this->checkAssignee($user, $task);
    }
}
