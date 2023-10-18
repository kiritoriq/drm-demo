<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Infrastructure\Filament\RedirectToIndex;

class EditContractor extends EditRecord
{
    use RedirectToIndex;

    protected static string $resource = ContractorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => Role::hasAny([Role::admin])),
            Actions\Action::make('Deactivate Account')
                ->label(label: 'Deactivate Account')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Deactivate Contractor Account')
                ->visible(fn ($livewire) => $livewire->data['status'] !== 0)
                ->action(function ($livewire) {
                    $record = $livewire->record;
                    $updated = $record->update([
                        'status' => 0
                    ]);

                    if ($updated) {
                        Notification::make()
                            ->title('Success')
                            ->body('Contractor account deactivated.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed')
                            ->body('Contractor account cannot be deactivated.')
                            ->danger()
                            ->send();
                    }
                })
                ->after(fn () => redirect(ContractorResource::getUrl('index'))),

            Actions\Action::make('Activate Account')
                ->label(label: 'Activate Account')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Activate Contractor Account')
                ->visible(fn ($livewire) => $livewire->data['status'] === 0)
                ->action(function ($livewire) {
                    $updated = $livewire->record->update([
                        'status' => 1
                    ]);

                    if ($updated) {
                        Notification::make()
                            ->title('Success')
                            ->body('Contractor account re-activated.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed')
                            ->body('Contractor account cannot be re-activated.')
                            ->danger()
                            ->send();
                    }
                })
                ->after(fn () => redirect(ContractorResource::getUrl('index'))),

            Actions\Action::make('Verify Account')
                ->label(label: 'Verify Account')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Verify Contractor Account')
                ->visible(fn ($livewire) => $livewire->record->isUnverified())
                ->action(function ($livewire) {
                    $updated = $livewire->record->update([
                        'verified_at' => now()
                    ]);

                    if ($updated) {
                        Notification::make()
                            ->title('Success')
                            ->body('Contractor account verified.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed')
                            ->body('Cannot verify contractor account.')
                            ->danger()
                            ->send();
                    }
                })
                ->after(fn () => redirect(ContractorResource::getUrl('index')))
        ];
    }
}
