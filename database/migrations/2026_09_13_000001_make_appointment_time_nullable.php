<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Public bookings now capture a date only — the clinic sets the exact
        // time when it confirms the request, so the time may be empty.
        Schema::table('appointments', function (Blueprint $table) {
            $table->time('appointment_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->time('appointment_time')->nullable(false)->change();
        });
    }
};
