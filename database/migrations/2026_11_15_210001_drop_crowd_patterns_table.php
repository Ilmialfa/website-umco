<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('crowd_patterns');
    }

    public function down(): void
    {
        if (! Schema::hasTable('crowd_patterns')) {
            Schema::create('crowd_patterns', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('day_of_week');
                $table->unsignedTinyInteger('hour');
                $table->unsignedTinyInteger('level')->default(50);
                $table->string('note')->nullable();
                $table->timestamps();
                $table->unique(['day_of_week', 'hour']);
            });
        }
    }
};
