<?php

namespace Infrastructure\Notification\Tappable;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Infrastructure\Notification\Builders\NotificationBuilder;

final class FilterBasedOnUser
{
    public function __construct(public readonly User $user)
    {
        //
    }

    public function __invoke(NotificationBuilder $builder): void
    {
        $builder
            ->with(relations: [
                'user.roles',
                'read' => fn (HasOne $query) => $query
                    ->whereUserId($this->user->id),
            ])
            ->when(
                value: Role::has(role: Role::admin),
                callback: fn (NotificationBuilder $query) => $query
                    ->onlyContractor()
                    ->whereUnlessRolesNull()
                    ->orWhere(
                        fn (NotificationBuilder $query) => $query
                            ->onlyContractor()
                            ->whereUnlessRoles($this->user->roles)
                    )
            )
            ->when(
                value: Role::doesntHave(role: Role::admin),
                callback: fn (NotificationBuilder $query) => $query
                    ->whereRoles($this->user->roles)
                    ->whereUserNull()
                    ->orWhere(
                        fn ($query) => $query
                            ->whereRoles($this->user->roles)
                            ->whereUserId($this->user->id)
                    )
            )
            ->orderByDesc(column: 'id');
    }
}