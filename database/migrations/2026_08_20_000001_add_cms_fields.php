<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Bundles the display-only extras (category, author_name, read_time,
            // duration, views_label) so blog + video items need no new narrow columns.
            if (! Schema::hasColumn('contents', 'meta')) {
                $table->json('meta')->nullable()->after('excerpt_en');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'features')) {
                $table->json('features')->nullable()->after('description_en');
            }
            if (! Schema::hasColumn('services', 'color')) {
                $table->string('color', 100)->nullable()->after('icon');
            }
            if (! Schema::hasColumn('services', 'slug')) {
                $table->string('slug', 220)->nullable()->after('name_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            if (Schema::hasColumn('contents', 'meta')) {
                $table->dropColumn('meta');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            foreach (['features', 'color', 'slug'] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
