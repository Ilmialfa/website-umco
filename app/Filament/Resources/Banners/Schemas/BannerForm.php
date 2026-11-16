<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        // Main Content (2 cols)
                        Group::make([
                            Section::make('Konten Banner Promosi')
                                ->description('Judul headline, deskripsi promo, tombol aksi, dan visual banner.')
                                ->icon('heroicon-o-megaphone')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul Banner')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->placeholder('Contoh: Promo Kuliah Sore: Diskon 20% Cold Brew'),

                                    Textarea::make('description')
                                        ->label('Deskripsi Singkat')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('Jelaskan promo atau pesan utama secara padat dan menarik.'),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('cta_label')
                                                ->label('Teks Tombol Aksi (CTA)')
                                                ->default('Lihat Selengkapnya')
                                                ->required(),

                                            TextInput::make('cta_url')
                                                ->label('Tautan Tujuan (URL / Halaman)')
                                                ->placeholder('/menu atau https://...')
                                                ->helperText('Bisa link internal seperti /menu, /reservasi, atau URL eksternal.'),
                                        ]),

                                    FileUpload::make('image_path')
                                        ->label('Gambar Banner Promosi')
                                        ->disk('public')
                                        ->directory('banners')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->maxSize(5120)
                                        ->columnSpanFull()
                                        ->helperText('Format JPG / PNG / WebP resolusi tinggi (maks. 5 MB).'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        // Settings Sidebar (1 col)
                        Group::make([
                            Section::make('Jadwal Tayang & Status')
                                ->description('Atur periode aktif banner.')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Aktif Ditampilkan')
                                        ->helperText('Matikan untuk menyembunyikan banner sementara.')
                                        ->default(true),

                                    DateTimePicker::make('starts_at')
                                        ->label('Mulai Tayang')
                                        ->helperText('Kosongkan jika langsung tayang.'),

                                    DateTimePicker::make('ends_at')
                                        ->label('Selesai Tayang')
                                        ->helperText('Kosongkan jika tanpa batas waktu.'),

                                    TextInput::make('sort')
                                        ->label('Urutan Prioritas')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Urutan tampil di slider banner.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
