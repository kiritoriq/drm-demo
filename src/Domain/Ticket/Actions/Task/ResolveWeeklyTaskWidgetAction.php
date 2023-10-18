<?php

namespace Domain\Ticket\Actions\Task;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Task;
use Filament\Widgets\StatsOverviewWidget\Card;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveWeeklyTaskWidgetAction extends Action
{
    public function execute(): Card
    {
        return Card::make(
            label: 'Upcoming Task This Week',
            value: Task::query()
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'),
                    Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')
                ])
                ->count()
        );
    }
}