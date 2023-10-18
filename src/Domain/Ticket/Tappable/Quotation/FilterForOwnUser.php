<?php

namespace Domain\Ticket\Tappable\Quotation;

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
            return $query->whereRelation(
                'ticket',
                fn (Builder $builder) => $builder->where('raised_by_id', resolve(Authenticatable::class)->id)
            );
        }

        return $query->whereRelation(
            'ticket',
            fn (Builder $builder) => $builder->where('customer_id', resolve(Authenticatable::class)->id)
        );
    }
}