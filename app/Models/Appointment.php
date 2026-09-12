<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'appointment_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /** "09:00" — bare HH:MM, or a dash when the time hasn't been set yet. */
    public function getTimeLabelAttribute(): string
    {
        $time = substr((string) $this->appointment_time, 0, 5);

        return $time !== '' ? $time : '—';
    }
}
