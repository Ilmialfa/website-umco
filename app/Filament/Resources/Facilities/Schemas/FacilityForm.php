<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fasilitas Nyata Kafe')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Nama Fasilitas')
                            ->placeholder('Contoh: Indoor Full AC Sejuk')
                            ->required(),

                        TextInput::make('icon')
                            ->label('Nama Ikon Heroicon')
                            ->default('sparkles')
                            ->helperText('Contoh: sparkles, bolt, wifi, sun, cake, truck, shopping-bag'),

                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif Ditampilkan')
                            ->default(true),
                    ]),
            ]);
    }
}
