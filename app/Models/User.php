<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ];
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
}
