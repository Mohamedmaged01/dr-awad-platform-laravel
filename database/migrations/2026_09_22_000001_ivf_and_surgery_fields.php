<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ivf_cycles', function (Blueprint $table) {
            if (! Schema::hasColumn('ivf_cycles', 'dose')) {
                $table->string('dose', 20)->nullable()->after('protocol'); // normal | full
            }
            if (! Schema::hasColumn('ivf_cycles', 'stimulation_end_date')) {
                $table->date('stimulation_end_date')->nullable()->after('stimulation_start_date');
            }
            if (! Schema::hasColumn('ivf_cycles', 'fertilization_date')) {
                $table->date('fertilization_date')->nullable()->after('egg_retrieval_date');
            }
            if (! Schema::hasColumn('ivf_cycles', 'is_frozen')) {
                $table->boolean('is_frozen')->nullable()->after('embryos_frozen');
            }
        });

        Schema::table('surgeries', function (Blueprint $table) {
            // Free-typed surgeon name (datalist of existing doctors + custom entry).
            if (! Schema::hasColumn('surgeries', 'doctor_name')) {
                $table->string('doctor_name', 200)->nullable()->after('staff_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ivf_cycles', function (Blueprint $table) {
            foreach (['dose', 'stimulation_end_date', 'fertilization_date', 'is_frozen'] as $col) {
                if (Schema::hasColumn('ivf_cycles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('surgeries', function (Blueprint $table) {
            if (Schema::hasColumn('surgeries', 'doctor_name')) {
                $table->dropColumn('doctor_name');
            }
        });
    }
};
