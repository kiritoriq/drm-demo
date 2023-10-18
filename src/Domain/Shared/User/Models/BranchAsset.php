<?php

namespace Domain\Shared\User\Models;

use Domain\Shared\Area\Models\Area;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Builders\Branch\Asset\AssetBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BranchAsset extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    const IMAGE_COLLECTION_NAME = 'asset_images';

    const FILE_COLLECTION_NAME = 'asset_files';

    const WARRANTY_FILE_COLLECTION_NAME = 'warranty_files';

    protected $table = 'branches_assets';

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'company_name',
        'outlet_loc',
    ];

    public function newEloquentBuilder($query): AssetBuilder
    {
        return new AssetBuilder($query);
    }

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
        $this->addMediaCollection(self::IMAGE_COLLECTION_NAME);
        $this->addMediaCollection(self::FILE_COLLECTION_NAME);
        $this->addMediaCollection(self::WARRANTY_FILE_COLLECTION_NAME);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(AssetPart::class, 'branch_asset_id', 'id');
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'asset_type', 'id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function repairHistories(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tasks_assets', 'branch_asset_id', 'task_id');
    }

    protected function companyName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch->owner->company_name
        );
    }

    protected function outletLoc(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->branch->name
        );
    }
}
