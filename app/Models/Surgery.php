<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasUuids;

    protected $table = 'surgeries';

    protected $guarded = [];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_date' => 'datetime',
        'attachments' => 'array',
        'total_cost' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
