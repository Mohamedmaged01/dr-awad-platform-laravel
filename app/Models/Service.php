<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'features' => 'array',
    ];
}
