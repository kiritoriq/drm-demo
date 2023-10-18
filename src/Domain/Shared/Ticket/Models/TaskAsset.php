<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\User\Models\BranchAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAsset extends Model
{
    use HasFactory;

    protected $table = 'tasks_assets';

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(BranchAsset::class, 'branch_asset_id', 'id');
    }
}
