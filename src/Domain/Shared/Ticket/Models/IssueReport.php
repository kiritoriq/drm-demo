<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IssueReport extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $table = 'issue_reports';

    protected $guarded = [
        'id'
    ];

    const COLLECTION_NAME = 'issue_report_images';

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
        return $this->belongsTo(Ticket::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
