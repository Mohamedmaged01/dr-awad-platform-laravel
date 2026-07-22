<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Fetch a settings row, decoding JSON-typed values.
     * Dashboard / IVF stat blocks are seeded here as JSON — parity convention.
     */
    public static function json(string $key, array $default = []): array
    {
        $row = static::where('key', $key)->first();

        if (! $row) {
            return $default;
        }

        return json_decode((string) $row->value, true) ?? $default;
    }
}
