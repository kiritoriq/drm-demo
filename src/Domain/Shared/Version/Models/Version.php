<?php

namespace Domain\Shared\Version\Models;

use Domain\Shared\Version\Builders\VersionBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $guarded = [
        'id',
    ];

    public function newEloquentBuilder($query): VersionBuilder
    {
        return new VersionBuilder($query);
    }

    public function version(): Attribute
    {
        return new Attribute(
            get: fn () => $this->major_version . '.' . $this->minor_version . '.' . $this->patch_version
        );
    }
}