<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'branches';

    protected $guarded = [];

    protected $casts = [
        'working_hours' => 'array',
        'is_main' => 'boolean',
        'is_active' => 'boolean',
    ];

    /** Short label used by the admin lists ("فرع الدقي"). */
    public function getShortNameAttribute(): string
    {
        return $this->working_hours['short'] ?? $this->name_ar;
    }
}
