<?php

namespace App\Filament\Widgets;

use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\FetchMonthlyTotalTicketAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TotalTicketsChart extends ApexChartWidget
{
    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'totalTicketsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total Tickets';

    public static function canView(): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager, Role::account]) ||
            (Role::exactlyCustomerRole());
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Tickets',
                    'data' => FetchMonthlyTotalTicketAction::resolve()->execute(),
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'decimalsInFloat' => 0,
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}
