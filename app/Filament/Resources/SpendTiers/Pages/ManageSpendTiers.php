<?php

namespace App\Filament\Resources\SpendTiers\Pages;

use App\Filament\Resources\SpendTiers\SpendTierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSpendTiers extends ManageRecords
{
    protected static string $resource = SpendTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
