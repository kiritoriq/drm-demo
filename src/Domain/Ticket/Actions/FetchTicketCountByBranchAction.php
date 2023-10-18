<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\Enums\StatusColor;
use Domain\Ticket\Tappable\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTicketCountByBranchAction extends Action
{
    public function execute(): array
    {
        $statuses = Status::getCasesBasedOnRole();

        $counts = [];

        $branches = FetchBranchHasTicketAction::resolve()
            ->execute();

        foreach ($statuses as $status) {
            $datas = [];

            foreach ($branches as $branch) {
                $datas[] = Ticket::query()
                    ->tap(new FilterForOwnUser(attribute: 'assignee_id'))
                    ->where('branch_id', $branch['id'])
                    ->where('status', $status)
                    ->count();
            }

            $counts[] = [
                'name' => $status->name == 'Solved' ? 'Job Done' : Str::headline($status->name),
                'color' => StatusColor::resolveValue($status->name),
                'data' => $datas
            ];
        }

        return $counts;
    }
}