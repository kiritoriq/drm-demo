<?php

namespace Domain\Shared\User\Models;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Builders\Wallet\WalletBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorWallet extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function newEloquentBuilder($query): WalletBuilder
    {
        return new WalletBuilder($query);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by', 'id');
    }
}