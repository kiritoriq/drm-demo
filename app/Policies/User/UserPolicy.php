<?php

namespace App\Policies\User;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class UserPolicy
{
    public function checkRecordIsAdmin(User $model): bool
    {
        return $model->hasRole(Role::admin->value);
    }

    protected function checkOwnership(User $user, User $model): bool
    {
        if (Role::exactlyCustomerRole()) {
            return $user->id === $model->id;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::customer]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::customer]) && $this->checkOwnership($user, $model);
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
    public function update(User $user, User $model): bool
    {
        return Role::hasAny([Role::officeAdmin]) &&
            ! $this->checkRecordIsAdmin($model) &&
            $this->checkOwnership($user, $model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return Role::hasAny([Role::officeAdmin]) && ! $this->checkRecordIsAdmin($model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return Role::hasAny([Role::officeAdmin]) && ! $this->checkRecordIsAdmin($model);
    }

    public function verification(User $user, User $model): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin]);
    }
}
