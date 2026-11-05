<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Aset Galeri Suasana')
                    ->columns(2)
                    ->schema([
                        Select::make('group')
                            ->label('Area Suasana')
                            ->options([
                                'indoor' => 'Indoor (Full AC & Nugas)',
                                'outdoor' => 'Outdoor (Street Food & Santai)',
                                'general' => 'Umum / Gedung H',
                            ])
                            ->default('general')
                            ->required(),

                        Select::make('type')
                            ->label('Tipe Media')
                            ->options([
                                'image' => 'Foto (Gambar)',
                                'video' => 'Video Loop Pendek (MP4/WebM)',
                            ])
                            ->default('image')
                            ->required(),

                        FileUpload::make('image_path')
                            ->label('File Foto / Video')
                            ->disk('public')
                            ->directory('gallery')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('caption')
                            ->label('Keterangan / Alt Text')
                            ->placeholder('Contoh: Suasana meja kerja indoor ber-AC sejuk.')
                            ->columnSpanFull(),

                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
