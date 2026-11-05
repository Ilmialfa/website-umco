<?php

namespace App\Filament\Resources\GalleryItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Media')
                    ->rounded(),

                TextColumn::make('group')
                    ->label('Area')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'indoor' => 'Indoor AC',
                        'outdoor' => 'Outdoor',
                        default => 'Umum',
                    })
                    ->color(fn ($state) => match ($state) {
                        'indoor' => 'info',
                        'outdoor' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),

                TextColumn::make('caption')
                    ->label('Keterangan')
                    ->limit(40),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),

                TextColumn::make('sort')
                    ->label('Urut')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Filter Area')
                    ->options([
                        'indoor' => 'Indoor',
                        'outdoor' => 'Outdoor',
                        'general' => 'Umum',
                    ]),
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
