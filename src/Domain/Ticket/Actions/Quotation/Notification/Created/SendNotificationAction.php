<?php

namespace Domain\Ticket\Actions\Quotation\Notification\Created;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(Ticket $ticket, Quotation $quotation): void
    {
        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();
        
        $branchUsers = $ticket->branch->users;

        $recipients = [
            ...$adminUsers,
            ...[
                $ticket->customer,
            ],
            ...[
                $ticket->raisedBy
            ],
            ...[
                $ticket->assignee
            ],
            ...$branchUsers
        ];

        $sentId = [];

        foreach ($recipients as $user) {
            if (! in_array ($user->id, $sentId)) {
                $user->notify(
                    Notification::make()
                        ->title(__('notification.web.quotation.created.title', [
                            'ticket_number' => $ticket->ticket_number
                        ]))
                        ->body(__('notification.web.quotation.created.content', [
                            'user_name' => $quotation->raisedBy->name,
                            'date_time' => now()->format('Y-m-d H:i:s')
                        ]))
                        ->success()
                        ->actions([
                            NotificationAction::make('view')
                                ->url(TicketResource::getUrl('edit', $ticket))
                        ])
                        ->toDatabase()
                );

                array_push($sentId, $user->id);
            }
        }
    }
}