<?php

namespace App\Notifications;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Reservation $reservation) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $dateObj = Carbon::parse($this->reservation->date);
        $dateObj->locale('id');
        $dayLabel = $dateObj->isoFormat('ddd');
        $time = substr((string) $this->reservation->time, 0, 5);
        $title = "Reservasi baru {$this->reservation->code} · {$this->reservation->party_size} orang · {$dayLabel} {$time}";

        $seatingOption = $this->reservation->seatingOption;
        $seating = $seatingOption !== null ? $seatingOption->label : 'Area bebas';
        $body = "{$this->reservation->name} ({$seating}) · Rp ".number_format($this->reservation->subtotal, 0, ',', '.');

        $url = url("/admin/reservations/{$this->reservation->id}");
        try {
            $url = ReservationResource::getUrl('view', ['record' => $this->reservation]);
        } catch (\Throwable) {
            // Fallback ke URL langsung saat berjalan di queue/CLI tanpa panel aktif
        }

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->actions([
                Action::make('lihat')
                    ->label('Lihat')
                    ->url($url)
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
