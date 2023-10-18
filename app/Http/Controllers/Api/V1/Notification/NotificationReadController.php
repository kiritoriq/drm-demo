<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\Resource;
use Domain\Notification\Actions\MarkAsReadAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Infrastructure\Notification\Models\Notification;

class NotificationReadController extends Controller
{
    public function store(Notification $notification): Resource
    {
        MarkAsReadAction::resolve()
            ->execute($notification, resolve(name: Authenticatable::class));

        return new Resource($notification->load(relations: ['user', 'read', 'reads.user']));
    }
}