<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TicketResource;
use Closure;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Enums\Status;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class TaskLists extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    public static function canView(): bool
    {
        return ! Role::customersRole();
    }

    protected function getTableHeading(): string
    {
        return 'My Task Lists';
    }

    protected function getTableQuery(): Builder
    {
        $query = Ticket::query();

        return $query
            ->whereStatusIsOngoing()
            ->where('assignee_id', resolve(Authenticatable::class)->id)
            ->latest('updated_at');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make(name: 'ticket_number')
                ->label(label: 'Ticket Number')
                ->searchable(),
            
            Tables\Columns\TextColumn::make(name: 'description')
                ->label(label: 'Job Description')
                ->words(6)
                ->searchable(),

            Tables\Columns\TextColumn::make(name: 'status')
                ->enum(Status::getCaseOptions())
                ->searchable(),

            Tables\Columns\TextColumn::make(name: 'updated_at')
                ->label(label: 'Last Action')
                ->dateTime()
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make(name: 'View Ticket')
                ->button()
                ->url(fn (Ticket $record) => TicketResource::getUrl('edit', $record))
        ];
    }
}
