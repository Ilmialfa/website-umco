<?php

namespace App\Console\Commands;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Models\User;
use App\Support\Settings;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Console\Command;

class RemindUpcomingReservations extends Command
{
    protected $signature = 'reservations:remind';

    protected $description = 'Kirim notifikasi pengingat 15 menit sebelum kedatangan dan peringatan keterlambatan tamu.';

    public function handle(): int
    {
        $now = Carbon::now();
        $remindBeforeMinutes = max(5, (int) Settings::get('reservation.remind_before_minutes', 15));
        $graceMinutes = max(5, (int) Settings::get('reservation.grace_minutes', 15));

        $staff = User::query()
            ->whereIn('role', ['owner', 'admin', 'cashier'])
            ->get();

        if ($staff->isEmpty()) {
            return self::SUCCESS;
        }

        // Ambil kandidat reservasi hari ini & besok dengan status pending/confirmed
        $candidates = Reservation::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('date', [$now->copy()->subDay()->format('Y-m-d'), $now->copy()->addDay()->format('Y-m-d')])
            ->get();

        foreach ($candidates as $reservation) {
            $arrivalStr = $reservation->date->format('Y-m-d').' '.substr((string) $reservation->time, 0, 5);
            try {
                $arrivalAt = Carbon::createFromFormat('Y-m-d H:i', $arrivalStr);
            } catch (\Throwable) {
                continue;
            }

            $viewUrl = url("/admin/reservations/{$reservation->id}");
            try {
                $viewUrl = ReservationResource::getUrl('view', ['record' => $reservation]);
            } catch (\Throwable) {
                // Fallback
            }

            // 1. Pengingat sebelum datang (antara sekarang s.d. sekarang + remindBeforeMinutes)
            if (
                $reservation->reminder_notified_at === null
                && $arrivalAt->gte($now)
                && $arrivalAt->lte($now->copy()->addMinutes($remindBeforeMinutes))
            ) {
                $minsLeft = max(1, (int) $now->diffInMinutes($arrivalAt, false));
                $timeFmt = str_replace(':', '.', $arrivalAt->format('H:i'));

                $notif = FilamentNotification::make()
                    ->title("{$minsLeft} menit lagi: {$reservation->name}, {$reservation->party_size} orang, jam {$timeFmt}. Follow up?")
                    ->body("Kode {$reservation->code} · +".$reservation->phone_wa)
                    ->warning()
                    ->actions([
                        Action::make('chat_wa')
                            ->label('Chat WA')
                            ->url(route('admin.wa.redirect', ['reservation' => $reservation->id, 'template' => 'reminder']), shouldOpenInNewTab: true),
                        Action::make('lihat')
                            ->label('Lihat')
                            ->url($viewUrl)
                            ->markAsRead(),
                    ]);

                $notif->sendToDatabase($staff);

                $reservation->update(['reminder_notified_at' => $now]);
            }

            // 2. Peringatan terlambat (5 menit sebelum masa hangus berakhir & belum ditandai tiba)
            $expiryAt = $arrivalAt->copy()->addMinutes($graceMinutes);
            $lateWarnAt = $expiryAt->copy()->subMinutes(min(5, $graceMinutes - 1));

            if (
                $reservation->arrived_at === null
                && $reservation->late_notified_at === null
                && $now->gte($lateWarnAt)
                && $now->lte($expiryAt->copy()->addMinutes(30))
            ) {
                $minsRemaining = max(0, (int) $now->diffInMinutes($expiryAt, false));
                $label = $minsRemaining > 0
                    ? "{$reservation->name} belum datang, karcis hangus {$minsRemaining} menit lagi"
                    : "{$reservation->name} belum datang, melewati batas {$graceMinutes} menit";

                FilamentNotification::make()
                    ->title($label)
                    ->body("Kode {$reservation->code} · Jam pesan ".str_replace(':', '.', substr((string) $reservation->time, 0, 5)))
                    ->danger()
                    ->actions([
                        Action::make('chat_wa')
                            ->label('Chat WA')
                            ->url(route('admin.wa.redirect', ['reservation' => $reservation->id, 'template' => 'reminder']), shouldOpenInNewTab: true),
                        Action::make('lihat')
                            ->label('Lihat')
                            ->url($viewUrl)
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($staff);

                $reservation->update(['late_notified_at' => $now]);
            }
        }

        return self::SUCCESS;
    }
}
