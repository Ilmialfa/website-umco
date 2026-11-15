<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make([
                            Section::make('Aset Galeri Suasana Kedai')
                                ->description('Dokumentasi visual suasana kedai, kopi, bar seduh, dan kegiatan.')
                                ->icon('heroicon-o-photo')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Select::make('group')
                                                ->label('Tag / Kategori Foto')
                                                ->options([
                                                    'umum' => 'Umum',
                                                    'suasana' => 'Suasana Kedai',
                                                    'kopi' => 'Kopi & Bar',
                                                    'makanan' => 'Makanan & Camilan',
                                                    'komunitas' => 'Komunitas & Acara',
                                                ])
                                                ->default('umum')
                                                ->required(),

                                            Select::make('type')
                                                ->label('Tipe Media')
                                                ->options([
                                                    'image' => 'Foto (Gambar)',
                                                    'video' => 'Video Loop Pendek (MP4)',
                                                ])
                                                ->default('image')
                                                ->required(),

                                            Select::make('orientation')
                                                ->label('Penempatan Galeri Beranda')
                                                ->options([
                                                    'portrait' => 'Galeri Portrait Infinite (Rasio 3:4)',
                                                    'diagonal' => 'Galeri Diagonal Campuran (Semua Rasio)',
                                                    'mixed' => 'Tampil di Keduanya (Otomatis)',
                                                ])
                                                ->default('mixed')
                                                ->required()
                                                ->columnSpanFull(),
                                        ]),

                                    FileUpload::make('image_path')
                                        ->label('Berkas Foto atau Video Loop')
                                        ->disk('public')
                                        ->directory('gallery')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4'])
                                        ->maxSize(12288)
                                        ->required()
                                        ->columnSpanFull()
                                        ->helperText('Format JPG, PNG, WebP, atau video MP4 ringan (maks. 12 MB). SVG/skrip ditolak otomatis.'),

                                    TextInput::make('caption')
                                        ->label('Keterangan / Teks Foto (Caption)')
                                        ->placeholder('Contoh: Meja panjang untuk diskusi dan laptopan di dekat bar.')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Visibilitas')
                                ->description('Status tayang galeri.')
                                ->icon('heroicon-o-eye')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Aktif Ditampilkan')
                                        ->helperText('Tampilkan foto/video ini di galeri publik.')
                                        ->default(true),

                                    TextInput::make('sort')
                                        ->label('Urutan Tampil')
                                        ->numeric()
                                        ->default(0),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
