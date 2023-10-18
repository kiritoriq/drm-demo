<?php

namespace App\Filament\Widgets;

use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\FetchMonthlyProjectsChartAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MonthlyProjectsChart extends ApexChartWidget
{
    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'monthlyProjectsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Monthly Projects';

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
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => FetchMonthlyProjectsChartAction::resolve()->execute(),
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
                'min' => 0,
                'forceNiceScale' => true,
                'decimalsInFloat' => 0,
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => false
            ],
            'colors' => ['#6366f1'],
        ];
    }
}
