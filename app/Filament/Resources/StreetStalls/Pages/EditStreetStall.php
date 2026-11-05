<?php

namespace App\Filament\Resources\StreetStalls\Pages;

use App\Filament\Resources\StreetStalls\StreetStallResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStreetStall extends EditRecord
{
    protected static string $resource = StreetStallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
