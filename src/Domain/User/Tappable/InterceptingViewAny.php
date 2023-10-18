<?php

namespace Domain\User\Tappable;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

abstract class InterceptingViewAny
{
    abstract protected function getAttribute(): mixed;

    abstract protected function resolveAuthenticatedForValue(Authenticatable | User $user): mixed;

    public function __invoke(Builder $query): Builder
    {
        $user = User::query()->resolve();

        if (Role::hasAny([Role::admin], $user)) {
            return $query;
        }

        return $query->where($this->getAttribute(), $this->resolveAuthenticatedForValue($user));
    }
}
