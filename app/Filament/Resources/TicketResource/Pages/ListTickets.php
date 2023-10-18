<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Enums\Status;
use Exception;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public string | null $status = null;

    public function getQueryString()
    {
        return parent::getQueryString() + ['status' => ['except' => '']];
    }

    /**
     * @return array<int, Actions\Action>
     *
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->when(
                $this->status && ! in_array($this->status, ['new', 'unassigned']),
                fn (Builder $query) => $query->whereStatus(Status::make($this->status))
            )
            ->whenStatusIsNew($this->status)
            ->whenStatusIsNewUnassigned($this->status)
            ->when(
                Role::exactlyCustomerRole(),
                fn (Builder $query) => $query->where('customer_id', resolve(Authenticatable::class)->id)
            )
            ->when(
                Role::exactlyBranchCustomerRole(),
                fn (Builder $query) => $query->whereBranchCustomerRole(resolve(Authenticatable::class))
            )
            ->when(
                Role::exactlyServiceManagerRole(),
                fn (Builder $query) => $query->where('assignee_id', resolve(Authenticatable::class)->id)
            );
    }
}
