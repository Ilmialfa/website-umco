<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\PageSections\Pages\ManagePageSections;
use App\Models\PageSection;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

    protected static ?string $navigationLabel = 'Section & Urutan';

    protected static ?int $navigationSort = 1;

    /**
     * Daftar resmi kunci section per halaman yang benar-benar dirender oleh template Blade.
     *
     * @var array<string, list<string>>
     */
    public const RENDERED_KEYS = [
        'home' => [
            'hero',
            'promo_banner',
            'marquee',
            'signature_reel',
            'karcis_teaser',
            'gallery_diagonal',
            'faq',
            'testimonials',
            'instagram_feed',
            'cta_footer',
        ],
        'menu' => ['hero'],
        'galeri' => ['hero'],
        'cerita' => [
            'hero',
            'history',
            'founder',
            'values',
            'facilities',
            'stalls',
            'gallery_preview',
            'collab',
        ],
    ];

    public static function isRendered(PageSection $record): bool
    {
        return in_array($record->key, self::RENDERED_KEYS[$record->page] ?? [], true);
    }

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
                        'galeri' => 'Galeri (/galeri)',
                        'cerita' => 'Cerita (/cerita)',
                    ])
                    ->disabledOn('edit')
                    ->required(),

                TextInput::make('key')
                    ->label('Kunci Section')
                    ->helperText('Sesuai daftar section aktif pada halaman terkait.')
                    ->disabledOn('edit')
                    ->required(),

                TextInput::make('sort')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->required(),

                Toggle::make('is_active')
                    ->label('Tampilkan di Website')
                    ->default(true),

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
                    ->label('Teks Tombol Utama'),

                TextInput::make('cta_url')
                    ->label('Tautan Tombol Utama'),

                TextInput::make('data.eyebrow')
                    ->label('Label Kecil Atas / Eyebrow (Opsional)')
                    ->placeholder('Contoh: Menu Andalan'),

                TextInput::make('data.hand_note')
                    ->label('Catatan Tulisan Tangan (Opsional)')
                    ->placeholder('Contoh: ~ seduhan hangat & dingin'),

                TextInput::make('data.secondary_label')
                    ->label('Teks Tombol Kedua (Opsional)'),

                TextInput::make('data.secondary_url')
                    ->label('Tautan Tombol Kedua (Opsional)'),

                FileUpload::make('media.desktop')
                    ->label('Gambar Utama Section (Opsional)')
                    ->image()
                    ->disk('public')
                    ->directory('sections')
                    ->helperText('Dipakai di halaman Cerita (hero, sejarah, foto owner, kolaborasi) maupun section lain yang mendukung foto.')
                    ->columnSpanFull(),

                KeyValue::make('data')
                    ->label('Data Tambahan Lainnya (key-value)')
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'home' => 'Beranda',
                        'menu' => 'Menu',
                        'galeri' => 'Galeri',
                        'cerita' => 'Cerita',
                        default => ucfirst($state),
                    })
                    ->color('info')
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Kunci')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('heading')
                    ->label('Judul')
                    ->limit(42)
                    ->searchable(),

                TextColumn::make('status_render')
                    ->label('Status Render')
                    ->state(fn (PageSection $record): string => self::isRendered($record) ? 'Aktif di Halaman' : 'Tidak Dipakai di Halaman')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Aktif di Halaman' ? 'success' : 'warning'),

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
                        'galeri' => 'Galeri',
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
