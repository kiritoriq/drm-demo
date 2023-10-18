<?php

namespace Domain\User\Tappable;

use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class FilterForInternalAdmin extends InterceptingViewAny
{
    public function __construct(
        public string $attribute = 'raised_by_id'
    ) { 
    }

    protected function getAttribute(): string
    {
        return $this->attribute;
    }

    protected function resolveAuthenticatedForValue(Authenticatable | User $user): int
    {
        return $user->id;
    }
}