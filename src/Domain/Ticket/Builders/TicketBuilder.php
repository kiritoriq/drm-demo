<?php

namespace Domain\Ticket\Builders;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Priority;
use Domain\Ticket\Enums\Status;
use Illuminate\Database\Eloquent\Builder;

class TicketBuilder extends Builder
{
    public function agreedQuotation()
    {
        return $this->model->quotations()
            ->where('is_client_agreed', 1)
            ->count();
    }

    public function newTicket(): bool
    {
        return $this->model->status === Status::New;
    }

    public function inProgressTicket(): bool
    {
        return $this->model->status === Status::InProgress;
    }

    public function solvedTicket(): bool
    {
        return $this->model->status === Status::Solved;
    }

    public function isQuotationUploaded(): bool
    {
        return $this->model->status == Status::QuoteRequested ||
            $this->model->status == Status::Quoted;
    }

    public function lowPriority(): bool
    {
        return $this->model->priority === Priority::Low;
    }

    public function mediumPriority(): bool
    {
        return $this->model->priority === Priority::Medium;
    }

    public function highPriority(): bool
    {
        return $this->model->priority === Priority::High;
    }

    public function criticalPriority(): bool
    {
        return $this->model->priority === Priority::Critical;
    }

    public function whereNewTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::New->value);
    }

    public function whereInProgressTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::InProgress->value);
    }

    public function whereQuotedTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::Quoted->value);
    }

    public function whereQuoteRequestedTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::QuoteRequested->value);
    }

    public function whereSolvedTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::Solved->value);
    }

    public function whereCancelledTicket(): static
    {
        return $this->where(column: 'status', operator: '=', value: Status::Cancelled->value);
    }

    public function whenStatusIsNew($status): static
    {
        return $this->when(
            $status && $status === 'new' && ! Role::customersRole(),
            fn (Builder $query) => $query->whereStatus(Status::make('new'))->whereNotNull('assignee_id')
        )
            ->when(
                $status && $status === 'new' && Role::customersRole(),
                fn (Builder $query) => $query->whereStatus(Status::make('new'))
            );
    }

    public function whenStatusIsNewUnassigned($status): static
    {
        return $this->when(
            $status && $status === 'unassigned',
            fn (Builder $query) => $query->whereStatus(Status::make('new'))->whereNull('assignee_id')
        );
    }

    public function whereBranchCustomerRole(User $user): static
    {
        return $this->whereIn('branch_id', $user->attachedBranches()->pluck('id')->toArray());
    }

    public function whereStatusIsOngoing(): static
    {
        return $this->whereNotIn('status', [Status::Cancelled, Status::Solved]);
    }
}
