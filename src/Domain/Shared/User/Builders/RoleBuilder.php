<?php

namespace Domain\Shared\User\Builders;

use Domain\Shared\User\Enums\Role;
use Illuminate\Database\Eloquent\Builder;

class RoleBuilder extends Builder
{
    public function resolveType(Role $type): null | int
    {
        return $this->where(column: 'guard_name', operator: '=', value: 'web')
            ->where(column: 'name', operator: '=', value: $type->value)
            ->value(column: 'id');
    }
}