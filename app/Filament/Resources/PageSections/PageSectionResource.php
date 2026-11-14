<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\PageSections\Pages\ManagePageSections;
use App\Models\PageSection;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PageSectionResource extends Resource
{
    protected static ?string $model = PageSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Section Halaman';

    protected static ?string $pluralModelLabel = 'Section & Urutan Landing';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page')
                    ->label('Halaman')
                    ->options([
                        'home' => 'Beranda (/)',
                        'menu' => 'Menu (/menu)',
                        'cerita' => 'Cerita (/cerita)',
                    ])
                    ->disabledOn('edit')
                    ->required(),

                TextInput::make('key')
                    ->label('Kunci Section')
                    ->disabledOn('edit')
                    ->required(),

                Select::make('bg_token')
                    ->label('Warna Latar Section')
                    ->options([
                        'kertas' => 'Kertas (#F5F2EA)',
                        'mentega' => 'Mentega (#EFC94C)',
                        'tinta' => 'Tinta (#22263A)',
                        'matcha' => 'Matcha (#C8D5AE)',
                        'blush' => 'Susu Stroberi (#F0D2C9)',
                    ])
                    ->default('kertas')
                    ->required(),

                TextInput::make('sort')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->required(),

                TextInput::make('heading')
                    ->label('Judul Section')
                    ->columnSpanFull(),

                TextInput::make('subheading')
                    ->label('Subjudul')
                    ->columnSpanFull(),

                Textarea::make('body')
                    ->label('Paragraf / Isi')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('cta_label')
                    ->label('Teks Tombol CTA'),

                TextInput::make('cta_url')
                    ->label('Tautan Tombol CTA'),

                Toggle::make('is_active')
                    ->label('Tampilkan di Website')
                    ->default(true)
                    ->columnSpanFull(),

                KeyValue::make('data')
                    ->label('Data Khusus Section (key-value)')
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
                TextColumn::make('page')
                    ->label('Halaman')
                    ->badge()
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Kunci')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('heading')
                    ->label('Judul')
                    ->limit(42)
                    ->searchable(),

                TextColumn::make('bg_token')
                    ->label('Latar')
                    ->badge()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Tayang'),

                TextColumn::make('sort')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('page')
                    ->label('Halaman')
                    ->options([
                        'home' => 'Beranda',
                        'menu' => 'Menu',
                        'cerita' => 'Cerita',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePageSections::route('/'),
        ];
    }
}
