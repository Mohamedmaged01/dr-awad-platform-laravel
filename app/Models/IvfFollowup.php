<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IvfFollowup extends Model
{
    use HasUuids;

    protected $table = 'ivf_followups';

    protected $guarded = [];

    protected $casts = [
        'followup_date' => 'date',
        'next_appointment' => 'date',
        'medications' => 'array',
        'follicle_details' => 'array',
        'ultrasound_images' => 'array',
    ];

    public function cycle()
    {
        return $this->belongsTo(IvfCycle::class, 'cycle_id');
    }
}
