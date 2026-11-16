<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Galeri, Testimoni & FAQ';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'Pertanyaan Umum (FAQ)';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->label('Pertanyaan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('answer')
                    ->label('Jawaban')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'reservasi' => 'Reservasi & Pembayaran',
                        'menu' => 'Menu & Cafe',
                        'kolaborasi' => 'Acara & Kolaborasi',
                        'umum' => 'Umum',
                    ])
                    ->default('umum')
                    ->required(),

                TextInput::make('sort')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Tampilkan di Beranda')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->limit(55)
                    ->weight('semibold'),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),

                ToggleColumn::make('is_active')
                    ->label('Tayang'),

                TextColumn::make('sort')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqs::route('/'),
        ];
    }
}
