<?php

namespace App\Filament\Resources\TicketResource\Actions\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Quotation\Notification\Rejected\SendNotificationAction;
use Domain\Ticket\Enums\Status;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\Auth\Authenticatable;

class RejectAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->label(label: 'Reject');

        $this->authorize(abilities: 'accept');

        $this->icon(icon: 'heroicon-s-x');

        $this->color(color: 'danger');

        $this->visible(function (Quotation $record): bool {
            return Role::hasAny([Role::admin, Role::customer, Role::branchCustomer]);
        });

        $this->action(function (Quotation $record) {
            $record->update([
                'is_client_agreed' => 0,
            ]);

            $record->ticket->update([
                'status' => Status::QuoteRequested->value
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
                ->event('quotation rejected')
                ->log(description: 'Quotation has been rejected by user');
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'reject-quotation';
    }
}
