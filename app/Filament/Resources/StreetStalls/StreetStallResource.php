<?php

namespace App\Filament\Resources\StreetStalls;

use App\Filament\Resources\StreetStalls\Pages\CreateStreetStall;
use App\Filament\Resources\StreetStalls\Pages\EditStreetStall;
use App\Filament\Resources\StreetStalls\Pages\ListStreetStalls;
use App\Filament\Resources\StreetStalls\Schemas\StreetStallForm;
use App\Filament\Resources\StreetStalls\Tables\StreetStallsTable;
use App\Models\StreetStall;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StreetStallResource extends Resource
{
    protected static ?string $model = StreetStall::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Editorial';

    protected static ?string $modelLabel = 'Stan Kuliner';

    protected static ?string $pluralModelLabel = 'Stan Street Food';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return StreetStallForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StreetStallsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStreetStalls::route('/'),
            'create' => CreateStreetStall::route('/create'),
            'edit' => EditStreetStall::route('/{record}/edit'),
        ];
    }
}
