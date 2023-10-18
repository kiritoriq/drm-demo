<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\TaskAsset;
use Domain\Shared\User\Models\BranchAsset;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AssetHistories extends Page implements HasTable
{
    use InteractsWithTable,
        InteractsWithRecord;
    protected static string $resource = BranchResource::class;

    protected static string $view = 'filament.resources.branch-resource.pages.asset-histories';

    public function getModel(): string
    {
        return BranchAsset::class();
    }

    public function mount(BranchAsset $record)
    {
        $this->getRecord($record);
    }

    protected function getTableQuery(): Builder|Relation
    {
        return TaskAsset::query()
            ->where('branch_asset_id', $this->record->id)
            ->whereRelation(
                'task',
                fn (Builder $query) => $query->latest('date_time')
            );
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make(name: 'task.ticket.ticket_number')
                ->label(label: 'Ticket Number')
                ->searchable(),
            TextColumn::make(name: 'task.task_number')
                ->label(label: 'Task Number')
                ->searchable(),
            TextColumn::make(name: 'task.title')
                ->label(label: 'Task Title')
                ->description(fn (TaskAsset $record) => filled ($record->task->description) ? Str::descriptionText($record->task->description) : '')
                ->searchable(),
            TextColumn::make(name: 'task.assignee.name')
                ->label(label: 'Contractor')
                ->searchable(),
            TextColumn::make(name: 'task.date_time')
                ->label(label: 'Date')
                ->dateTime()
                ->searchable()
        ];
    }
}
