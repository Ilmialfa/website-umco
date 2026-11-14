<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\Actions\ReservationActions;
use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReservationActions::whatsappGroup(),
            ...ReservationActions::statusActions(),
            EditAction::make(),
        ];
    }
}
