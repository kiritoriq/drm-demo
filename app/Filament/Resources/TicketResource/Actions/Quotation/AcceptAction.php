<?php

namespace App\Filament\Resources\TicketResource\Actions\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Quotation\Notification\Accepted\SendNotificationAction;
use Domain\Ticket\Enums\Status;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class AcceptAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->label(label: 'Accept');

        $this->authorize(abilities: 'accept');

        $this->icon(icon: 'heroicon-o-check');

        $this->color(color: 'success');

        $this->visible(function (Quotation $record): bool {
            return Role::hasAny([Role::admin, Role::customer, Role::branchCustomer]);
        });

        $this->failureNotification(fn () => Notification::make()
            ->title('Cannot accept quotation, there\'s existing accepted quotation.')
            ->danger()
        );

        $this->action(function (Quotation $record) {
            $record->update([
                'is_client_agreed' => 1
            ]);

            $record->ticket->update([
                'status' => Status::Quoted->value
            ]);

            SendNotificationAction::resolve()
                ->execute(ticket: $record->ticket);

            activity()
                ->causedBy(resolve(Authenticatable::class)->id)
                ->performedOn($record->ticket)
                ->withProperties([
                    'attributes' => [
                        'quotation_number' => $record->quotation_number
                    ]
                ])
                ->event('quotation accepted')
                ->log(description: 'Quotation has been accepted by user');
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'accept-quotation';
    }
}
