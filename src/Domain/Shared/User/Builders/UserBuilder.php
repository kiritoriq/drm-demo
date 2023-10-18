<?php

namespace Domain\Shared\User\Builders;

use App\Mail\ResetPasswordMail;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class UserBuilder extends Builder
{
    public function active(): bool
    {
        return $this->model->status === 1;
    }

    public function whereStatusActive(): static
    {
        return $this->where(column: 'status', operator: '=', value: 1);
    }

    public function whereStatusInactive(): static
    {
        return $this->where(column: 'status', operator: '=', value: 0);
    }

    public function resolve(User | null $user = null): User | null
    {
        return $user ?? resolve(Authenticatable::class);
    }

    public function whereContractorRole(): static
    {
        return $this->whereRelation(
            'roles',
            fn (Builder $query) => $query->where('name', Role::contractor->value)
        );
    }

    public function whereOwnerRole(): static
    {
        return $this->whereRelation(
            'roles',
            fn (Builder $query) => $query->where('name', Role::customer->value)
        );
    }

    public function whereCanAssignedToTicket(): static
    {
        return $this->whereRelation(
            'roles',
            fn (Builder $query) => $query->whereNotIn('name', [Role::customer->value, Role::contractor->value, Role::branchCustomer->value])
        );
    }

    public function sendResetPasswordLink($token): void
    {
        Mail::to($this->model->email)->send(new ResetPasswordMail(user: $this->model, token: $token));
    }

    public function isVerified(): bool
    {
        return filled ($this->model->verified_at);
    }

    public function isUnverified(): bool
    {
        return blank ($this->model->verified_at);
    }
}
