<?php

namespace Domain\Task\Builders;

use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class TaskBuilder extends Builder
{
    public function resolveFromTaskNumberData(TaskNumberData $data): Task
    {
        return $this
            ->with(relations: ['ticket'])
            ->where(['task_number' => $data->taskNumber])
            ->sole();
    }

    public function whereAssignedTask(User $user): static
    {
        return $this->where('assignee_id', $user->id);
    }

    public function eligibleToAccept(): bool
    {
        return $this->model->status === JobStatus::New;
    }

    public function eligibleToReject(): bool
    {
        return $this->eligibleToAccept();
    }

    public function eligibleToStart(): bool
    {
        return in_array($this->model->status, [
            JobStatus::JobAwarded,
            JobStatus::QcRejected
        ]);
    }

    public function eligibleToComplete(): bool
    {
        return $this->model->status == JobStatus::Progress;
    }

    public function eligibleToReportIssue(): bool
    {
        return $this->model->status == JobStatus::PendingSiteVisit;
    }

    public function eligibleToReview(): bool
    {
        return $this->model->status === JobStatus::ProgressCompleted &&
            Role::hasAny([Role::admin, Role::customer]) &&
            $this->model->review()->count() <= 0;
    }

    public function contractorDoesntHaveDevices(): bool
    {
        return $this->model->assignee->devices->isEmpty();
    }

    public function getContractor(): User
    {
        return $this->model->assignee;
    }

    public function getContractorExternalUserIds(): array
    {
        return Arr::wrap($this->getContractor()->getOriginal(key: 'email'));
    }

    public function hasContractor(): bool
    {
        return filled($this->model->assignee_id);
    }

    public function doesntHaveContractor(): bool
    {
        return ! $this->hasContractor();
    }

    public function isCompleted(): bool
    {
        return in_array ($this->model->status, [
            JobStatus::ProgressCompleted,
            JobStatus::TaskFinished,
            JobStatus::QcRejected
        ]);
    }
}
