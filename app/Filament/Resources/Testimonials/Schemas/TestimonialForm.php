<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        // Main Content (2 cols)
                        Group::make([
                            Section::make('Kutipan Ulasan Pelanggan')
                                ->description('Kesan nyata dari pengunjung UM.CO House Kampus UMRI.')
                                ->icon('heroicon-o-chat-bubble-left-right')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Nama Pengulas')
                                                ->required()
                                                ->placeholder('Contoh: Fikri Ramadhan'),

                                            TextInput::make('role')
                                                ->label('Status / Peran')
                                                ->placeholder('Contoh: Mahasiswa Fasilkom UMRI')
                                                ->helperText('Bisa identitas kampus atau pengunjung kafe.'),
                                        ]),

                                    Textarea::make('body')
                                        ->label('Isi Ulasan')
                                        ->rows(4)
                                        ->required()
                                        ->columnSpanFull()
                                        ->placeholder('Tuliskan testimoni pelanggan mengenai kenyamanan, kopi, atau suasana kafe.'),

                                    FileUpload::make('avatar_path')
                                        ->label('Foto Profil Pengulas (Opsional)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('testimonials')
                                        ->avatar()
                                        ->helperText('Jika dikosongkan, inisial nama pengulas akan ditampilkan rapi.')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        // Settings Sidebar (1 col)
                        Group::make([
                            Section::make('Penilaian & Visibilitas')
                                ->description('Peringkat bintang dan status publikasi.')
                                ->icon('heroicon-o-star')
                                ->schema([
                                    Select::make('rating')
                                        ->label('Bintang Rating')
                                        ->options([
                                            5 => '5 Bintang (Sempurna)',
                                            4 => '4 Bintang (Sangat Baik)',
                                            3 => '3 Bintang (Cukup)',
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

                                    Toggle::make('is_featured')
                                        ->label('Tampilkan di Beranda')
                                        ->helperText('Tampilkan di section ulasan halaman depan.')
                                        ->default(true),

                                    Toggle::make('is_sample')
                                        ->label('Tandai Sebagai Ulasan Contoh')
                                        ->helperText('Tandai bila ulasan ini bersifat sampel sementara sebelum ulasan pelanggan asli dimasukkan.')
                                        ->default(false),

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
