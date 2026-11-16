<?php

namespace App\Filament\Resources\StoryBlocks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StoryBlockForm
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
                            Section::make('Blok Narasi Editorial Kafe')
                                ->description('Teks cerita, filosofi, dan narasi arsitektur.')
                                ->icon('heroicon-o-book-open')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul Narasi / Sub-Headline')
                                        ->placeholder('Contoh: Titik Temu Mahasiswa & Penikmat Kopi')
                                        ->required()
                                        ->columnSpanFull(),

                                    Textarea::make('body')
                                        ->label('Isi Cerita Lengkap')
                                        ->rows(6)
                                        ->required()
                                        ->columnSpanFull()
                                        ->placeholder('Tuliskan paragraf cerita kafe di sini...'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Identifikasi Teknis')
                                ->description('Penempatan halaman dan urutan.')
                                ->icon('heroicon-o-tag')
                                ->schema([
                                    Select::make('page')
                                        ->label('Halaman Target')
                                        ->options([
                                            'cerita' => 'Halaman Cerita (/cerita)',
                                        ])
                                        ->default('cerita')
                                        ->required(),

                                    TextInput::make('key')
                                        ->label('Kunci Pengenal (Key)')
                                        ->required()
                                        ->helperText('Contoh: philosophy, architecture_h, bean_journey_1'),

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
