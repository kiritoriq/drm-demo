<?php

namespace Domain\Ticket\Actions\Project;

use Domain\Shared\Ticket\Models\Project;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchDefaultProjectAction extends Action
{
    public function execute()
    {
        return Project::query()
            ->firstOrCreate(
                ['name' => 'SMRE'],
                ['description' => 'Maintenance']
            )->id;
    }
}
