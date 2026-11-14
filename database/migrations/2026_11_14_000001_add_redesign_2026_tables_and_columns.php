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
        if (! Schema::hasTable('seating_options')) {
            Schema::create('seating_options', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->integer('sort')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('spend_tiers')) {
            Schema::create('spend_tiers', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('party_min');
                $table->unsignedSmallInteger('party_max')->nullable();
                $table->unsignedInteger('min_spend');
                $table->unsignedInteger('per_extra_person')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crowd_patterns')) {
            Schema::create('crowd_patterns', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('day_of_week'); // 1 (Senin) - 7 (Minggu)
                $table->unsignedTinyInteger('hour');        // 0 - 23
                $table->unsignedTinyInteger('level')->default(0); // 0 - 100
                $table->timestamps();

                $table->unique(['day_of_week', 'hour']);
            });
        }

        if (! Schema::hasTable('rules')) {
            Schema::create('rules', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body');
                $table->integer('sort')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('page_sections')) {
            Schema::create('page_sections', function (Blueprint $table) {
                $table->id();
                $table->string('page', 50);
                $table->string('key', 50);
                $table->boolean('is_active')->default(true);
                $table->integer('sort')->default(0);
                $table->string('heading')->nullable();
                $table->string('subheading')->nullable();
                $table->text('body')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_url')->nullable();
                $table->json('media')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();

                $table->unique(['page', 'key']);
                $table->index(['page', 'is_active', 'sort']);
            });
        }

        if (! Schema::hasTable('bean_origins')) {
            Schema::create('bean_origins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('region');
                $table->unsignedSmallInteger('altitude'); // mdpl
                $table->text('note')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('reservations', 'payment_type')) {
                $table->string('payment_type', 20)->default('dp')->after('payment_status');
            }
            if (! Schema::hasColumn('reservations', 'amount_due')) {
                $table->unsignedInteger('amount_due')->default(0)->after('dp_amount');
            }
            if (! Schema::hasColumn('reservations', 'amount_paid')) {
                $table->unsignedInteger('amount_paid')->nullable()->after('amount_due');
            }
            if (! Schema::hasColumn('reservations', 'min_spend_snapshot')) {
                $table->unsignedInteger('min_spend_snapshot')->default(0)->after('subtotal');
            }
            if (! Schema::hasColumn('reservations', 'seating_option_id')) {
                $table->foreignId('seating_option_id')->nullable()->after('note')->constrained('seating_options')->nullOnDelete();
            }
            if (! Schema::hasColumn('reservations', 'seating_note')) {
                $table->text('seating_note')->nullable()->after('seating_option_id');
            }
            if (! Schema::hasColumn('reservations', 'source')) {
                $table->string('source', 20)->default('web')->after('mode');
            }
            if (! Schema::hasColumn('reservations', 'payment_rejected_reason')) {
                $table->text('payment_rejected_reason')->nullable()->after('payment_proof_path');
            }
            if (! Schema::hasColumn('reservations', 'reminder_notified_at')) {
                $table->timestamp('reminder_notified_at')->nullable()->after('confirmed_at');
                $table->index('reminder_notified_at');
            }
            if (! Schema::hasColumn('reservations', 'late_notified_at')) {
                $table->timestamp('late_notified_at')->nullable()->after('reminder_notified_at');
            }
            if (! Schema::hasColumn('reservations', 'arrived_at')) {
                $table->timestamp('arrived_at')->nullable()->after('late_notified_at');
            }
            if (! Schema::hasColumn('reservations', 'cancelled_reason')) {
                $table->text('cancelled_reason')->nullable()->after('admin_note');
            }
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            if (! Schema::hasColumn('reservation_items', 'variant_label')) {
                $table->string('variant_label')->nullable()->after('name_snapshot');
            }
            if (! Schema::hasColumn('reservation_items', 'temperature')) {
                $table->string('temperature', 10)->nullable()->after('variant_label');
            }
            if (! Schema::hasColumn('reservation_items', 'unit_price')) {
                $table->unsignedInteger('unit_price')->default(0)->after('price_snapshot');
            }
        });

        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'ingredients')) {
                $table->text('ingredients')->nullable()->after('description');
            }
            if (! Schema::hasColumn('menu_items', 'has_temperature')) {
                $table->boolean('has_temperature')->default(false)->after('ingredients');
            }
            if (! Schema::hasColumn('menu_items', 'variants')) {
                $table->json('variants')->nullable()->after('price');
            }
            if (! Schema::hasColumn('menu_items', 'show_on_home')) {
                $table->boolean('show_on_home')->default(false)->after('is_favorite');
            }
            if (! Schema::hasColumn('menu_items', 'is_placeholder')) {
                $table->boolean('is_placeholder')->default(false)->after('image_path');
            }
            if (! Schema::hasColumn('menu_items', 'image_variants')) {
                $table->json('image_variants')->nullable()->after('is_placeholder');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            foreach (['image_variants', 'is_placeholder', 'show_on_home', 'variants', 'has_temperature', 'ingredients'] as $col) {
                if (Schema::hasColumn('menu_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            foreach (['unit_price', 'temperature', 'variant_label'] as $col) {
                if (Schema::hasColumn('reservation_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'seating_option_id')) {
                $table->dropConstrainedForeignId('seating_option_id');
            }
            if (Schema::hasColumn('reservations', 'reminder_notified_at')) {
                $table->dropIndex(['reminder_notified_at']);
            }
            foreach ([
                'cancelled_reason',
                'arrived_at',
                'late_notified_at',
                'reminder_notified_at',
                'payment_rejected_reason',
                'source',
                'seating_note',
                'min_spend_snapshot',
                'amount_paid',
                'amount_due',
                'payment_type',
            ] as $col) {
                if (Schema::hasColumn('reservations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('bean_origins');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('crowd_patterns');
        Schema::dropIfExists('spend_tiers');
        Schema::dropIfExists('seating_options');
    }
};
