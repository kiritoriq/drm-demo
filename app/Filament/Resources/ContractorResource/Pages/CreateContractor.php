<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\DataTransferObjects\User\Contractor\ContractorData;
use App\Filament\Resources\ContractorResource;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\User\Actions\Contractor\AfterUpsertContractorAction;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Infrastructure\Filament\RedirectToIndex;

class CreateContractor extends CreateRecord
{
    use RedirectToIndex;

    protected array $contractor;

    protected array $locationCoverages;

    protected array $services;

    protected static string $resource = ContractorResource::class;

    protected function beforeCreate(): void
    {
        $users = User::query()
            ->where('email', $this->data['email'])
            ->whereNull('deleted_at')
            ->get();

        foreach ($users as $exist) {
            if ($exist->hasRole(Role::contractor->value)) {
                Notification::make()
                    ->danger()
                    ->title('Create Contractor Account Failed')
                    ->body('Email address is already used!')
                    ->persistent()
                    ->send();
                
                $this->halt();
            }
        }
    }

    protected function afterCreate(): void
    {
        $this->record->update([
            'status' => 0,
            'verified_at' => now()->format('Y-m-d H:i:s')
        ]);

        $this->record->assignRole(Role::contractor->value);

        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();

        foreach ($adminUsers as $user) {
            $user->notify(
                Notification::make()
                    ->title(__('notification.web.settings.contractor.created.title'))
                    ->body(__('notification.web.settings.contractor.created.content', [
                        'contractor_name' => $this->record->name
                    ]))
                    ->success()
                    ->toDatabase()
            );
        }
    }
}
