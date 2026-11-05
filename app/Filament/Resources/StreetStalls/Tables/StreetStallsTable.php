<?php

namespace App\Filament\Resources\StreetStalls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class StreetStallsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Stan')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('signature_item')
                    ->label('Menu Jagoan')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('active_hours')
                    ->label('Jam Aktif'),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),

                TextColumn::make('sort')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),
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
