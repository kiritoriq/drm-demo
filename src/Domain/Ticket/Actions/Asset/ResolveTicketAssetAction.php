<?php

namespace Domain\Ticket\Actions\Asset;

use Domain\Shared\Ticket\Models\Ticket;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveTicketAssetAction extends Action
{
    public function execute(Ticket $ticket, array $data): void
    {
        $assets = collect($data)->map(fn ($item) => [
            'branch_asset_id' => $item,
        ]);

        $ticket->assets()->createMany($assets);
    }
}
