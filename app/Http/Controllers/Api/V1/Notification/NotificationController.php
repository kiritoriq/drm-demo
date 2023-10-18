<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\Collection;
use App\Http\Responses\HttpResponse;
use Domain\Notification\Actions\FetchNotificationsAction;
use Domain\Shared\Ticket\Models\Task;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Collection
     */
    public function index(Request $request): Collection
    {
        return FetchNotificationsAction::resolve()
            ->execute($request->user());
    }

    public function getUnreadNotifications(Request $request): Responsable
    {
        $unread = Task::query()
            ->whereAssignedTask($request->user())
            ->whereRelation(
                'ticket',
                fn (Builder $query) => $query->whereStatusIsOngoing()
            )
            ->whereRelation(
                'logs',
                fn (Builder $query) => $query
                    ->whereIn('event', ['created', 'updated'])
                    ->whereNotNull('properties->attributes->status')
                    ->latest('created_at')
                    ->limit(50)
                    ->whereNull('read_at')
            )
            ->exists();

        return new HttpResponse(
            success: true,
            code: Response::HTTP_OK,
            data: [
                'has_unread_notifications' => $unread
            ]
        );
    }
}