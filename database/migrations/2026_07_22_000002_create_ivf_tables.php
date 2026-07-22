<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ported from src/db/schema.ts — IVF/ICSI cycles, followups, embryos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ivf_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('staff_id')->nullable()->constrained('staff');
            $table->integer('cycle_number');
            $table->string('cycle_type', 20); // ivf, icsi, iui
            $table->string('protocol', 100)->nullable();
            $table->date('start_date');
            $table->enum('current_stage', [
                'consultation', 'stimulation', 'egg_retrieval', 'fertilization',
                'embryo_transfer', 'pregnancy_test', 'completed', 'cancelled',
            ])->default('consultation');
            $table->date('stimulation_start_date')->nullable();
            $table->date('egg_retrieval_date')->nullable();
            $table->date('embryo_transfer_date')->nullable();
            $table->date('pregnancy_test_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('eggs_retrieved')->nullable();
            $table->integer('eggs_mature')->nullable();
            $table->integer('eggs_fertilized')->nullable();
            $table->integer('embryos_formed')->nullable();
            $table->integer('embryos_transferred')->nullable();
            $table->integer('embryos_frozen')->nullable();
            $table->boolean('is_pregnant')->nullable();
            $table->string('outcome', 50)->nullable();
            $table->json('medications')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('ivf_followups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cycle_id')->constrained('ivf_cycles');
            $table->date('followup_date');
            $table->integer('day_of_cycle')->nullable();
            $table->integer('follicle_count')->nullable();
            $table->json('follicle_details')->nullable();
            $table->decimal('endometrium_thickness', 5, 2)->nullable();
            $table->decimal('e2_level', 10, 2)->nullable();
            $table->decimal('lh_level', 10, 2)->nullable();
            $table->decimal('progesterone_level', 10, 2)->nullable();
            $table->json('medications')->nullable();
            $table->json('ultrasound_images')->nullable();
            $table->text('instructions')->nullable();
            $table->date('next_appointment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('embryos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cycle_id')->constrained('ivf_cycles');
            $table->integer('embryo_number');
            $table->string('quality', 20)->nullable();
            $table->string('grade', 20)->nullable();
            $table->integer('cell_count')->nullable();
            $table->integer('fragmentation_percent')->nullable();
            $table->string('status', 50)->default('fresh');
            $table->date('freeze_date')->nullable();
            $table->date('thaw_date')->nullable();
            $table->date('transfer_date')->nullable();
            $table->string('storage_location', 100)->nullable();
            $table->string('pgs_result', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embryos');
        Schema::dropIfExists('ivf_followups');
        Schema::dropIfExists('ivf_cycles');
    }
};
