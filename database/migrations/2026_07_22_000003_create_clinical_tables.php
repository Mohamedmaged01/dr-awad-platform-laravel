<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ported from src/db/schema.ts — appointments, medical records, pregnancies, surgeries.
// NOTE: appointment status includes `waiting`, which the source UI used even though
// the Drizzle enum omitted it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('staff_id')->nullable()->constrained('staff');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->foreignUuid('service_id')->nullable()->constrained('services');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->time('end_time')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'waiting', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->string('type', 50)->nullable();
            $table->text('notes')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('staff_id')->nullable()->constrained('staff');
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments');
            $table->string('record_type', 50);
            $table->string('title', 200)->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('treatment')->nullable();
            $table->json('prescription')->nullable();
            $table->json('vitals')->nullable();
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });

        Schema::create('pregnancies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->date('lmp_date')->nullable();
            $table->date('edd_date')->nullable();
            $table->date('conception_date')->nullable();
            $table->string('conception_method', 50)->nullable();
            $table->foreignUuid('ivf_cycle_id')->nullable()->constrained('ivf_cycles');
            $table->boolean('is_high_risk')->default(false);
            $table->json('risk_factors')->nullable();
            $table->string('status', 50)->default('ongoing');
            $table->date('delivery_date')->nullable();
            $table->string('delivery_type', 50)->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('baby_gender', 10)->nullable();
            $table->decimal('baby_weight', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pregnancy_followups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pregnancy_id')->constrained('pregnancies');
            $table->foreignUuid('staff_id')->nullable()->constrained('staff');
            $table->date('followup_date');
            $table->integer('gestational_week')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->integer('fetal_heart_rate')->nullable();
            $table->decimal('fundal_height', 5, 2)->nullable();
            $table->text('ultrasound_notes')->nullable();
            $table->json('ultrasound_images')->nullable();
            $table->json('lab_results')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('next_appointment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('surgeries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('staff_id')->nullable()->constrained('staff');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches');
            $table->enum('surgery_type', ['laparoscopy', 'hysteroscopy', 'cesarean', 'natural_delivery', 'other']);
            $table->string('surgery_name', 200);
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('actual_date')->nullable();
            $table->integer('duration')->nullable();
            $table->string('anesthesia_type', 100)->nullable();
            $table->text('pre_op_diagnosis')->nullable();
            $table->text('post_op_diagnosis')->nullable();
            $table->text('procedure')->nullable();
            $table->text('findings')->nullable();
            $table->text('complications')->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->text('operative_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgeries');
        Schema::dropIfExists('pregnancy_followups');
        Schema::dropIfExists('pregnancies');
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('appointments');
    }
};
