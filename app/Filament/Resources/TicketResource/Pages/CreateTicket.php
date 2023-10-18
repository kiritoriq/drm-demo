<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Notification\Created\ResolveBackupAssigneeNotificationAction;
use Domain\Ticket\Actions\ResolveMaxTicketNumberByMonthAction;
use Domain\Ticket\Enums\Priority;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;
use Spatie\Valuestore\Valuestore;

class CreateTicket extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = TicketResource::class;

    public array $assets = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Role::exactlyCustomerRole()) {
            $data['priority'] = Priority::Low->value;

            $settings = Valuestore::make(config('filament-settings.path'));

            $data['due_at'] = now()->addDays($settings->get('low_priority_due'));
        }

        $data['ticket_number'] = str_pad(
            string: (string) ResolveMaxTicketNumberByMonthAction::resolve()->execute(),
            length: 4,
            pad_string: '0',
            pad_type: STR_PAD_LEFT
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        if (filled ($this->record->backupAssignees)) {
            ResolveBackupAssigneeNotificationAction::resolve()
                ->execute(
                    ticket: $this->record,
                    assignees: $this->record->backupAssignees
                );
        }
    }
}
