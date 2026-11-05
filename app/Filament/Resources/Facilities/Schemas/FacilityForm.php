<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make([
                            Section::make('Fasilitas Nyata Kafe')
                                ->description('Keunggulan fisik yang tersedia di lokasi UM.CO House Kampus UMRI.')
                                ->icon('heroicon-o-sparkles')
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Nama Fasilitas')
                                        ->placeholder('Contoh: Indoor Full AC Sejuk & Dingin')
                                        ->required()
                                        ->columnSpanFull(),

                                    TextInput::make('icon')
                                        ->label('Nama Ikon Heroicon')
                                        ->default('sparkles')
                                        ->helperText('Contoh: sparkles, bolt, wifi, sun, cake, truck, shopping-bag')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Status & Urutan')
                                ->description('Visibilitas di halaman About.')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Aktif Ditampilkan')
                                        ->helperText('Tampilkan di daftar fasilitas website.')
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
