<?php

namespace App\Filament\Resources\BeanOrigins;

use App\Filament\Resources\BeanOrigins\Pages\ManageBeanOrigins;
use App\Models\BeanOrigin;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BeanOriginResource extends Resource
{
    protected static ?string $model = BeanOrigin::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAsiaAustralia;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Asal Biji Kopi';

    protected static ?string $pluralModelLabel = 'Asal Biji Kopi (Altimeter)';

    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Biji / Daerah')
                    ->placeholder('Contoh: Gayo Pantan Musara')
                    ->required(),

                TextInput::make('region')
                    ->label('Asal Wilayah')
                    ->placeholder('Contoh: Aceh Tengah')
                    ->required(),

                TextInput::make('altitude')
                    ->label('Ketinggian (mdpl)')
                    ->numeric()
                    ->suffix('mdpl')
                    ->required(),

                TextInput::make('sort')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Textarea::make('note')
                    ->label('Catatan Rasa / Deskripsi')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('sort')->label('No')->sortable(),
                TextColumn::make('name')->label('Nama Biji')->weight('bold')->searchable(),
                TextColumn::make('region')->label('Wilayah')->searchable(),
                TextColumn::make('altitude')->label('Ketinggian')->suffix(' mdpl')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBeanOrigins::route('/'),
        ];
    }
}
