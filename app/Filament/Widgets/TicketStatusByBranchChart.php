<?php

namespace App\Filament\Widgets;

use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\FetchBranchHasTicketAction;
use Domain\Ticket\Actions\FetchTicketCountByBranchAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TicketStatusByBranchChart extends ApexChartWidget
{
    protected int | string | array $columnSpan = 2;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'ticketStatusByBranchChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Ticket Status By Branch';

    public static function canView(): bool
    {
        return Role::exactlyCustomerRole();
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
                'type' => 'bar',
                'height' => 300,
                'stacked' => true,
                'stackType' => '100%'
            ],
            'series' => FetchTicketCountByBranchAction::resolve()->execute(),
            'xaxis' => [
                'categories' => array_map(fn ($value) => $value['name'], FetchBranchHasTicketAction::resolve()->execute()),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
