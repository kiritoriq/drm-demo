<?php

namespace App\Policies\Area;

use Domain\Shared\Area\Models\Area;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class AreaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Role::has(Role::officeAdmin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Area $area): bool
    {
        return Role::has(Role::officeAdmin);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::has(Role::officeAdmin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Area $area): bool
    {
        return Role::has(Role::officeAdmin);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Area $area): bool
    {
        return Role::has(Role::officeAdmin);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Area $area): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Area $area): bool
    {
        //
    }
}
