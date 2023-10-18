<?php

namespace App\Policies\Ticket;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class TicketPolicy
{
    public function checkOwnership(User $user, Ticket $ticket): bool
    {
        if (Role::exactlyCustomerRole()) {
            return $user->id === $ticket->customer_id;
        }

        if (Role::exactlyBranchCustomerRole()) {
            return in_array($ticket->branch_id, $user->attachedBranches()->pluck('id')->toArray());
        }

        if (Role::exactlyServiceManagerRole()) {
            return $ticket->assignee_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Role::hasAny([Role::customer, Role::branchCustomer, Role::officeAdmin, Role::serviceManager, Role::contractor]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return Role::hasAny([Role::customer, Role::branchCustomer, Role::officeAdmin, Role::serviceManager, Role::contractor]) && $this->checkOwnership($user, $ticket);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::customer, Role::branchCustomer]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return Role::hasAny([Role::customer, Role::branchCustomer, Role::officeAdmin, Role::serviceManager]) && $this->checkOwnership($user, $ticket);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
