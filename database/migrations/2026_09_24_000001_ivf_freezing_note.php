<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Freezing is now a free-text note (was a yes/no flag).
        Schema::table('ivf_cycles', function (Blueprint $table) {
            if (! Schema::hasColumn('ivf_cycles', 'freezing_note')) {
                $table->string('freezing_note', 500)->nullable()->after('is_frozen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ivf_cycles', function (Blueprint $table) {
            if (Schema::hasColumn('ivf_cycles', 'freezing_note')) {
                $table->dropColumn('freezing_note');
            }
        });
    }
};
