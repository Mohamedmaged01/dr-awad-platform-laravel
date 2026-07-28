<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'medical_history' => 'array',
        'date_of_birth' => 'date',
        'is_vip' => 'boolean',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function ivfCycles()
    {
        return $this->hasMany(IvfCycle::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Display name used across the admin tables. */
    public function getNameAttribute(): string
    {
        return trim($this->first_name_ar . ' ' . $this->last_name_ar);
    }

    /** Prefer the seeded demo age so the table matches the prototype exactly. */
    public function getAgeAttribute(): ?int
    {
        return $this->medical_history['age'] ?? $this->date_of_birth?->age;
    }

    /** Short name used by the IVF cards and dashboard ("سارة أحمد"). */
    public function getShortNameAttribute(): string
    {
        return $this->medical_history['short_name'] ?? $this->name;
    }

    /** Demo display fields stored in the medical_history jsonb — parity convention. */
    public function getCaseTypeAttribute(): ?string
    {
        return $this->medical_history['type'] ?? null;
    }

    public function getLastVisitAttribute(): ?string
    {
        return $this->medical_history['last_visit'] ?? null;
    }

    public function getDemoStatusAttribute(): string
    {
        return $this->medical_history['status'] ?? 'active';
    }
}
