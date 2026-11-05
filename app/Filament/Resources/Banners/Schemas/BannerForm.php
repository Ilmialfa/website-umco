<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Banner')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Banner')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('cta_label')
                            ->label('Teks Tombol Aksi')
                            ->default('Lihat Selengkapnya')
                            ->required(),

                        TextInput::make('cta_url')
                            ->label('Tautan Tujuan (URL / Halaman)')
                            ->placeholder('/menu atau https://...')
                            ->helperText('Bisa berupa link internal seperti /menu, /about, atau URL eksternal.'),

                        FileUpload::make('image_path')
                            ->label('Gambar Pendukung')
                            ->disk('public')
                            ->directory('banners')
                            ->image()
                            ->columnSpanFull(),
                    ]),

                Section::make('Jadwal Tayang & Status')
                    ->columns(3)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Mulai Tayang')
                            ->helperText('Kosongkan jika langsung tayang.'),

                        DateTimePicker::make('ends_at')
                            ->label('Selesai Tayang')
                            ->helperText('Kosongkan jika tayang tanpa batas waktu.'),

                        TextInput::make('sort')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
