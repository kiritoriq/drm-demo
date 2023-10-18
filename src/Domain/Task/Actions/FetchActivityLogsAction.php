<?php

namespace Domain\Task\Actions;

use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Infrastructure\Laravel\LengthAwarePaginator;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Spatie\Activitylog\Models\Activity;
use stdClass;

readonly class FetchActivityLogsAction extends Action
{
    public function execute(
        User $user,
        int $perPage
    ): Collection | Paginator {
        $tickets = Ticket::query()
            ->select('id', 'ticket_number', 'subject', 'description', 'due_at', 'created_at', 'updated_at', 'customer_id', 'branch_id')
            ->whereRelation(
                'tasks',
                fn (Builder $query) => $query->where('assignee_id', $user->id)
            )
            ->latest('updated_at')
            ->whereStatusIsOngoing()
            ->paginate(perPage: $perPage);

        $dataTransformed = $tickets
            ->getCollection()
            ->map(function ($ticket) use ($user) {
                $ticket->setAppends([]);
                $ticket->created_at = $ticket->created_at;
                $ticket->last_updated_at = $ticket->updated_at;
                unset($ticket->updated_at);

                $tasks = Task::query()->where('ticket_id', $ticket->id)->where('assignee_id', $user->id)->get()->pluck('id')->toArray();

                $ticket->is_last_log_read = true;
                $unreadLogs = Activity::query()
                    ->whereMorphedTo(relation: 'subject', model: Task::class)
                    ->whereIn('event', ['created', 'updated'])
                    ->whereIn('subject_id', $tasks)
                    ->whereNotNull('properties->attributes->status')
                    ->latest('created_at')
                    ->whereNull('read_at')
                    ->limit(50)
                    ->get();

                if (filled ($unreadLogs)) {
                    $ticket->is_last_log_read = false;
                }

                $ticket->customer = new stdClass;
                $ticket->customer = User::query()
                    ->select('id', 'name', 'email', 'company_name', 'brand_name', 'phone', 'office_address')
                    ->where('id', $ticket->customer_id)
                    ->first();
                unset($ticket->customer_id);

                $ticket->branch = new stdClass;
                $ticket->branch = Branch::query()
                    ->select('name', 'description', 'phone', 'address', 'latitude', 'longitude')
                    ->where('id', $ticket->branch_id)
                    ->first();
                unset($ticket->branch_id);

                $ticket->project = Project::select('id', 'name')
                    ->find($ticket->project_id);

                return $ticket;
            })->toArray();

        return new LengthAwarePaginator(
                $dataTransformed,
                $tickets->total(),
                $tickets->perPage(),
                $tickets->currentPage(),
                [
                    'path' => resolve(Request::class)->url(),
                    'query' => [
                        'page' => $tickets->currentPage()
                    ]
                ]
            );
    }
}