<?php

namespace Infrastructure\Laravel\Providers;

use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\ServiceProvider;
use Reworck\FilamentSettings\FilamentSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/filament.css');
        });

        Filament::registerRenderHook(
            'body.start',
            fn () => '<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>'
        );

        Filament::registerNavigationGroups([
            'Statuses',
            'Settings',
            'Contractors',
            'Master Data',
            'Versions'
        ]);

        FilamentSettings::setFormFields([
            Section::make('Ticket Due')
                ->description('Based On Priority')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make(name: 'low_priority_due')
                                ->label(label: 'Low Priority')
                                ->suffix('days'),
                            TextInput::make(name: 'medium_priority_due')
                                ->label(label: 'Medium Priority')
                                ->suffix('days'),
                            TextInput::make(name: 'high_priority_due')
                                ->label(label: 'High Priority')
                                ->suffix('days'),
                            TextInput::make(name: 'critical_priority_due')
                                ->label(label: 'Critical Priority')
                                ->suffix('days'),
                        ]),
                ]),

            Section::make('Ticket Reminder')
                ->description('Based On Status')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make(name: 'new_reminder')
                                ->label(label: 'New')
                                ->suffix('days'),
                            TextInput::make(name: 'quote_requested_reminder')
                                ->label(label: 'Quote Requested')
                                ->suffix('days'),
                            TextInput::make(name: 'quoted_reminder')
                                ->label(label: 'Quoted')
                                ->suffix('days'),
                            TextInput::make(name: 'in_progress_reminder')
                                ->label(label: 'In Progress')
                                ->suffix('days'),
                            TextInput::make(name: 'solved_reminder')
                                ->label(label: 'Job Done')
                                ->suffix('days'),
                            TextInput::make(name: 'invoice_due_reminder')
                                ->label(label: 'Invoice Due')
                                ->suffix('days'),
                        ]),
                ]),
        ]);
    }
}
