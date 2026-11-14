<?php

namespace App\Filament\Resources\SeatingOptions\Pages;

use App\Filament\Resources\SeatingOptions\SeatingOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSeatingOptions extends ManageRecords
{
    protected static string $resource = SeatingOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
