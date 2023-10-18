<?php

namespace Infrastructure\Notification\Builders;

use Domain\Notification\Actions\UnpackRolesAction;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Role as ModelsRole;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NotificationBuilder extends Builder
{
    public function whereUserNull(): self
    {
        return$this->whereNull(['user_id']);
    }

    public function orWhereUserAndRoles(User $user): self
    {
        return $this->orWhere(fn (self $query) => $query->whereUserId($user->id)->whereRoles($user->roles));
    }

    public function whereRoles(Collection | int $roles): self
    {
        return $this->whereIn(column: 'role_id', values: UnpackRolesAction::resolve()->execute($roles));
    }

    public function onlyContractor(): self
    {
        return $this->whereRoles(ModelsRole::query()->resolveType(Role::contractor));
    }

    public function whereUnlessRoles(Collection | int $roles): self
    {
        return $this->whereNotIn(column: 'unless_role_id', values: UnpackRolesAction::resolve()->execute($roles));
    }

    public function whereUnlessRolesNull(): self
    {
        return $this->whereNull('unless_role_id');
    }
}