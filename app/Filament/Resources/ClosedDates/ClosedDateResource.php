<?php

namespace App\Filament\Resources\ClosedDates;

use App\Filament\Resources\ClosedDates\Pages\CreateClosedDate;
use App\Filament\Resources\ClosedDates\Pages\EditClosedDate;
use App\Filament\Resources\ClosedDates\Pages\ListClosedDates;
use App\Filament\Resources\ClosedDates\Schemas\ClosedDateForm;
use App\Filament\Resources\ClosedDates\Tables\ClosedDatesTable;
use App\Models\ClosedDate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClosedDateResource extends Resource
{
    protected static ?string $model = ClosedDate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static string|UnitEnum|null $navigationGroup = 'Operasional Reservasi';

    protected static ?string $modelLabel = 'Tanggal Tutup Khusus';

    protected static ?string $pluralModelLabel = 'Jadwal Libur Khusus';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ClosedDateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClosedDatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClosedDates::route('/'),
            'create' => CreateClosedDate::route('/create'),
            'edit' => EditClosedDate::route('/{record}/edit'),
        ];
    }
}
