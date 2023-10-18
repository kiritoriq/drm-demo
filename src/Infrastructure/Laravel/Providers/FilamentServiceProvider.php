<?php

namespace Infrastructure\Laravel\Providers;

use App\Filament\Resources\ContractorResource;
use App\Filament\Resources\TicketResource;
use BackedEnum;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Actions\ResolveQuantityIndicatorAction;
use Domain\Ticket\Enums\Status;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->resolveNavigations();
    }

    protected function resolveNavigations(): void
    {
        Filament::serving(function () {
            $navigations = $this->resolveTicketStatusNavigationItems();

            Filament::registerNavigationItems(Arr::flatten(array: $navigations));
        });
    }

    protected function resolveTicketStatusNavigationItems(): array
    {
        return [
            ...$this->statusMenus(),
            ...$this->statusMenusForCustomer()
        ];
    }

    protected function statusMenus(): array
    {
        return [
            $this->resolveNavigationWithStatus(
                label: 'New ' . $this->resolveQuantityIndicator(Status::New),
                baseResource: TicketResource::class,
                status: Status::New,
                icon: 'heroicon-o-information-circle',
                group: 'Statuses',
                order: 2
            ),
            $this->resolveNavigationWithStatus(
                label: ((resolve(Authenticatable::class) && Role::customersRole()) ? 'Pending ' : 'Quote Requested ') . $this->resolveQuantityIndicator(Status::QuoteRequested),
                baseResource: TicketResource::class,
                status: Status::QuoteRequested,
                icon: 'heroicon-o-question-mark-circle',
                group: 'Statuses',
                order: 3
            ),
            $this->resolveNavigationWithStatus(
                label: 'Quoted ' . $this->resolveQuantityIndicator(Status::Quoted),
                baseResource: TicketResource::class,
                status: Status::Quoted,
                icon: 'heroicon-o-check-circle',
                group: 'Statuses',
                order: 4
            ),
            $this->resolveNavigationWithStatus(
                label: 'In Progress ' . $this->resolveQuantityIndicator(Status::InProgress),
                baseResource: TicketResource::class,
                status: Status::InProgress,
                icon: 'heroicon-o-play',
                group: 'Statuses',
                order: 5
            ),
            $this->resolveNavigationWithStatus(
                label: 'Job Done ' . $this->resolveQuantityIndicator(Status::Solved),
                baseResource: TicketResource::class,
                status: Status::Solved,
                icon: 'heroicon-o-check-circle',
                group: 'Statuses',
                order: 6
            )
        ];
    }

    protected function statusMenusForCustomer(): array
    {
        if (resolve(Authenticatable::class) && ! Role::hasAny([Role::customer, Role::branchCustomer])) {
            return [
                $this->resolveNavigationWithStatus(
                    label: 'New (Unassigned) ' . $this->resolveQuantityIndicator('unassigned'),
                    baseResource: TicketResource::class,
                    status: 'unassigned',
                    icon: 'heroicon-o-information-circle',
                    group: 'Statuses',
                    order: 1
                ),
                $this->resolveNavigationWithStatus(
                    label: 'Invoice Due ' . $this->resolveQuantityIndicator(Status::InvoiceDue),
                    baseResource: TicketResource::class,
                    status: Status::InvoiceDue,
                    icon: 'heroicon-o-currency-dollar',
                    group: 'Statuses',
                    order: 7
                ),
                $this->resolveNavigationWithStatus(
                    label: 'Invoice Overdue ' . $this->resolveQuantityIndicator(Status::InvoiceOverdue),
                    baseResource: TicketResource::class,
                    status: Status::InvoiceOverdue,
                    icon: 'heroicon-s-currency-dollar',
                    group: 'Statuses',
                    order: 8
                ),
                $this->resolveNavigationWithStatus(
                    label: 'Unverified Contractor (' . User::query()->whereRelation('roles', 'name', '=', Role::contractor->value)->whereNull('verified_at')->get()->count() . ')',
                    baseResource: ContractorResource::class,
                    status: 'unverified',
                    icon: 'heroicon-s-users',
                    group: 'Contractors',
                    order: 2
                )
            ];
        }

        return [];
    }

    /**
     * @param string $label
     * @param class-string<resource> $baseResource
     * @param Status $status
     * @param string $icon
     * @param string $group
     * @param int $order
     * @return NavigationItem
     */
    protected function resolveNavigationWithStatus(
        string $label,
        string $baseResource,
        Status | string $status,
        string $icon,
        string $group,
        int $order
    ): NavigationItem {
        $getRouteBaseName = $baseResource::getRouteBaseName();

        return NavigationItem::make(label: $label)
            ->url(url: $baseResource::getUrl() . '?' . http_build_query(data: ['status' => ($status instanceof BackedEnum ? $status->value : $status)]))
            ->icon(icon: $icon)
            ->isActiveWhen(fn () => request()->routeIs("{$getRouteBaseName}.*") && request()->get(key: 'status') === ($status instanceof BackedEnum ? $status->value : $status))
            ->group(group: $group)
            ->sort(sort: $order);
    }

    protected function resolveQuantityIndicator(Status | string $status): string | int
    {
        $status = ($status instanceof BackedEnum) ? $status->value : $status;
        
        return ResolveQuantityIndicatorAction::resolve()
            ->execute($status);
    }
}
