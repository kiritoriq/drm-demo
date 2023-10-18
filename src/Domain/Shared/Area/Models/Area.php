<?php

namespace Domain\Shared\Area\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $guarded = [
        'id',
    ];
}
