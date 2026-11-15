<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category', 60)->default('umum');
                $table->unsignedInteger('sort')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('testimonials') && ! Schema::hasColumn('testimonials', 'avatar_path')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->string('avatar_path')->nullable()->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('testimonials') && Schema::hasColumn('testimonials', 'avatar_path')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->dropColumn('avatar_path');
            });
        }

        Schema::dropIfExists('faqs');
    }
};
