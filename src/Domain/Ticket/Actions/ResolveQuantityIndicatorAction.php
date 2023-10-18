<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Enums\Status;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveQuantityIndicatorAction extends Action
{
    public function execute(Status | string $status): string
    {
        if (resolve(Authenticatable::class)) {
            $counted = Ticket::query()
                ->when(
                    Role::exactlyCustomerRole(),
                    fn (Builder $query) => $query->where('customer_id', resolve(Authenticatable::class)->id)
                )
                ->when(
                    Role::exactlyBranchCustomerRole(),
                    fn (Builder $query) => $query->whereBranchCustomerRole(resolve(Authenticatable::class))
                )
                ->when(
                    $status && ! in_array($status, ['new', 'unassigned']),
                    fn (Builder $query) => $query->whereStatus(Status::make($status))
                )
                ->when(
                    Role::exactlyServiceManagerRole(),
                    fn (Builder $query) => $query->where('assignee_id', resolve(Authenticatable::class)->id)
                )
                ->whenStatusIsNew($status)
                ->whenStatusIsNewUnassigned($status)
                ->count();

            return '('. $counted .')';
        }

        return '';
    }
}
