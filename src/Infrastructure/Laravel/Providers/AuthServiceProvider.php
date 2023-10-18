<?php

namespace Infrastructure\Laravel\Providers;

use App\Policies\Area\AreaPolicy;
use App\Policies\Service\ServicePolicy;
use App\Policies\Ticket\Project\ProjectPolicy;
use App\Policies\Ticket\Quotation\QuotationPolicy;
use App\Policies\Ticket\Report\TicketReportPolicy;
use App\Policies\Ticket\SiteVisit\SiteVisitPolicy;
use App\Policies\Ticket\Task\TaskPolicy;
use App\Policies\Ticket\TicketPolicy;
use App\Policies\User\Branch\BranchPolicy;
use App\Policies\User\UserPolicy;
use Domain\Shared\Area\Models\Area;
use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\Ticket\Models\SiteVisit;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\Service\Models\Service;
use Domain\Shared\Ticket\Models\TicketReport;
use Domain\Shared\User\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Branch::class => BranchPolicy::class,
        Area::class => AreaPolicy::class,
        Task::class => TaskPolicy::class,
        Quotation::class => QuotationPolicy::class,
        Ticket::class => TicketPolicy::class,
        Project::class => ProjectPolicy::class,
        SiteVisit::class => SiteVisitPolicy::class,
        Service::class => ServicePolicy::class,
        TicketReport::class => TicketReportPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole(Role::admin->value) ? true : null;
        });
    }
}
