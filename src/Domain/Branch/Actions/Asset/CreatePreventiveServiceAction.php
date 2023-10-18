<?php

namespace Domain\Branch\Actions\Asset;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\Models\PreventiveService;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class CreatePreventiveServiceAction extends Action
{
    public function execute(BranchAsset $asset, Task $task): void
    {
        if ($asset->isPreventiveCycleIsMonth()) {
            $serviceCount = 12/$asset->preventive_service;
            $startedDate = Carbon::parse($task->completed_at);
            $nextServiceDate = $startedDate->addMonths($asset->preventive_service);

            for ($i = 0; $i < $serviceCount; $i++) {
                if ($i == 0) {
                    $nextDate = $nextServiceDate;
                } else {
                    $nextDate = $nextServiceDate->addMonths($asset->preventive_service);
                }

                PreventiveService::create([
                    'branch_asset_id' => $asset->id,
                    'next_service_date' => $nextDate->format('Y-m-d'),
                    'created_at' => now()
                ]);
            }
        }

        if ($asset->isPreventiveCycleIsYear()) {
            if ($asset->preventive_service <= 1) {
                PreventiveService::create([
                    'branch_asset_id' => $asset->id,
                    'next_service_date' => Carbon::parse($task->completed_at)->addYear()->format('Y-m-d'),
                    'created_at' => now()
                ]);
            }
        }
    }
}