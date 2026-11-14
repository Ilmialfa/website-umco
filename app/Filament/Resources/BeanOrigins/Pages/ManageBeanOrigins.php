<?php

namespace App\Filament\Resources\BeanOrigins\Pages;

use App\Filament\Resources\BeanOrigins\BeanOriginResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBeanOrigins extends ManageRecords
{
    protected static string $resource = BeanOriginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
