<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function beforeCreate(): void
    {
        $users = User::query()
            ->where('email', $this->data['email'])
            ->get();

        foreach ($users as $exist) {
            if ($exist->hasRole(Role::customer->value)) {
                Notification::make()
                    ->danger()
                    ->title('Create Customer Account Failed')
                    ->body('Email address is already used!')
                    ->persistent()
                    ->send();
                
                $this->halt();
            }
        }
    }

    protected function afterCreate(): void
    {
        if (filled ($this->record->parent_id)) {
            $this->record->assignRole(Role::branchCustomer->value);
        } else {
            $this->record->assignRole(Role::customer->value);
        }

        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();

        foreach ($adminUsers as $user) {
            $user->notify(
                Notification::make()
                    ->title(__('notification.web.settings.customer.created.title'))
                    ->body(__('notification.web.settings.customer.created.content', [
                        'customer_name' => $this->record->name
                    ]))
                    ->success()
                    ->toDatabase()
            );
        }
    }
}
