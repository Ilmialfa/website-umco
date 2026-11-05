<?php

namespace App\Filament\Resources\StoryBlocks\Pages;

use App\Filament\Resources\StoryBlocks\StoryBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStoryBlock extends EditRecord
{
    protected static string $resource = StoryBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
