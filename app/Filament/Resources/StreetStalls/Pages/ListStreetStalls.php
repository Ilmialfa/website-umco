<?php

namespace App\Filament\Resources\StreetStalls\Pages;

use App\Filament\Resources\StreetStalls\StreetStallResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStreetStalls extends ListRecords
{
    protected static string $resource = StreetStallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
