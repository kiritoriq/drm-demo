<?php

namespace Domain\Shared\User\Tappable;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FilterForOwnUser
{
    public function __invoke(Builder $builder): Builder
    {
        if (Role::exactlyCustomerRole()) {
            $builder->where('user_id', User::query()->resolve()->id);
        }

        return $builder;
    }
}
