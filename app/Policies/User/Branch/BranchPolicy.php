<?php

namespace App\Policies\User\Branch;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\User\Models\User;

class BranchPolicy
{
    public function checkOwnership(User $user, Branch $branch): bool
    {
        return $user->id === $branch->user_id;
    }

    public function viewAny(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::customer]);
    }

    public function view(User $user, Branch $branch): bool
    {
        return Role::hasAny([Role::officeAdmin]) || $this->checkOwnership($user, $branch);
    }

    public function create(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::customer]);
    }

    public function update(User $user, Branch $branch): bool
    {
        return $this->checkOwnership($user, $branch);
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $this->checkOwnership($user, $branch);
    }
}
