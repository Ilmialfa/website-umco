<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ReservationCalendarController extends Controller
{
    public function download(string $code): Response
    {
        $reservation = Reservation::with('seatingOption')->where('code', $code)->firstOrFail();

        // Calculate start and end datetime
        $start = Carbon::parse($reservation->date->format('Y-m-d') . ' ' . $reservation->time, 'Asia/Jakarta');
        $end = $start->copy()->addHours(2);

        $dtStart = $start->copy()->utc()->format('Ymd\THis\Z');
        $dtEnd = $end->copy()->utc()->format('Ymd\THis\Z');
        $nowUtc = gmdate('Ymd\THis\Z');

        $seatingLabel = $reservation->seatingOption?->label ?? 'Area bebas';
        $isVerifiedViewer = session()->get("umco_status_verified_{$reservation->code}") === true || auth()->check();
        $guestLabel = $isVerifiedViewer ? $reservation->name : 'Tamu UMCO';

        $ics = "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//UMCO House//Reservasi//ID\r\n" .
            "CALSCALE:GREGORIAN\r\n" .
            "METHOD:PUBLISH\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:{$reservation->code}@umco.house\r\n" .
            "DTSTAMP:{$nowUtc}\r\n" .
            "DTSTART:{$dtStart}\r\n" .
            "DTEND:{$dtEnd}\r\n" .
            "SUMMARY:Reservasi UMCO House ({$reservation->code})\r\n" .
            "DESCRIPTION:Reservasi meja {$reservation->party_size} orang ({$seatingLabel}) atas nama {$guestLabel}. Kode Booking: {$reservation->code}\r\n" .
            "LOCATION:UMCO House, Kampus 2 UMRI, Jl. Tuanku Tambusai No.2, Pekanbaru\r\n" .
            "STATUS:CONFIRMED\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"reservasi-{$reservation->code}.ics\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
