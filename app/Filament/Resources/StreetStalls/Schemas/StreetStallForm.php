<?php

namespace App\Filament\Resources\StreetStalls\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StreetStallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tenant Stan Kuliner Outdoor')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Stan / Tenant')
                            ->required(),

                        TextInput::make('signature_item')
                            ->label('Menu Jagoan / Signature')
                            ->placeholder('Contoh: Tahu Walik Isi Daging Ayam')
                            ->required(),

                        TextInput::make('active_hours')
                            ->label('Jam Buka / Aktif')
                            ->placeholder('15:00 - 21:00 WIB')
                            ->default('15:00 - 21:00 WIB'),

                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->columnSpanFull()
                            ->rows(3),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
