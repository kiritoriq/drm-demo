<?php

namespace Domain\Shared\User\Tappable\Roles;

use Domain\Shared\User\Enums\Role;
use Illuminate\Database\Eloquent\Builder;

class ResolveRoleSelections
{
    public function __invoke(Builder $builder): Builder
    {
        if (Role::has(Role::officeAdmin) && Role::doesntHave(Role::admin)) {
            $builder
                ->whereIn('name', [
                    Role::officeAdmin->value,
                    Role::serviceManager->value,
                ]);
        }

        return $builder
            ->whereIn('name', Role::internalUserRoles());
    }
}
