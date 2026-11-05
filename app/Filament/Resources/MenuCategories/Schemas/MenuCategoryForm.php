<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Menu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug URL')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Otomatis dibuat dari nama kategori'),

                        TextInput::make('sort')
                            ->label('Nomor Urut')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
