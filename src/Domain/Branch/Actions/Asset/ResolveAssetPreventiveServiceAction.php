<?php

namespace Domain\Branch\Actions\Asset;

use Domain\Shared\Ticket\Models\Task;
use Domain\Branch\Actions\Asset\CreatePreventiveServiceAction;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveAssetPreventiveServiceAction extends Action
{
    public function execute(Task $task): void
    {
        foreach ($task->ticket->assets as $asset) {
            CreatePreventiveServiceAction::resolve()
                ->execute(
                    asset: $asset,
                    task: $task
                );
        }
    }
}