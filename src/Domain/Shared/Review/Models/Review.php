<?php

namespace Domain\Shared\Review\Models;

use Domain\Review\Builders\ReviewBuilder;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function newEloquentBuilder($query): ReviewBuilder
    {
        return new ReviewBuilder($query);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id', 'id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
