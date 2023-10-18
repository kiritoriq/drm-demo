<?php

namespace Domain\Ticket\Actions;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Tappable\FilterForOwnUser;
use Illuminate\Support\Facades\Cache;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchMonthlyProjectsChartAction extends Action
{
    public function execute()
    {
        $projects = Project::query()
            ->whereNull('deleted_at')
            ->get();
        
        $series = [];

        foreach ($projects as $project) {
            $tickets = Ticket::query()
                ->tap(new FilterForOwnUser(attribute: 'assignee_id'))
                ->where('project_id', $project->id)
                ->whereYear('created_at', now()->format('Y'))
                ->get()
                ->groupBy(fn ($query) => Carbon::parse($query->created_at)->format('m'));

            $ticketMonth = [];

            $data = [];

            foreach ($tickets as $key => $value) {
                $ticketMonth[(int)$key] = count($value);
            }

            for ($i = 1; $i <= 12; $i++) {
                if (!empty($ticketMonth[$i])) {
                    $data[] = $ticketMonth[$i];
                } else {
                    $data[] = 0;
                }
            }

            $series[] = [
                'name' => $project->name,
                'data' => $data,
                'color' => Cache::remember($project->name . '-color', 86400, fn () => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6))
            ];
        }

        return $series;
    }
}