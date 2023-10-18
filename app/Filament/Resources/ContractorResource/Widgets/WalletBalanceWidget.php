<?php

namespace App\Filament\Resources\ContractorResource\Widgets;

use Domain\Shared\User\Models\User;
use Domain\User\Actions\Contractor\ResolveWalletBalanceWidgetAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Model;

class WalletBalanceWidget extends BaseWidget
{
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        $user = User::find($this->record->id);

        return [
            Card::make(label: 'Contractor Name', value: $user->name),
            ResolveWalletBalanceWidgetAction::resolve()
                ->execute(user: $user)
        ];
    }
}