<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'staff';

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
