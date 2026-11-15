<?php

namespace App\Filament\Resources\SeatingOptions;

use App\Filament\Resources\SeatingOptions\Pages\ManageSeatingOptions;
use App\Models\SeatingOption;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class SeatingOptionResource extends Resource
{
    protected static ?string $model = SeatingOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Aturan & Kapasitas';

    protected static ?string $modelLabel = 'Pilihan Tempat';

    protected static ?string $pluralModelLabel = 'Pilihan Area Duduk';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Nama Area / Posisi')
                    ->placeholder('Contoh: Dekat colokan')
                    ->required(),

                TextInput::make('sort')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('sort')->label('Urutan')->sortable(),
                TextColumn::make('label')->label('Nama Area')->weight('bold')->searchable(),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSeatingOptions::route('/'),
        ];
    }
}
