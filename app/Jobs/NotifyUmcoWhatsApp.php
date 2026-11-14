<?php

namespace App\Jobs;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Services\WhatsApp\WhatsAppManager;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyUmcoWhatsApp implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $reservationId)
    {
    }

    public function handle(): void
    {
        $reservation = Reservation::with(['items', 'seatingOption'])->find($this->reservationId);
        if (! $reservation) {
            return;
        }

        $targetWa = PhoneNumber::normalizeBusiness(
            (string) Settings::get('contact.business_wa', '02248001571')
        );

        if ($targetWa === '') {
            Log::warning('NotifyUmcoWhatsApp: nomor WA bisnis belum diatur.');
            return;
        }

        $message = self::buildMessage($reservation);

        try {
            WhatsAppManager::gateway()->send($targetWa, $message);
        } catch (\Throwable $e) {
            Log::warning('NotifyUmcoWhatsApp gagal mengirim pesan', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function buildMessage(Reservation $reservation): string
    {
        $reservation->loadMissing(['items', 'seatingOption']);

        $template = (string) Settings::get(
            'whatsapp_templates.new_reservation',
            "Reservasi baru {kode}\nNama: {nama} ({wa})\nWaktu: {hari}, {tanggal} jam {jam}\nOrang: {jumlah}\nTempat: {tempat}\nPesanan: {pesanan}\nTotal: {total} | Bayar: {jenis_bayar} {nominal_bayar}\nCek bukti: {link_admin}"
        );

        $date = Carbon::parse($reservation->date)->locale('id');
        $itemsSummary = $reservation->items
            ->map(function ($item): string {
                $variant = $item->variant_label ? " ({$item->variant_label})" : '';
                $temp = $item->temperature ? " [{$item->temperature}]" : '';

                return "{$item->qty}x {$item->name_snapshot}{$variant}{$temp}";
            })
            ->implode(', ');

        if ($itemsSummary === '') {
            $itemsSummary = '-';
        }

        $seating = $reservation->seatingOption?->label ?? 'Bebas di mana saja';
        if (! empty($reservation->seating_note)) {
            $seating .= " ({$reservation->seating_note})";
        }

        $adminUrl = url("/admin/reservations/{$reservation->id}");
        try {
            $adminUrl = ReservationResource::getUrl('view', ['record' => $reservation]);
        } catch (\Throwable) {
            // Gunakan fallback path
        }

        $payTypeLabel = match ($reservation->payment_type) {
            'full' => 'Lunas',
            'cash' => 'Tunai di kasir',
            default => 'DP ' . max(50, (int) Settings::get('reservation.dp_percentage', 50)) . '%',
        };

        $replacements = [
            '{kode}' => $reservation->code,
            '{nama}' => $reservation->name,
            '{wa}' => '+' . ltrim((string) $reservation->phone_wa, '+'),
            '{hari}' => $date->isoFormat('dddd'),
            '{tanggal}' => $date->isoFormat('D MMM YYYY'),
            '{jam}' => substr((string) $reservation->time, 0, 5),
            '{jumlah}' => (string) $reservation->party_size . ' orang',
            '{tempat}' => $seating,
            '{pesanan}' => $itemsSummary,
            '{total}' => 'Rp ' . number_format($reservation->subtotal, 0, ',', '.'),
            '{jenis_bayar}' => $payTypeLabel,
            '{nominal_bayar}' => 'Rp ' . number_format($reservation->amount_due, 0, ',', '.'),
            '{link_admin}' => $adminUrl,
        ];

        return strtr($template, $replacements);
    }

    public static function waLink(Reservation $reservation): string
    {
        $targetWa = PhoneNumber::normalizeBusiness(
            (string) Settings::get('contact.business_wa', '02248001571')
        );

        return 'https://wa.me/' . $targetWa . '?text=' . rawurlencode(self::buildMessage($reservation));
    }
}
