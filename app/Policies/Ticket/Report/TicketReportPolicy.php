<?php

namespace App\Policies\Ticket\Report;

use Domain\Shared\Ticket\Models\TicketReport;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class TicketReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ! Role::exactlyCustomerRole();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketReport $report): bool
    {
        return ! Role::exactlyCustomerRole();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::serviceManager]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketReport $report): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::serviceManager]) && $report->is_generated !== 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketReport $report): bool
    {
        return Role::hasAny([Role::serviceManager]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TicketReport $report): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TicketReport $report): bool
    {
        //
    }
}
