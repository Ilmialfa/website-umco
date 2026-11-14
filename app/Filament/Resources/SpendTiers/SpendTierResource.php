<?php

namespace App\Filament\Resources\SpendTiers;

use App\Filament\Resources\SpendTiers\Pages\ManageSpendTiers;
use App\Models\SpendTier;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SpendTierResource extends Resource
{
    protected static ?string $model = SpendTier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Audit';

    protected static ?string $modelLabel = 'Minimal Belanja';

    protected static ?string $pluralModelLabel = 'Tabel Minimal Belanja';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('party_min')
                    ->label('Minimal Orang')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('party_max')
                    ->label('Maksimal Orang (kosongkan jika terbuka ke atas)')
                    ->numeric()
                    ->nullable(),

                TextInput::make('min_spend')
                    ->label('Minimal Belanja Dasar (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                TextInput::make('per_extra_person')
                    ->label('Tambahan per Orang di atas Minimal (opsional)')
                    ->numeric()
                    ->prefix('Rp')
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('party_min')
            ->columns([
                TextColumn::make('party_min')->label('Dari (orang)')->sortable(),
                TextColumn::make('party_max')->label('Sampai (orang)')->placeholder('Ke atas'),
                TextColumn::make('min_spend')->label('Minimal Belanja')->money('IDR'),
                TextColumn::make('per_extra_person')->label('+ / Orang Tambahan')->money('IDR')->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSpendTiers::route('/'),
        ];
    }
}
