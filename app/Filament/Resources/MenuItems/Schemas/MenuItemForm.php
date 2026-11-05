<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk & Harga')
                    ->columns(2)
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori Menu')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Menu')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Harga (Rupiah)')
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->helperText('Contoh: 28000 (akan diformat Rp 28.000)'),

                        FileUpload::make('image_path')
                            ->label('Foto Menu')
                            ->disk('public')
                            ->directory('menu')
                            ->image()
                            ->helperText('Opsional. Tanpa foto menu tetap tampil estetik.'),

                        Textarea::make('description')
                            ->label('Deskripsi Rasa / Bahan')
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Jelaskan karakter rasa, bahan utama, atau sensasi minuman/makanan ini.'),
                    ]),

                Section::make('Status & Penandaan Unggulan')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_favorite')
                            ->label('Jadikan Menu Favorit / Best Seller')
                            ->helperText('Akan ditonjolkan di bagian atas dan beranda')
                            ->default(false),

                        TextInput::make('favorite_order')
                            ->label('Prioritas Unggulan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Nomor lebih kecil tampil lebih awal (1, 2, 3...)'),

                        Toggle::make('is_new')
                            ->label('Label Menu Baru (New)')
                            ->default(false),

                        Toggle::make('is_available')
                            ->label('Tersedia (Bukan Sold Out)')
                            ->helperText('Jika nonaktif, menu berlabel "Habis" dan tidak bisa dipesan di reservasi')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('Tampilkan di Website')
                            ->default(true),

                        TextInput::make('sort')
                            ->label('Nomor Urut Kategori')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
