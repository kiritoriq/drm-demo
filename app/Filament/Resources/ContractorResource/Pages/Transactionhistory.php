<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use App\Filament\Resources\ContractorResource\Widgets\WalletBalanceWidget;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\ContractorWallet;
use Domain\Shared\User\Models\User;
use Domain\User\Actions\Contractor\ResolveCreditWalletAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;

class TransactionHistory extends Page implements HasTable
{
    use InteractsWithTable,
        InteractsWithRecord;

    protected static string $resource = ContractorResource::class;

    protected static string $view = 'filament.resources.contractor-resource.pages.transaction-history';

    public function getModel(): string
    {
        return User::class();
    }

    public function mount(User $record)
    {
        $this->getRecord($record);
    }

    protected function getTableQuery(): Builder|Relation
    {
        return ContractorWallet::query()
            ->where('user_id', $this->record->id)
            ->orderBy('created_at', 'desc');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WalletBalanceWidget::class,
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('task.ticket.ticket_number')
                    ->label(label: 'Ticket Number')
                    ->searchable(),
            Tables\Columns\TextColumn::make('task.task_number')
                ->label(label: 'Task Number')
                ->searchable(),
            Tables\Columns\TextColumn::make('task.title')
                ->label(label: 'Task Title')
                ->searchable(),
            Tables\Columns\TextColumn::make('amount')
                ->label(label: 'Cost')
                ->money(currency: 'myr', shouldConvert: true),
            Tables\Columns\TextColumn::make('task.completed_at')
                ->label(label: 'Completed Date')
                ->dateTime()
                ->searchable(),
            IconColumn::make('is_redeemed')
                ->label(label: 'Credited Status')
                ->boolean(true),
            Tables\Columns\TextColumn::make('redeemed_at')
                ->label(label: 'Credited Date')
                ->dateTime(),
            Tables\Columns\TextColumn::make('redeemedBy.name')
                ->label(label: 'Credited By'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            TernaryFilter::make(name: 'credited_status')
                ->label(label: 'Credited Status')
                ->placeholder(placeholder: 'All Status')
                ->trueLabel(trueLabel: 'Credited Status')
                ->falseLabel(falseLabel: 'Uncredited Status')
                ->queries(
                    true: fn (Builder $query) => $query->whereNotNull('redeemed_at'),
                    false: fn (Builder $query) => $query->whereNull('redeemed_at'),
                    blank: fn (Builder $query) => $query
                )
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make(name: 'credit')
                ->label(label: 'Credit to Contractor')
                ->visible(fn (ContractorWallet $record) => Role::hasAny([Role::admin, Role::officeAdmin]) && blank ($record->redeemed_at))
                ->button()
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
                ->requiresConfirmation(true)
                ->modalHeading('Credit to Contractor')
                ->modalSubheading(function (ContractorWallet $record) {
                    $user = User::find($record->user_id);

                    return 'Are you sure you\'d like to credit RM ' . number_format($record->amount, 2, '.', ',') . ' to ' . $user->name . '? This cannot be undone.';
                })
                ->action(fn (ContractorWallet $record) => ResolveCreditWalletAction::resolve()->execute($record))
        ];
    }
}