<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminWhatsAppRedirectController extends Controller
{
    public function __invoke(Request $request, Reservation $reservation, string $template): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->isAdmin(), 403);

        $dateObj = Carbon::parse($reservation->date);
        $dateObj->locale('id');
        $timeShort = substr((string) $reservation->time, 0, 5);
        $graceMinutes = max(5, (int) Settings::get('reservation.grace_minutes', 15));

        $hangusTime = Carbon::createFromFormat('Y-m-d H:i', $reservation->date->format('Y-m-d').' '.$timeShort)
            ->addMinutes($graceMinutes)
            ->format('H.i');

        $amountPaid = (int) ($reservation->amount_paid ?? 0);
        $shortfall = max(0, (int) $reservation->amount_due - $amountPaid);

        $templateText = match ($template) {
            'confirmation' => (string) Settings::get(
                'whatsapp_templates.confirmation',
                'Halo {nama}, reservasi {kode} untuk {jumlah} orang hari {hari} {tanggal} jam {jam} sudah kami terima dan pembayarannya sudah kami cek. Sampai ketemu di UMCO.'
            ),
            'reminder' => (string) Settings::get(
                'whatsapp_templates.reminder',
                'Halo {nama}, mengingatkan reservasi {kode} jam {jam} hari ini. Mejamu kami tahan sampai {jam_hangus}. Kalau ada perubahan, kabari kami di sini ya.'
            ),
            'follow_up_proof' => (string) Settings::get(
                'whatsapp_templates.follow_up_proof',
                'Halo {nama}, bukti pembayaran untuk {kode} belum terbaca jelas. Boleh kirim ulang lewat link ini: {link_status}'
            ),
            'underpaid' => (string) Settings::get(
                'whatsapp_templates.underpaid',
                'Halo {nama}, nominal yang masuk {nominal_masuk}, kurang {kekurangan} dari DP {kode}.'
            ),
            'cancellation' => (string) Settings::get(
                'whatsapp_templates.cancellation',
                'Halo {nama}, mohon maaf reservasi {kode} terpaksa kami batalkan karena {alasan}. Kami bantu atur ulang kalau kamu mau.'
            ),
            default => '',
        };

        $msg = $templateText === '' ? '' : strtr($templateText, [
            '{nama}' => $reservation->name,
            '{kode}' => $reservation->code,
            '{jumlah}' => (string) $reservation->party_size,
            '{hari}' => $dateObj->isoFormat('dddd'),
            '{tanggal}' => $dateObj->isoFormat('D MMM YYYY'),
            '{jam}' => str_replace(':', '.', $timeShort),
            '{jam_hangus}' => $hangusTime,
            '{total}' => 'Rp '.number_format($reservation->subtotal, 0, ',', '.'),
            '{dp}' => 'Rp '.number_format($reservation->dp_amount, 0, ',', '.'),
            '{nominal_masuk}' => 'Rp '.number_format($amountPaid, 0, ',', '.'),
            '{kekurangan}' => 'Rp '.number_format($shortfall, 0, ',', '.'),
            '{alasan}' => $reservation->cancelled_reason ?: 'penyesuaian jadwal kedai',
            '{link_status}' => route('reservation.status', ['code' => $reservation->code]),
            '{catatan}' => $reservation->note ?? '-',
        ]);

        ActivityLog::record('reservation.whatsapp_opened', Reservation::class, $reservation->id, [
            'code' => $reservation->code,
            'template' => $template,
            'staff_id' => $user->id,
        ]);

        return redirect()->away(PhoneNumber::waLink($reservation->phone_wa, $msg));
    }
}
