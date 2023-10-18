<?php

namespace Domain\Task\Actions\Notifiable;

use Domain\Notification\Actions\ResolveExternalUserIds;
use Domain\Shared\Foundation\Models\HasTask;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Infrastructure\OneSignal\Enums\Platform;

final class ResolveExternalUserIdsAction extends ResolveExternalUserIds
{
    use HasTask;

    public function skipWhen(): bool
    {
        return $this->task->contractorDoesntHaveDevices();
    }

    public function resolveType(): Role
    {
        return Role::contractor;
    }

    public function resolvePlatform(): Platform
    {
        return Platform::contractor;
    }

    public function resolveIds(): array
    {
        return $this->task->getContractorExternalUserIds();
    }

    public function resolveModel(): User
    {
        return $this->task->getContractor();
    }

    public function mergeNotificationData(): array
    {
        return $this->task->only(attributes: 'task_number');
    }

    public function resolveUnlessType(): Role | null
    {
        return null;
    }
}