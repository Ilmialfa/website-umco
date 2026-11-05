<?php

namespace App\Filament\Resources\ClosedDates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClosedDatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal Tutup')
                    ->date('l, d F Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('reason')
                    ->label('Alasan / Keterangan')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
