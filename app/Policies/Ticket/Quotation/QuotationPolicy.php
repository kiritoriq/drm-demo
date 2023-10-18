<?php

namespace App\Policies\Ticket\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class QuotationPolicy
{
    public function checkOwnership(User $user, Quotation $quotation): bool
    {
        if (Role::exactlyCustomerRole()) {
            return $user->id === $quotation->ticket->customer_id;
        }

        if (Role::exactlyBranchCustomerRole()) {
            return in_array($quotation->ticket->branch_id, $user->attachedBranches()->pluck('id')->toArray());
        }

        if (Role::exactlyServiceManagerRole()) {
            return $quotation->ticket->assignee_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::serviceManager, Role::customer, Role::branchCustomer]) && $this->checkOwnership($user, $quotation);
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
    public function update(User $user, Quotation $quotation): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::serviceManager]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        return Role::hasAny([Role::officeAdmin, Role::serviceManager]);
    }

    public function accept(User $user, Quotation $quotation): bool
    {
        return Role::hasAny([Role::customer, Role::branchCustomer]);
    }

    public function reject(User $user, Quotation $quotation): bool
    {
        return Role::hasAny([Role::customer, Role::branchCustomer]);
    }
}
