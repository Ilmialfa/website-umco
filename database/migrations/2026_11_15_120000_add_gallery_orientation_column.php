<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('gallery_items', 'orientation')) {
            Schema::table('gallery_items', function (Blueprint $table) {
                $table->string('orientation', 24)->default('mixed')->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('gallery_items', 'orientation')) {
            Schema::table('gallery_items', function (Blueprint $table) {
                $table->dropColumn('orientation');
            });
        }
    }
};
