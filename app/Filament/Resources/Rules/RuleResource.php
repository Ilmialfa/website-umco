<?php

namespace App\Filament\Resources\Rules;

use App\Filament\Resources\Rules\Pages\ManageRules;
use App\Models\Rule;
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

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = 'Aturan & Kapasitas';

    protected static ?string $modelLabel = 'Aturan Cafe';

    protected static ?string $pluralModelLabel = 'Aturan Main Cafe';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Aturan')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('body')
                    ->label('Penjelasan Singkat')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('sort')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Daftar ini ditampilkan saat tamu mengklik "Baca aturan" di Langkah 5 (Pembayaran) pada halaman reservasi.')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('sort')->label('No')->sortable(),
                TextColumn::make('title')->label('Aturan')->weight('bold')->searchable(),
                TextColumn::make('body')->label('Penjelasan')->limit(65),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRules::route('/'),
        ];
    }
}
