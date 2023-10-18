<?php

namespace App\Policies\Ticket\SiteVisit;

use Domain\Shared\Ticket\Models\SiteVisit;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;

class SiteVisitPolicy
{
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
    public function view(User $user, SiteVisit $siteVisit): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Role::hasAny([Role::serviceManager]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiteVisit $siteVisit): bool
    {
        return $user->id === $siteVisit->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiteVisit $siteVisit): bool
    {
        return $user->id === $siteVisit->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiteVisit $siteVisit): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiteVisit $siteVisit): bool
    {
        //
    }
}
