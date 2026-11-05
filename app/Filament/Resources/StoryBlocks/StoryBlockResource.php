<?php

namespace App\Filament\Resources\StoryBlocks;

use App\Filament\Resources\StoryBlocks\Pages\CreateStoryBlock;
use App\Filament\Resources\StoryBlocks\Pages\EditStoryBlock;
use App\Filament\Resources\StoryBlocks\Pages\ListStoryBlocks;
use App\Filament\Resources\StoryBlocks\Schemas\StoryBlockForm;
use App\Filament\Resources\StoryBlocks\Tables\StoryBlocksTable;
use App\Models\StoryBlock;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StoryBlockResource extends Resource
{
    protected static ?string $model = StoryBlock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $modelLabel = 'Cerita & Filosofi';

    protected static ?string $pluralModelLabel = 'Narasi Editorial';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return StoryBlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoryBlocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoryBlocks::route('/'),
            'create' => CreateStoryBlock::route('/create'),
            'edit' => EditStoryBlock::route('/{record}/edit'),
        ];
    }
}
