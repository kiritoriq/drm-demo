<?php

namespace App\Filament\Resources\BranchResource\Actions\Asset;

use App\Filament\Resources\BranchResource;
use Domain\Shared\User\Models\BranchAsset;
use Filament\Tables\Actions\Action;

class AssetHistoriesAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(label: 'Histories');

        $this->color(color: 'primary');

        $this->icon(icon: 'heroicon-o-exclamation-circle');

        $this->url(function (BranchAsset $record): string {
            return BranchResource::getUrl('asset-histories', $record);
        });

        $this->action(function() {
            //
        });
    }

    /**
     * @throws Exception
     */
    public static function getDefaultName(): null | string
    {
        return 'histories';
    }
}
