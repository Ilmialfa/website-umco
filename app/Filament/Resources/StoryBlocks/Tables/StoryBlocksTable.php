<?php

namespace App\Filament\Resources\StoryBlocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoryBlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('page')
                    ->label('Halaman')
                    ->badge(),

                TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('bold'),

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
