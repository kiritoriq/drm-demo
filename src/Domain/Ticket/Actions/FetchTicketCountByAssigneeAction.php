<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\Enums\StatusColor;
use Domain\Ticket\Tappable\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTicketCountByAssigneeAction extends Action
{
    public function execute(): array
    {
        $statuses = Status::cases();

        $counts = [];

        $persons = FetchPersonAssignedToTicketAction::resolve()
            ->execute();

        foreach ($statuses as $status) {
            $datas = [];

            foreach ($persons as $person) {
                $datas[] = Ticket::query()
                    ->tap(new FilterForOwnUser(attribute: 'assignee_id'))
                    ->where('assignee_id', $person['id'])
                    ->where('status', $status)
                    ->count();
            }

            $counts[] = [
                'name' => Str::headline($status->name),
                'data' => $datas
                // 'color' => StatusColor::resolveValue($status->name),
            ];
        }

        return $counts;
    }
}