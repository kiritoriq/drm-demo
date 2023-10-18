<?php

namespace Domain\Ticket\Tappable;

use Domain\Shared\User\Enums\Role;
use Domain\User\Tappable\FilterForInternalAdmin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class FilterForOwnUser
{
    public function __construct(
        public readonly string $attribute
    ) {

    }

    public function __invoke(Builder $query): Builder
    {
        if (Role::hasAny([
            Role::admin, Role::officeAdmin, Role::serviceManager, Role::account
        ])) {
            return $query->tap(new FilterForInternalAdmin($this->attribute));
        }

        if (Role::exactlyBranchCustomerRole()) {
            return $query->whereBranchCustomerRole(resolve(Authenticatable::class));
        }

        return $query->where('customer_id', resolve(Authenticatable::class)->id);
    }
}
