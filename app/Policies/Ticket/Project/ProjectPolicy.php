<?php

namespace App\Policies\Ticket\Project;

use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }
}
