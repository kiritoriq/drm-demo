<?php

namespace Domain\Shared\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetPart extends Model
{
    use HasFactory;

    protected $table = 'assets_parts';

    protected $guarded = [
        'id',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(BranchAsset::class, 'branch_asset_id', 'id');
    }
}
