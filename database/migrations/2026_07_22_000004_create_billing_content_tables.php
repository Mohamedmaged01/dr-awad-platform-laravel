<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ported from src/db/schema.ts — invoices, payments, content, reviews,
// messages, subscribers, activity logs, settings, permissions.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number', 50)->unique();
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments');
            $table->foreignUuid('surgery_id')->nullable()->constrained('surgeries');
            $table->foreignUuid('ivf_cycle_id')->nullable()->constrained('ivf_cycles');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('items')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('invoices');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->string('transaction_id', 100)->nullable();
            $table->enum('status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['article', 'video', 'faq', 'success_story', 'news', 'offer']);
            $table->string('title_ar', 300);
            $table->string('title_en', 300)->nullable();
            $table->string('slug', 300)->unique()->nullable();
            $table->text('content_ar')->nullable();
            $table->text('content_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->foreignUuid('author_id')->nullable()->constrained('staff');
            $table->foreignUuid('category_id')->nullable()->constrained('service_categories');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable();
            // readTime / duration / views demo fields live in the seo_* columns — parity convention.
            $table->string('seo_title', 100)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->nullable()->constrained('patients');
            $table->string('patient_name', 100)->nullable();
            $table->integer('rating');
            // title_ar = service, title_en = location — parity convention.
            $table->string('title_ar', 200)->nullable();
            $table->string('title_en', 200)->nullable();
            $table->text('content_ar')->nullable();
            $table->text('content_en')->nullable();
            $table->foreignUuid('service_id')->nullable()->constrained('services');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_success_story')->default(false);
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20);
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->string('source', 50)->nullable();
            $table->enum('status', ['unread', 'read', 'replied', 'archived'])->default('unread');
            $table->foreignUuid('assigned_to')->nullable()->constrained('staff');
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 255)->unique();
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('action', 100);
            $table->string('entity_type', 100)->nullable();
            $table->uuid('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string');
            $table->string('group', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('group', 100)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('role', ['admin', 'doctor', 'nurse', 'receptionist', 'patient', 'lab_technician']);
            $table->foreignUuid('permission_id')->constrained('permissions');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
