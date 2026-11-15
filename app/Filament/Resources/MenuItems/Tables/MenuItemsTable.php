<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Models\MenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('favorite_order', 'asc')
            ->columns([
                ImageColumn::make('preview_image')
                    ->label('Foto')
                    ->circular()
                    ->getStateUsing(fn (MenuItem $record): string => $record->imageUrlFor(480) ?? asset('/images/menu-placeholder.svg'))
                    ->defaultImageUrl(asset('/images/menu-placeholder.svg')),

                TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                ToggleColumn::make('is_available')
                    ->label('Tersedia'),

                ToggleColumn::make('is_favorite')
                    ->label('Favorit')
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false)),

                TextColumn::make('favorite_order')
                    ->label('Urut Favorit')
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('is_new')
                    ->label('Baru')
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false)),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false)),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                TernaryFilter::make('is_available')
                    ->label('Ketersediaan')
                    ->placeholder('Semua')
                    ->trueLabel('Tersedia Saja')
                    ->falseLabel('Habis (Sold Out)'),

                TernaryFilter::make('is_favorite')
                    ->label('Menu Favorit')
                    ->placeholder('Semua')
                    ->trueLabel('Favorit / Unggulan')
                    ->falseLabel('Menu Biasa'),
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
