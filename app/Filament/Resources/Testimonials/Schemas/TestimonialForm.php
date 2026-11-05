<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kutipan Ulasan Pelanggan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Pengulas')
                            ->required(),

                        TextInput::make('role')
                            ->label('Status / Peran')
                            ->placeholder('Contoh: Mahasiswa Fasilkom UMRI / Pengunjung Google Maps'),

                        Select::make('rating')
                            ->label('Bintang Rating')
                            ->options([
                                5 => '5 Bintang ★★★★★',
                                4 => '4 Bintang ★★★★☆',
                                3 => '3 Bintang ★★★☆☆',
                            ])
                            ->default(5)
                            ->required(),

                        Select::make('source')
                            ->label('Sumber Ulasan')
                            ->options([
                                'google' => 'Google Maps Review',
                                'manual' => 'Input Manual / Buku Tamu',
                            ])
                            ->default('google')
                            ->required(),

                        Textarea::make('body')
                            ->label('Kutipan Ulasan')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->label('Tampilkan di Beranda')
                            ->default(true),

                        Toggle::make('is_sample')
                            ->label('Tandai Sebagai Contoh (Placeholder)')
                            ->helperText('Jika aktif, akan muncul peringatan di dashboard agar diganti dengan ulasan nyata berizin.')
                            ->default(false),

                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
