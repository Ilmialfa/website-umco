<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('role')
                    ->label('Peran / Mahasiswa')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Bintang')
                    ->formatStateUsing(fn ($state) => "{$state} / 5")
                    ->color('warning'),

                TextColumn::make('body')
                    ->label('Isi Ulasan')
                    ->limit(50),

                ToggleColumn::make('is_sample')
                    ->label('Contoh / Sampel'),

                ToggleColumn::make('is_featured')
                    ->label('Unggulan Beranda'),

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
