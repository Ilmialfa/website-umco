<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tidak ada duplikat pasangan (group, key) sebelum memasang indeks unik komposit
        $duplicates = DB::table('settings')
            ->select('group', 'key', DB::raw('MAX(id) as keep_id'))
            ->groupBy('group', 'key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('settings')
                ->where('group', $dup->group)
                ->where('key', $dup->key)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        try {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_unique');
            });
        } catch (Throwable) {
            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique(['key']);
                });
            } catch (Throwable) {
                // Indeks lama mungkin sudah dilepas pada lingkungan tertentu
            }
        }

        try {
            Schema::table('settings', function (Blueprint $table) {
                $table->unique(['group', 'key'], 'settings_group_key_unique');
            });
        } catch (Throwable) {
            // Lewati bila indeks unik komposit sudah ada
        }
    }

    public function down(): void
    {
        try {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_group_key_unique');
            });
        } catch (Throwable) {
        }

        try {
            Schema::table('settings', function (Blueprint $table) {
                $table->unique('key', 'settings_key_unique');
            });
        } catch (Throwable) {
        }
    }
};
