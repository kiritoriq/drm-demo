<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\Review\Models\Review;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\Models\User;
use Domain\Task\Builders\TaskBuilder;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Task extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'status' => JobStatus::class,
    ];

    protected $appends = [
        'reject_reason',
        'task_cost'
    ];

    const COLLECTION_NAME = 'task_attachments';

    const SITE_VISIT_COLLECTION_NAME = 'site_visit_task_attachments';

    const AFTER_FIX_COLLECTION_NAME = 'completed_task_attachments';

    protected static $recordEvents = ['created', 'updated'];

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
        $this->addMediaCollection(self::SITE_VISIT_COLLECTION_NAME);
        $this->addMediaCollection(self::AFTER_FIX_COLLECTION_NAME);
    }

    public function newEloquentBuilder($query): TaskBuilder
    {
        return new TaskBuilder($query);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('system')
            ->logOnly(['assignee', 'status', 'title', 'description'])
            ->dontLogIfAttributesChangedOnly(['task_number', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => 'Task has been ' . $eventName);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id', 'id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(BranchAsset::class, 'tasks_assets', 'task_id', 'branch_asset_id');
    }

    public function cancelledTask(): HasOne
    {
        return $this->hasOne(TaskCancel::class, 'task_id', 'id');
    }

    public function issueReports(): HasMany
    {
        return $this->hasMany(IssueReport::class, 'task_id', 'id');
    }

    public function processStatuses(): HasMany
    {
        return $this->hasMany(ProcessStatus::class, 'task_id', 'id')->latest('created_at');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'task_id', 'id');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(related: Activity::class, name: 'subject');
    }

    public function completedReports(): HasMany
    {
        return $this->hasMany(TaskCompletedReport::class, 'task_id', 'id');
    }
    
    public function costs(): HasMany
    {
        return $this->hasMany(TaskCost::class, 'task_id', 'id');
    }

    public function isReviewed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->review()->exists()
        );
    }

    public function rejectReason(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cancelledTask?->reject_reason
        );
    }

    public function taskCost(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->costs()?->sum('cost'),
            set: fn () => $this->costs()?->sum('cost')
        );
    }
}
