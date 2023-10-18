<?php

namespace App\Policies\Service;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\Service\Models\Service;
use Domain\Shared\User\Models\User;

class ServicePolicy
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
    public function view(User $user, Service $service): bool
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
    public function update(User $user, Service $service): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return Role::hasAny([Role::officeAdmin]);
    }
}