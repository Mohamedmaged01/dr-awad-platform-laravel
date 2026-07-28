<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'prescription' => 'array',
        'vitals' => 'array',
        'attachments' => 'array',
        'is_confidential' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
