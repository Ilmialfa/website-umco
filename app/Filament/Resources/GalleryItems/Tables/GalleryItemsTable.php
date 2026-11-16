<?php

namespace App\Filament\Resources\GalleryItems\Tables;

use App\Models\GalleryItem;
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
                ImageColumn::make('preview_media')
                    ->label('Media')
                    ->rounded()
                    ->getStateUsing(function (GalleryItem $record): string {
                        $path = (string) ($record->image_path ?? '');
                        if ($path === '' || $record->type === 'video') {
                            return asset('/images/menu-placeholder.svg');
                        }

                        return str_starts_with($path, 'http') || str_starts_with($path, '/')
                            ? asset($path)
                            : asset('storage/'.ltrim($path, '/'));
                    })
                    ->defaultImageUrl(asset('/images/menu-placeholder.svg')),

                TextColumn::make('group')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'kopi' => 'Kopi & Bar',
                        'makanan' => 'Makanan & Camilan',
                        'suasana' => 'Suasana Kedai',
                        'komunitas' => 'Komunitas & Acara',
                        default => 'Umum',
                    })
                    ->color(fn ($state) => match ($state) {
                        'kopi' => 'success',
                        'makanan' => 'warning',
                        'suasana' => 'info',
                        'komunitas' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('orientation')
                    ->label('Penempatan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'galeri_only' => 'Hanya /galeri',
                        'diagonal' => 'Beranda & Galeri',
                        default => 'Otomatis',
                    })
                    ->color(fn ($state) => $state === 'galeri_only' ? 'gray' : 'success'),

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
                    ->label('Filter Kategori')
                    ->options([
                        'umum' => 'Umum',
                        'suasana' => 'Suasana Kedai',
                        'kopi' => 'Kopi & Bar',
                        'makanan' => 'Makanan & Camilan',
                        'komunitas' => 'Komunitas & Acara',
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
