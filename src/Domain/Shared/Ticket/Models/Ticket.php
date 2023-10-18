<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\Area\Models\Area;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\Models\PreventiveService;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Builders\TicketBuilder;
use Domain\Ticket\Enums\Priority;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\ValueObjects\TicketNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Ticket extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    const COLLECTION_NAME = 'ticket_images';

    protected $table = 'tickets';

    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'priority' => Priority::class,
    ];

    protected $appends = [
        'company', 'email', 'phone', 'outlet_pic_name', 'outlet_phone'
    ];

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
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('system')
            ->logOnly(['branch.name', 'status', 'priority', 'project.name', 'assignee.name', 'due_at', 'updated_at', 'tasks', 'quotations'])
            ->dontLogIfAttributesChangedOnly(['ticket_number', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => 'Ticket has been ' . $eventName);
    }

    public function ticketNumber(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $ticketNumber = new TicketNumber(
                    latestNumber: intval($value),
                    date: $this->created_at,
                    project: $this->project
                );

                return $ticketNumber->formatted;
            }
        );
    }

    public function newEloquentBuilder($query): TicketBuilder
    {
        return new TicketBuilder($query);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'ticket_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'ticket_id', 'id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'tickets_areas', 'ticket_id', 'area_id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(BranchAsset::class, 'tickets_assets', 'ticket_id', 'branch_asset_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'ticket_id', 'id');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class, 'ticket_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(TicketReport::class, 'ticket_id', 'id');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(related: Activity::class, name: 'subject');
    }

    public function backupAssignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_assignees', 'ticket_id', 'assignee_id');
    }

    public function preventiveServices(): HasManyThrough
    {
        return $this->hasManyThrough(
            PreventiveService::class,
            TicketAsset::class,
            'ticket_id',
            'branch_asset_id',
            'id',
            'branch_asset_id'
        );
    }

    protected function company(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch?->owner?->company_name
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch?->owner?->id
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch?->owner?->id
        );
    }

    protected function outletPicName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch?->person_in_charge
        );
    }

    protected function outletPhone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch?->phone
        );
    }
}
