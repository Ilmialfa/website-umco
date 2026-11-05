<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        // Left / Main Content (2 cols)
                        Group::make([
                            Section::make('Informasi Produk & Harga')
                                ->description('Detail nama menu, kategori sajian, harga jual, dan foto sajian.')
                                ->icon('heroicon-o-cake')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Menu Lengkap')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Contoh: Coconut Pandan Cold Brew')
                                        ->columnSpanFull(),

                                    Grid::make(2)
                                        ->schema([
                                            Select::make('category_id')
                                                ->label('Kategori Menu')
                                                ->relationship('category', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            TextInput::make('price')
                                                ->label('Harga (Rupiah)')
                                                ->prefix('Rp')
                                                ->numeric()
                                                ->required()
                                                ->placeholder('34000')
                                                ->helperText('Contoh: 34000 (akan diformat Rp 34.000).'),
                                        ]),

                                    Textarea::make('description')
                                        ->label('Deskripsi Rasa / Karakter Menu')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('Jelaskan karakter rasa, perpaduan bahan utama, atau sensasi minuman/makanan ini.'),

                                    FileUpload::make('image_path')
                                        ->label('Foto Menu Asli')
                                        ->disk('public')
                                        ->directory('menu')
                                        ->image()
                                        ->columnSpanFull()
                                        ->helperText('Format JPG, PNG, atau WebP. Jika dikosongkan, kartu menu otomatis menggunakan visual estetik UMCO.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        // Right / Sidebar (1 col)
                        Group::make([
                            Section::make('Status Ketersediaan')
                                ->description('Kontrol stok kasir & visibilitas website.')
                                ->icon('heroicon-o-check-badge')
                                ->schema([
                                    Toggle::make('is_available')
                                        ->label('Tersedia (Bukan Sold Out)')
                                        ->helperText('Jika dinonaktifkan, menu diberi tanda "Habis" dan tidak bisa dipesan di reservasi.')
                                        ->default(true),

                                    Toggle::make('is_active')
                                        ->label('Tampilkan di Website Publik')
                                        ->helperText('Sembunyikan menu dari katalog website jika dinonaktifkan.')
                                        ->default(true),

                                    TextInput::make('sort')
                                        ->label('Nomor Urut Tampil')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Urutan tampil dalam kategori.'),
                                ]),

                            Section::make('Highlight & Rekomendasi')
                                ->description('Promosi menu unggulan di beranda.')
                                ->icon('heroicon-o-sparkles')
                                ->schema([
                                    Toggle::make('is_favorite')
                                        ->label('Menu Favorit / Signature')
                                        ->helperText('Ditandai sebagai sajian rekomendasi barista.')
                                        ->default(false),

                                    TextInput::make('favorite_order')
                                        ->label('Prioritas Unggulan')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Nomor lebih kecil tampil lebih awal (1, 2, 3...).'),

                                    Toggle::make('is_new')
                                        ->label('Badge Menu Baru (New)')
                                        ->helperText('Menampilkan badge penanda kreasi baru.')
                                        ->default(false),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
