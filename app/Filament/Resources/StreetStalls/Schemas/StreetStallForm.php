<?php

namespace App\Filament\Resources\StreetStalls\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StreetStallForm
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
                            Section::make('Tenant Stan Kuliner Outdoor')
                                ->description('Informasi stan jajanan & street food mitra kafe.')
                                ->icon('heroicon-o-building-storefront')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Nama Stan / Tenant')
                                                ->placeholder('Contoh: Tahu Walik Crispy UMRI')
                                                ->required(),

                                            TextInput::make('signature_item')
                                                ->label('Menu Jagoan / Signature')
                                                ->placeholder('Contoh: Tahu Walik Sambal Kecap Pedas')
                                                ->required(),
                                        ]),

                                    TextInput::make('active_hours')
                                        ->label('Jam Buka / Jam Operasional')
                                        ->placeholder('15:00 - 21:00 WIB')
                                        ->default('15:00 - 21:00 WIB')
                                        ->columnSpanFull(),

                                    Textarea::make('description')
                                        ->label('Deskripsi Singkat Sajian')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('Ceritakan keunikan cita rasa atau bahan stan ini.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Status Tenant')
                                ->description('Visibilitas di halaman About & Beranda.')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Stan Aktif Beroperasi')
                                        ->helperText('Tampilkan stan kuliner ini di website.')
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
