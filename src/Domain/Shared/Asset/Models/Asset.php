<?php

namespace Domain\Shared\Asset\Models;

use Domain\Shared\Ticket\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function task(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tasks_assets');
    }
}
