<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'is_success_story' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
