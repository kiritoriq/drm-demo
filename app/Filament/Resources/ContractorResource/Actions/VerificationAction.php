<?php

namespace App\Filament\Resources\ContractorResource\Actions;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Filament\Tables\Actions\Action;

class VerificationAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();
        
        $this->requiresConfirmation();

        $this->label(label: 'Verify Account');

        $this->authorize(abilities: 'verification');

        $this->icon(icon: 'heroicon-o-check');

        $this->color(color: 'success');

        $this->visible(fn (User $record) => $record->isUnverified() && Role::hasAny([Role::admin, Role::officeAdmin]));

        $this->action(function (User $record) {
            $record->update([
                'verified_at' => now()->format('Y-m-d H:i:s')
            ]);
        });
    }

    /**
     * @throws Exception
     */
    public static function getDefaultName(): null | string
    {
        return 'verification';
    }
}