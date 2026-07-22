<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IvfCycle extends Model
{
    use HasUuids;

    protected $table = 'ivf_cycles';

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'medications' => 'array',
        'is_pregnant' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function followups()
    {
        return $this->hasMany(IvfFollowup::class, 'cycle_id')->orderByDesc('followup_date');
    }

    /** Latest followup carries day_of_cycle + next_appointment for the demo card. */
    public function latestFollowup()
    {
        return $this->hasOne(IvfFollowup::class, 'cycle_id')->orderByDesc('followup_date');
    }
}
