<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->rounded(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('cta_label')
                    ->label('Tombol')
                    ->badge(),

                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Sekarang'),

                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Selamanya'),

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
