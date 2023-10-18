<?php

namespace Infrastructure\Notification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Infrastructure\Notification\Builders\NotificationBuilder;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'mobile_notifications';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function newEloquentBuilder($query): NotificationBuilder
    {
        return new NotificationBuilder($query);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function read(): HasOne
    {
        return $this->hasOne(related: NotificationRead::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(related: NotificationRead::class);
    }
}