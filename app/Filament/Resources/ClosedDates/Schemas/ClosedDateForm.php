<?php

namespace App\Filament\Resources\ClosedDates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClosedDateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tanggal Tutup / Libur Khusus')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal Tutup')
                            ->required()
                            ->unique('closed_dates', 'date', ignoreRecord: true),

                        TextInput::make('reason')
                            ->label('Alasan Libur / Keterangan')
                            ->placeholder('Contoh: Libur Idul Fitri / Renovasi Lantai 2 / Acara Kampus')
                            ->required(),
                    ]),
            ]);
    }
}
