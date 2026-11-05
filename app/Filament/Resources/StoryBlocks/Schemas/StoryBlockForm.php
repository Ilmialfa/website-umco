<?php

namespace App\Filament\Resources\StoryBlocks\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StoryBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blok Narasi Editorial')
                    ->columns(2)
                    ->schema([
                        TextInput::make('page')
                            ->label('Halaman')
                            ->default('about')
                            ->required(),

                        TextInput::make('key')
                            ->label('Kunci Pengenal (Key)')
                            ->required()
                            ->helperText('Contoh: philosophy, architecture_h, bean_journey_1'),

                        TextInput::make('title')
                            ->label('Judul Narasi')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('body')
                            ->label('Isi Cerita')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('sort')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
