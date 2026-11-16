<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            Section::make('Kategori Menu Sajian')
                                ->description('Kelompok kategori untuk memisahkan minuman, makanan, atau cemilan.')
                                ->icon('heroicon-o-tag')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Kategori')
                                        ->placeholder('Contoh: Signature Coffee')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    TextInput::make('slug')
                                        ->label('Slug URL (Otomatis)')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->placeholder('signature-coffee')
                                        ->helperText('Otomatis dibuat oleh sistem dari nama kategori.')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Status Kategori')
                                ->description('Visibilitas di tab menu.')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Kategori Aktif')
                                        ->helperText('Jika dimatikan, seluruh menu di kategori ini disembunyikan.')
                                        ->default(true),

                                    TextInput::make('sort')
                                        ->label('Nomor Urut Tab')
                                        ->numeric()
                                        ->default(0)
                                        ->helperText('Kategori dengan nomor kecil tampil di tab paling kiri.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
