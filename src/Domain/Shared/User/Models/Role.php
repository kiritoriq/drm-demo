<?php

namespace Domain\Shared\User\Models;

use Domain\Shared\User\Builders\RoleBuilder;
use Spatie\Permission\Models\Role as Spatie;

final class Role extends Spatie
{
    public function newEloquentBuilder($query): RoleBuilder
    {
        return new RoleBuilder($query);
    }
}