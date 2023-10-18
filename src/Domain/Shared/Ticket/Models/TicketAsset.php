<?php

namespace Domain\Shared\Ticket\Models;

use Domain\Shared\User\Models\BranchAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAsset extends Model
{
    use HasFactory;

    protected $table = 'tickets_assets';

    protected $guarded = [
        'id',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function branchAsset(): BelongsTo
    {
        return $this->belongsTo(BranchAsset::class, 'branch_asset_id', 'id');
    }
}
