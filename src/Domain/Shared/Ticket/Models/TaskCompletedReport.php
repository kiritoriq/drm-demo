<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskCompletedReport extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $table = 'task_completed_reports';

    protected $guarded = [
        'id'
    ];

    const COLLECTION_NAME = 'task_completed_reports';

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(130)
            ->height(130);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLLECTION_NAME);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}