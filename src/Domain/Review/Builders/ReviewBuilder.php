<?php

namespace Domain\Review\Builders;

use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReviewBuilder extends Builder
{
    public function whereAssignedToContractor(User $user): static
    {
        return $this->whereContractorId($user->id);
    }
}
