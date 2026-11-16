<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Support\PhoneNumber;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['phone_input'])) {
            $rawPhone = (string) $data['phone_input'];
            try {
                $data['phone_wa'] = PhoneNumber::normalize($rawPhone);
            } catch (\InvalidArgumentException) {
                $data['phone_wa'] = preg_replace('/\D+/', '', $rawPhone) ?: $rawPhone;
            }
        }

        if (($data['status'] ?? null) === 'confirmed' && empty($data['confirmed_at'])) {
            $data['confirmed_at'] = now();
        }

        return $data;
    }
}
