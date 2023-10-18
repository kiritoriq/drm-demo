<?php

namespace App\Filament\Resources\BranchResource\Actions\Asset;

use Domain\Shared\User\Models\BranchAsset;
use Filament\Tables\Actions\Action;

class ViewQRCodeAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(label: 'View QR Code');

        $this->icon(icon: 'heroicon-s-eye');

        $this->visible(fn (BranchAsset $record) => filled($record->asset_code));

        $this->modalWidth(width: 'md');

        $this->modalActions(fn (): array => array_merge(
            $this->getExtraModalActions(),
            [$this->getModalCancelAction()->label(__('filament-support::actions/view.single.modal.actions.close.label'))],
        ));

        $this->action(fn () => $this->record);

        $this->modalContent(function (BranchAsset $record) {
            return view('filament.resources.branch-resource.pages.view-qr-code', [
                'record' => $record,
            ]);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'view-qr-code';
    }
}
