<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ported from src/db/schema.ts — branches, service categories, services, staff, patients.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_ar', 200);
            $table->string('name_en', 200)->nullable();
            $table->text('address_ar');
            $table->text('address_en')->nullable();
            $table->string('phone', 20);
            $table->string('phone_alt', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('google_maps_url', 500)->nullable();
            $table->json('working_hours')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_ar', 200);
            $table->string('name_en', 200)->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('slug', 200)->unique()->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->nullable()->constrained('service_categories');
            $table->string('name_ar', 200);
            $table->string('name_en', 200)->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('short_desc_ar')->nullable();
            $table->text('short_desc_en')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('duration')->nullable(); // minutes
            $table->string('icon', 100)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('seo_title', 100)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches');
            $table->string('first_name_ar', 100);
            $table->string('last_name_ar', 100);
            $table->string('first_name_en', 100)->nullable();
            $table->string('last_name_en', 100)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('specialization', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('file_number', 50)->unique()->nullable();
            $table->string('first_name_ar', 100);
            $table->string('last_name_ar', 100);
            $table->string('first_name_en', 100)->nullable();
            $table->string('last_name_en', 100)->nullable();
            $table->string('phone', 20);
            $table->string('phone_alt', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id', 20)->nullable();
            $table->enum('gender', ['male', 'female'])->default('female');
            $table->string('blood_type', 5)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 20)->nullable();
            // Demo display fields (type / last_visit / status) live here — parity convention.
            $table->json('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('branches');
    }
};
