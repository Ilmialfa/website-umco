<?php

namespace App\Filament\Resources\ClosedDates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClosedDateForm
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
                            Section::make('Penetapan Tanggal Tutup / Libur Khusus')
                                ->description('Sistem reservasi otomatis memblokir pemesanan meja pada tanggal ini.')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    DatePicker::make('date')
                                        ->label('Tanggal Tutup')
                                        ->required()
                                        ->unique('closed_dates', 'date', ignoreRecord: true)
                                        ->helperText('Pilih tanggal di kalender.'),

                                    TextInput::make('reason')
                                        ->label('Alasan Libur / Keterangan Penutupan')
                                        ->placeholder('Contoh: Libur Bersama Idul Fitri / Maintenance Listrik')
                                        ->required()
                                        ->helperText('Alasan ini akan diinformasikan ke calon tamu jika mereka memilih tanggal ini.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        Group::make([
                            Section::make('Panduan Kasir')
                                ->description('Dampak terhadap reservasi online.')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Placeholder::make('info_notice')
                                        ->label('Informasi Sistem')
                                        ->content('Slot meja dinonaktifkan otomatis. Semua jam operasional pada tanggal terpilih tidak akan memunculkan slot reservasi di website publik.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
