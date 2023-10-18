<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\DataTransferObjects\Task\ReadLogData;
use App\Http\Controllers\Controller;
use App\Http\Responses\HttpResponse;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Task\Actions\FetchActivityLogAction;
use Domain\Task\Actions\FetchActivityLogsAction;
use Domain\Task\Actions\ResolveReadLogAction;
use Domain\Task\Actions\ResolveReadLogsAction;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    public function getLogActivities(Request $request): Paginator
    {
        $tickets = FetchActivityLogsAction::resolve()
            ->execute(
                user: $request->user(),
                perPage: $request->per_page ?? 15
            );

        return $tickets;
    }

    public function getLogDetail(Request $request, Ticket $ticket): Responsable
    {
        $data = FetchActivityLogAction::resolve()
            ->execute(
                user: $request->user(),
                ticket: $ticket
            );

        return new HttpResponse(
            success: true,
            code: Response::HTTP_OK,
            data: $data->toArray()
        );
    }

    public function readLog(Request $request): Responsable
    {
        $data = ResolveReadLogAction::resolve()
            ->execute(
                data: ReadLogData::resolveFrom($request->all())
            );

        return new HttpResponse(
            success: true,
            code: Response::HTTP_OK,
            data: $data
        );
    }

    public function readLogs(Request $request, Task $task): Responsable
    {
        $data = ResolveReadLogsAction::resolve()
            ->execute($task);

        return new HttpResponse(
            success: true,
            code: Response::HTTP_OK,
            data: $data->toArray()
        );
    }
}