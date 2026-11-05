<?php

namespace App\Filament\Resources\StoryBlocks\Pages;

use App\Filament\Resources\StoryBlocks\StoryBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoryBlocks extends ListRecords
{
    protected static string $resource = StoryBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
