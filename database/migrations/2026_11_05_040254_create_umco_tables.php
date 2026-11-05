<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add role & password change flag to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('email');
            }
            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('role');
            }
        });

        // Menu Categories
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // in Rupiah
            $table->string('image_path')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('favorite_order')->default(0);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_favorite', 'favorite_order']);
        });

        // Banners
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('cta_label')->default('Lihat Selengkapnya');
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // Reservations
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('phone_input');
            $table->string('phone_wa');
            $table->date('date');
            $table->string('time', 10);
            $table->unsignedSmallInteger('party_size');
            $table->text('note')->nullable();
            $table->string('mode', 20)->default('table_only'); // table_only, table_menu
            $table->string('status', 20)->default('pending'); // pending, confirmed, completed, cancelled
            $table->string('payment_status', 20)->default('none'); // none, waiting_proof, proof_uploaded, verified, rejected
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('dp_amount')->default(0);
            $table->string('payment_proof_path')->nullable();
            $table->text('admin_note')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['date', 'time', 'status']);
            $table->index('phone_wa');
        });

        // Reservation Items
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('name_snapshot');
            $table->unsignedInteger('price_snapshot');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->timestamps();
        });

        // Settings (Zero-code storage for all text, numbers, toggles, images, and JSON)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index();
            $table->string('key', 100)->unique();
            $table->text('payload')->nullable();
            $table->timestamps();
        });

        // Story Blocks (About Page, Brand Narrative, Bean-to-Cup, H-Architecture)
        Schema::create('story_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50)->default('about');
            $table->string('key', 50);
            $table->string('title');
            $table->text('body');
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // Gallery Items (Indoor AC, Outdoor Street Food, General)
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('group', 30)->default('general'); // indoor, outdoor, general
            $table->string('type', 20)->default('image'); // image, video
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Facilities (AC, Outdoor, Nugas Friendly, Colokan, Takeaway, Parkir, Wi-Fi)
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('icon')->default('check');
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Street Food Stalls (Outdoor directory)
        Schema::create('street_stalls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('signature_item')->nullable();
            $table->text('description')->nullable();
            $table->string('active_hours')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Testimonials (Rating & Review)
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('body');
            $table->string('source', 30)->default('google'); // google, manual
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sample')->default(false);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // Closed Dates (Libur khusus kampus atau renovasi)
        Schema::create('closed_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // price_change, status_change, setting_update, whatsapp_click
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('closed_dates');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('street_stalls');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('story_blocks');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
