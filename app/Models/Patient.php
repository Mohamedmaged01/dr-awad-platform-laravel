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

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A unique file number (P{year}{seq}). Checks against trashed rows too, since
     * the unique index still covers soft-deleted patients — otherwise a count-based
     * scheme collides after any deletion.
     */
    public static function generateFileNumber(): string
    {
        $year = now()->format('Y');
        $seq = self::withTrashed()->count() + 1;

        do {
            $candidate = 'P' . $year . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (self::withTrashed()->where('file_number', $candidate)->exists());

        return $candidate;
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
