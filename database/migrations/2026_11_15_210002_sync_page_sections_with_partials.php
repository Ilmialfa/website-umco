<?php

use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        PageSection::where('page', 'home')->where('key', 'rules')->delete();
        PageSection::where('page', 'cerita')->where('key', 'altimeter')->delete();

        PageSection::updateOrCreate(
            ['page' => 'cerita', 'key' => 'stalls'],
            [
                'is_active' => true,
                'sort' => 6,
                'heading' => 'Teman jajan',
                'subheading' => 'Sekitar Halaman Kedai',
                'body' => 'Stan makanan di sekitar halaman UMCO yang bisa dipesan langsung di tempat.',
            ]
        );

        PageSection::updateOrCreate(
            ['page' => 'cerita', 'key' => 'gallery_preview'],
            [
                'is_active' => true,
                'sort' => 7,
                'heading' => 'Suasana dari waktu ke waktu',
                'subheading' => 'Potret Ruang',
                'cta_label' => 'Buka seluruh galeri',
                'cta_url' => '/galeri',
            ]
        );

        PageSection::where('page', 'cerita')
            ->where('key', 'collab')
            ->update(['sort' => 8]);
    }

    public function down(): void
    {
        PageSection::where('page', 'cerita')->whereIn('key', ['stalls', 'gallery_preview'])->delete();
        PageSection::where('page', 'cerita')->where('key', 'collab')->update(['sort' => 7]);
    }
};
