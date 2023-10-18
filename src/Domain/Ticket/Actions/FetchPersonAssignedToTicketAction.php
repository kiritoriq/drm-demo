<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchPersonAssignedToTicketAction extends Action
{
    public function execute(): array
    {
        $user = User::query()
            ->whereIn(
                'id',
                Ticket::query()
                    ->when(
                        value: Role::hasAny([Role::officeAdmin, Role::serviceManager]),
                        callback: fn (Builder $query) => $query->where('assignee_id', resolve(Authenticatable::class)->id)
                    )
                    ->whereNotNull('assignee_id')
                    ->pluck('assignee_id')
            )
            ->get()
            ->toArray();

        return $user;
    }
}