<?php

namespace App\Actions\Reservations;

use App\Jobs\NotifyUmcoWhatsApp;
use App\Models\ActivityLog;
use App\Models\ClosedDate;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\SeatingOption;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use App\Support\IndonesianFormat;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateReservation
{
    /**
     * Validasi aturan bisnis & buat reservasi dalam satu transaksi DB.
     * Dipakai oleh halaman publik `/reservasi` maupun wizard admin Filament.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public static function execute(array $data, ?UploadedFile $proofFile = null, ?User $actor = null): Reservation
    {
        $source = (string) ($data['source'] ?? 'web');
        if (! in_array($source, ['web', 'walk_in', 'whatsapp', 'phone'], true)) {
            $source = 'web';
        }

        $isAdminSource = $source !== 'web';
        $skipMinRules = (bool) ($data['skip_min_rules'] ?? false);

        if ($skipMinRules && (! $actor || ! $actor->isOwner())) {
            throw ValidationException::withMessages([
                'skip_min_rules' => 'Hanya owner yang boleh melewati aturan minimal.',
            ]);
        }

        $minParty = max(2, (int) Settings::get('reservation.min_party', 2));
        $maxPartyOnline = max($minParty, (int) Settings::get('reservation.max_party_size_online', 12));
        $capacityPerSlot = max(1, (int) Settings::get('reservation.capacity_per_slot', 40));

        $partySize = (int) ($data['party_size'] ?? 0);
        if (! $skipMinRules && $partySize < $minParty) {
            throw ValidationException::withMessages([
                'party_size' => "Reservasi minimal {$minParty} orang ya.",
            ]);
        }
        if ($partySize < 1) {
            throw ValidationException::withMessages([
                'party_size' => 'Jumlah orang minimal 1.',
            ]);
        }
        if (! $isAdminSource && $partySize > $maxPartyOnline) {
            throw ValidationException::withMessages([
                'party_size' => "Reservasi online maksimal {$maxPartyOnline} orang. Lebih dari itu langsung chat WhatsApp kami ya.",
            ]);
        }
        if ($partySize > $capacityPerSlot) {
            throw ValidationException::withMessages([
                'party_size' => "Jumlah orang melebihi kapasitas maksimal {$capacityPerSlot} kursi per slot.",
            ]);
        }

        // Validasi tanggal & hari tutup
        $dateRaw = trim((string) ($data['date'] ?? ''));
        try {
            $dateObj = Carbon::createFromFormat('Y-m-d', $dateRaw)->startOfDay();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal kedatangan belum valid.',
            ]);
        }

        $closed = ClosedDate::whereDate('date', $dateObj->format('Y-m-d'))->first();
        if ($closed) {
            $reason = $closed->reason ? " ({$closed->reason})" : '';
            throw ValidationException::withMessages([
                'date' => "Maaf, UMCO tutup di tanggal tersebut{$reason}.",
            ]);
        }

        $now = Carbon::now();
        $maxDays = max(1, (int) Settings::get('reservation.max_advance_days', 14));
        if (! $isAdminSource) {
            if ($dateObj->lt($now->copy()->startOfDay()) || $dateObj->gt($now->copy()->startOfDay()->addDays($maxDays))) {
                throw ValidationException::withMessages([
                    'date' => "Tanggal reservasi harus antara hari ini sampai {$maxDays} hari ke depan.",
                ]);
            }
        }

        // Validasi jam & advance minutes
        $timeRaw = substr(trim((string) ($data['time'] ?? '')), 0, 5);
        if (! preg_match('/^\d{2}:\d{2}$/', $timeRaw)) {
            throw ValidationException::withMessages([
                'time' => 'Pilih jam kedatangan dulu ya.',
            ]);
        }

        $slotDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateObj->format('Y-m-d').' '.$timeRaw);
        $minAdvanceMinutes = max(0, (int) Settings::get('reservation.min_advance_minutes', 60));

        if (! $isAdminSource && $slotDateTime->lt($now->copy()->addMinutes($minAdvanceMinutes))) {
            throw ValidationException::withMessages([
                'time' => "Pemesanan minimal {$minAdvanceMinutes} menit sebelum jam datang.",
            ]);
        }

        $openStr = (string) Settings::get('hours.opening_time', '08:00');
        $closeStr = (string) Settings::get('hours.closing_time', '22:00');
        $lastBeforeClose = max(0, (int) Settings::get('reservation.last_slot_before_close', 60));

        $openTime = Carbon::createFromFormat('Y-m-d H:i', $dateObj->format('Y-m-d').' '.substr($openStr, 0, 5));
        $lastSlot = Carbon::createFromFormat('Y-m-d H:i', $dateObj->format('Y-m-d').' '.substr($closeStr, 0, 5))
            ->subMinutes($lastBeforeClose);

        if ($slotDateTime->lt($openTime) || $slotDateTime->gt($lastSlot)) {
            throw ValidationException::withMessages([
                'time' => 'Jam tersebut di luar jam slot reservasi UMCO.',
            ]);
        }

        // Validasi nama & WA pelanggan
        $name = trim((string) ($data['name'] ?? ''));
        if (mb_strlen($name) < 2) {
            throw ValidationException::withMessages([
                'name' => 'Isi nama kamu minimal 2 huruf ya.',
            ]);
        }

        $phoneInput = trim((string) ($data['phone_input'] ?? $data['phone'] ?? ''));
        try {
            $phoneWa = PhoneNumber::normalize($phoneInput);
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'phone_input' => 'Nomor WhatsApp belum valid. Contoh: 082212345678.',
            ]);
        }

        // Pilihan tempat
        $seatingOptionId = ! empty($data['seating_option_id']) ? (int) $data['seating_option_id'] : null;
        if ($seatingOptionId !== null && ! SeatingOption::where('id', $seatingOptionId)->exists()) {
            $seatingOptionId = null;
        }

        // Tipe pembayaran
        $paymentType = (string) ($data['payment_type'] ?? 'dp');
        if (! in_array($paymentType, ['dp', 'full', 'cash'], true)) {
            $paymentType = 'dp';
        }
        if ($paymentType === 'cash' && ! $isAdminSource) {
            throw ValidationException::withMessages([
                'payment_type' => 'Reservasi online perlu bayar DP atau Lunas.',
            ]);
        }
        if ($paymentType === 'full' && ! (bool) Settings::get('reservation.full_payment_enabled', true)) {
            $paymentType = 'dp';
        }

        // Hitung menu & minimal belanja di server
        $rawItems = is_array($data['items'] ?? null) ? $data['items'] : [];
        $quote = QuoteReservation::calculate($partySize, $rawItems, $paymentType);

        $menuRequired = (bool) Settings::get('reservation.menu_required', true);
        if (! $skipMinRules && $menuRequired) {
            if (empty($quote['items'])) {
                throw ValidationException::withMessages([
                    'items' => 'Pilih minimal satu menu dulu ya.',
                ]);
            }
            if (! $quote['meets_min_spend']) {
                $minFormatted = IndonesianFormat::rupiah($quote['min_spend']);
                $shortFormatted = IndonesianFormat::rupiah($quote['shortfall']);
                throw ValidationException::withMessages([
                    'items' => "Minimal belanja untuk {$partySize} orang adalah {$minFormatted} (kurang {$shortFormatted}).",
                ]);
            }
        }

        // Validasi & simpan bukti bayar
        $existingProofPath = isset($data['payment_proof_path']) ? trim((string) $data['payment_proof_path']) : '';
        $proofPath = $existingProofPath !== '' ? $existingProofPath : null;

        if ($paymentType !== 'cash' && ! $proofFile && $proofPath === null) {
            throw ValidationException::withMessages([
                'payment_proof' => 'Unggah bukti bayar dulu sebelum mengirim reservasi.',
            ]);
        }

        if ($proofFile) {
            $ext = strtolower($proofFile->getClientOriginalExtension() ?: $proofFile->extension() ?: '');
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'];
            if (! in_array($ext, $allowed, true)) {
                throw ValidationException::withMessages([
                    'payment_proof' => 'Format bukti harus JPG, PNG, WebP, atau HEIC.',
                ]);
            }
            if ($proofFile->getSize() > 5 * 1024 * 1024) {
                throw ValidationException::withMessages([
                    'payment_proof' => 'Ukuran file bukti maksimal 5 MB.',
                ]);
            }
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $realPath = $proofFile->getRealPath();
                if (! $realPath || ! is_array(@getimagesize($realPath))) {
                    throw ValidationException::withMessages([
                        'payment_proof' => 'File bukti transfer tidak dikenali sebagai gambar yang sah.',
                    ]);
                }
            }

            $filename = Str::uuid()->toString().'.'.$ext;
            $proofPath = $proofFile->storeAs('payment_proofs', $filename, 'public');
        }

        $reservation = DB::transaction(function () use (
            $dateObj,
            $timeRaw,
            $partySize,
            $capacityPerSlot,
            $name,
            $phoneInput,
            $phoneWa,
            $data,
            $seatingOptionId,
            $source,
            $paymentType,
            $quote,
            $proofPath,
            $skipMinRules,
            $actor
        ): Reservation {
            $slotQuery = Reservation::query()
                ->whereDate('date', $dateObj->format('Y-m-d'))
                ->where('time', $timeRaw)
                ->whereIn('status', ['pending', 'confirmed', 'arrived']);

            if (DB::getDriverName() !== 'sqlite') {
                $slotQuery->lockForUpdate();
            }

            $bookedSeats = (int) $slotQuery->sum('party_size');
            if (($bookedSeats + $partySize) > $capacityPerSlot) {
                $remaining = max(0, $capacityPerSlot - $bookedSeats);
                throw ValidationException::withMessages([
                    'time' => "Slot jam {$timeRaw} tersisa {$remaining} kursi. Pilih jam lain ya.",
                ]);
            }

            $paymentStatus = $paymentType === 'cash' ? 'waiting_proof' : 'proof_uploaded';
            if ($paymentType === 'cash' && $actor) {
                $paymentStatus = 'verified';
            }

            $status = ($paymentType === 'cash' && $actor) ? 'confirmed' : 'pending';

            $reservation = Reservation::create([
                'name' => $name,
                'phone_input' => $phoneInput,
                'phone_wa' => $phoneWa,
                'date' => $dateObj->format('Y-m-d'),
                'time' => $timeRaw,
                'party_size' => $partySize,
                'note' => isset($data['note']) ? trim((string) $data['note']) ?: null : null,
                'seating_option_id' => $seatingOptionId,
                'seating_note' => isset($data['seating_note']) ? trim((string) $data['seating_note']) ?: null : null,
                'mode' => 'table_menu',
                'source' => $source,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_type' => $paymentType,
                'subtotal' => $quote['subtotal'],
                'min_spend_snapshot' => $quote['min_spend'],
                'dp_amount' => $quote['dp_amount'],
                'amount_due' => $quote['amount_due'],
                'amount_paid' => ($paymentType === 'cash' && $actor) ? $quote['subtotal'] : null,
                'payment_proof_path' => $proofPath,
                'admin_note' => isset($data['admin_note']) ? trim((string) $data['admin_note']) ?: null : null,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
            ]);

            foreach ($quote['items'] as $item) {
                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'name_snapshot' => $item['name_snapshot'],
                    'variant_label' => $item['variant_label'],
                    'temperature' => $item['temperature'],
                    'price_snapshot' => $item['price_snapshot'],
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                ]);
            }

            ActivityLog::record('reservation.created', Reservation::class, $reservation->id, [
                'code' => $reservation->code,
                'source' => $source,
                'party_size' => $partySize,
                'subtotal' => $quote['subtotal'],
                'payment_type' => $paymentType,
                'skip_min_rules' => $skipMinRules,
                'actor_id' => $actor?->id,
            ]);

            return $reservation;
        });

        $reservation->load(['items', 'seatingOption']);

        // Notifikasi database Filament ke owner / admin / kasir
        $staff = User::query()
            ->whereIn('role', ['owner', 'admin', 'cashier'])
            ->get();

        if ($staff->isNotEmpty()) {
            Notification::send($staff, new NewReservationNotification($reservation));
        }

        // Notifikasi WhatsApp Lapis 2
        NotifyUmcoWhatsApp::dispatch($reservation->id);

        return $reservation;
    }
}
