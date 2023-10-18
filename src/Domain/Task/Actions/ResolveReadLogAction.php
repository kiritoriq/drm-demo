<?php

namespace Domain\Task\Actions;

use App\DataTransferObjects\Task\ReadLogData;
use Carbon\Carbon;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Support\Collection;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Spatie\Activitylog\Models\Activity;
use stdClass;

readonly class ResolveReadLogAction extends Action
{
    public function execute(ReadLogData $data)
    {
        return value (
            function (Activity | Collection $log) {
                $log->update([
                    'read_at' => now()->format('Y-m-d H:i:s')
                ]);

                return [
                    'log_id' => $log->id,
                    'event' => $log->event,
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                    'read_at' => filled ($log->read_at) ? Carbon::parse($log->read_at) : null,
                    'previous_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['old']['status'])->name) : ''),
                    'changes_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['attributes']['status'])->name) : Str::headline(JobStatus::New->name))
                ];
            },

            log: Activity::query()
                ->find($data->logId)
        );
    }
}