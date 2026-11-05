<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\ClosedDate;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.public')]
class ReservationPage extends Component
{
    use WithFileUploads;

    #[Url(as: 'mode')]
    public string $mode = 'a'; // 'a' = Meja Saja (Tanpa DP), 'b' = Meja + Menu (DP 30%)

    #[Url(as: 'menu')]
    public ?string $initialMenuSlug = null;

    public string $name = '';
    public string $phone = '';
    public string $date = '';
    public string $time = '14:00';
    public int $party_size = 2;
    public string $note = '';

    // Mode B fields
    public array $selected_items = []; // [item_id => quantity]
    public $payment_proof = null;

    // View state: 'form' | 'success'
    public string $step = 'form';
    public ?string $created_code = null;
    public ?Reservation $created_reservation = null;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');

        // Check if query string requested mode b
        if ($this->mode !== 'b') {
            $this->mode = 'a';
        }

        // If pre-selected menu slug is present in URL
        if ($this->initialMenuSlug) {
            $item = MenuItem::where('slug', $this->initialMenuSlug)
                ->where('is_active', true)
                ->where('is_available', true)
                ->first();

            if ($item) {
                $this->mode = 'b';
                $this->selected_items[$item->id] = 1;
            }
        }
    }

    public function setMode(string $mode): void
    {
        if (in_array($mode, ['a', 'b'])) {
            $this->mode = $mode;
        }
    }

    public function addItem(int $itemId): void
    {
        $item = MenuItem::where('id', $itemId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->first();

        if ($item) {
            $current = $this->selected_items[$itemId] ?? 0;
            $this->selected_items[$itemId] = $current + 1;
        }
    }

    public function removeItem(int $itemId): void
    {
        if (isset($this->selected_items[$itemId])) {
            if ($this->selected_items[$itemId] > 1) {
                $this->selected_items[$itemId]--;
            } else {
                unset($this->selected_items[$itemId]);
            }
        }
    }

    public function getSubtotalProperty(): int
    {
        $subtotal = 0;
        if (empty($this->selected_items)) {
            return 0;
        }

        $items = MenuItem::whereIn('id', array_keys($this->selected_items))->get();
        foreach ($items as $item) {
            $qty = $this->selected_items[$item->id] ?? 0;
            $subtotal += ($item->price * $qty);
        }

        return $subtotal;
    }

    /**
     * DP formula: 30% of subtotal, minimum Rp 25.000, rounded up to nearest Rp 1.000.
     */
    public function getDpAmountProperty(): int
    {
        if ($this->mode !== 'b' || $this->subtotal === 0) {
            return 0;
        }

        $rawDp = $this->subtotal * 0.30;
        $rounded = ceil($rawDp / 1000) * 1000;

        return (int) max(25000, $rounded);
    }

    public function getAvailableSlotsProperty(): array
    {
        $openStr = Settings::get('hours.opening_time', '08:00');
        $closeStr = Settings::get('hours.closing_time', '21:00');

        $openHour = (int) explode(':', $openStr)[0];
        $closeHour = (int) explode(':', $closeStr)[0];

        $slots = [];
        for ($h = $openHour; $h < $closeHour; $h++) {
            $slots[] = sprintf('%02d:00', $h);
            $slots[] = sprintf('%02d:30', $h);
        }

        // Query used capacity for this date
        $usedBySlot = Reservation::where('date', $this->date)
            ->where('status', '!=', 'cancelled')
            ->select('time', DB::raw('SUM(party_size) as total_pax'))
            ->groupBy('time')
            ->pluck('total_pax', 'time')
            ->toArray();

        $maxCapacity = 40;
        $result = [];
        foreach ($slots as $slot) {
            $used = (int) ($usedBySlot[$slot] ?? 0);
            $remaining = max(0, $maxCapacity - $used);
            $result[] = [
                'time' => $slot,
                'remaining' => $remaining,
                'is_full' => ($remaining < $this->party_size),
            ];
        }

        return $result;
    }

    public function getClosedDateNoticeProperty(): ?ClosedDate
    {
        if (empty($this->date)) {
            return null;
        }

        return ClosedDate::where('date', $this->date)->first();
    }

    public function submit(): void
    {
        // 1. Phone validation & normalization check
        if (!PhoneNumber::isValid($this->phone)) {
            $this->addError('phone', 'Nomor WhatsApp belum benar, contoh: 0812 3456 7890');
            return;
        }

        $normalizedPhone = PhoneNumber::normalize($this->phone);

        // 2. Base Validation
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string'],
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addDays(14)->format('Y-m-d')],
            'time' => ['required', 'string'],
            'party_size' => ['required', 'integer', 'min:1', 'max:12'],
            'note' => ['nullable', 'string', 'max:500'],
        ];

        // 3. Mode B Validation
        if ($this->mode === 'b') {
            if (empty($this->selected_items) || array_sum($this->selected_items) === 0) {
                $this->addError('selected_items', 'Silakan pilih minimal 1 menu untuk Mode B.');
                return;
            }

            $rules['payment_proof'] = ['required', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:4096'];
        }

        $this->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'date.required' => 'Tanggal reservasi wajib dipilih.',
            'date.after_or_equal' => 'Tanggal reservasi tidak boleh di masa lampau.',
            'date.before_or_equal' => 'Maksimal reservasi hingga 14 hari ke depan.',
            'time.required' => 'Jam reservasi wajib dipilih.',
            'party_size.min' => 'Jumlah orang minimal 1.',
            'party_size.max' => 'Untuk reservasi di atas 12 orang, silakan hubungi WhatsApp kami langsung.',
            'payment_proof.required' => 'Bukti transfer DP 30% wajib diunggah untuk konfirmasi Mode B.',
            'payment_proof.max' => 'Ukuran file bukti transfer maksimal 4 MB.',
            'payment_proof.mimes' => 'Format bukti transfer harus berupa JPG, PNG, WEBP, atau PDF.',
        ]);

        // 4. Closed date check
        if ($this->closedDateNotice) {
            $reason = $this->closedDateNotice->reason ?: 'Toko sedang tutup operasional';
            throw ValidationException::withMessages([
                'date' => "Mohon maaf, UM.CO House tutup pada tanggal ini: {$reason}.",
            ]);
        }

        // 5. DB Transaction with Slot Pessimistic Locking
        try {
            DB::transaction(function () use ($normalizedPhone) {
                // Lock existing reservations on same date & time to prevent race conditions
                $existingPax = Reservation::where('date', $this->date)
                    ->where('time', $this->time)
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->sum('party_size');

                $maxCapacity = 40;
                if (($existingPax + $this->party_size) > $maxCapacity) {
                    $remaining = max(0, $maxCapacity - $existingPax);
                    throw ValidationException::withMessages([
                        'time' => "Kapasitas meja untuk jam {$this->time} sudah penuh ({$remaining} kursi tersisa). Silakan pilih jam lain.",
                    ]);
                }

                // Handle payment proof upload
                $proofPath = null;
                if ($this->mode === 'b' && $this->payment_proof) {
                    $proofPath = $this->payment_proof->store('payment-proofs', 'public');
                }

                // Create reservation
                $reservation = Reservation::create([
                    'code' => Reservation::generateUniqueCode(),
                    'name' => trim($this->name),
                    'phone_input' => trim($this->phone),
                    'phone_wa' => $normalizedPhone,
                    'date' => $this->date,
                    'time' => $this->time,
                    'party_size' => $this->party_size,
                    'note' => $this->note ?: null,
                    'mode' => ($this->mode === 'b') ? 'table_menu' : 'table_only',
                    'status' => 'pending',
                    'payment_status' => ($this->mode === 'b') ? 'proof_uploaded' : 'none',
                    'subtotal' => ($this->mode === 'b') ? $this->subtotal : 0,
                    'dp_amount' => ($this->mode === 'b') ? $this->dp_amount : 0,
                    'payment_proof_path' => $proofPath,
                ]);

                // Create reservation items snapshot if Mode B
                if ($this->mode === 'b') {
                    $itemIds = array_keys($this->selected_items);
                    $menuItems = MenuItem::whereIn('id', $itemIds)->get()->keyBy('id');

                    foreach ($this->selected_items as $itemId => $qty) {
                        if ($qty > 0 && isset($menuItems[$itemId])) {
                            $item = $menuItems[$itemId];
                            ReservationItem::create([
                                'reservation_id' => $reservation->id,
                                'menu_item_id' => $item->id,
                                'name_snapshot' => $item->name,
                                'price_snapshot' => $item->price,
                                'quantity' => $qty,
                                'line_total' => $item->price * $qty,
                            ]);
                        }
                    }
                }

                // Audit log
                ActivityLog::record(
                    action: 'reservasi_dibuat_pelanggan',
                    subjectType: Reservation::class,
                    subjectId: $reservation->id,
                    details: [
                        'code' => $reservation->code,
                        'customer' => $reservation->name,
                        'phone' => $reservation->phone_wa,
                        'date' => $reservation->date->format('Y-m-d'),
                        'time' => $reservation->time,
                        'pax' => $reservation->party_size,
                        'mode' => $reservation->mode,
                        'dp' => $reservation->dp_amount,
                    ]
                );

                $this->created_code = $reservation->code;
                $this->created_reservation = $reservation->load('items');
                $this->step = 'success';
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Terjadi kesalahan sistem saat memproses reservasi. Silakan coba kembali atau hubungi WhatsApp kasir.');
        }
    }

    public function resetBooking(): void
    {
        $this->reset([
            'step',
            'created_code',
            'created_reservation',
            'name',
            'phone',
            'note',
            'selected_items',
            'payment_proof',
        ]);
        $this->date = now()->format('Y-m-d');
        $this->time = '14:00';
        $this->party_size = 2;
        $this->mode = 'a';
    }

    public function render()
    {
        $menuItems = MenuItem::where('is_active', true)
            ->where('is_available', true)
            ->with('category')
            ->orderBy('sort')
            ->get()
            ->groupBy('category.name');

        $bankAccounts = Settings::get('payment.bank_accounts', [
            ['bank' => 'Bank Mandiri', 'account_number' => '1080023456789', 'account_name' => 'UMCO HOUSE PEKANBARU'],
            ['bank' => 'Bank BCA', 'account_number' => '8420198765', 'account_name' => 'UMCO HOUSE PEKANBARU'],
            ['bank' => 'Bank Syariah Indonesia (BSI)', 'account_number' => '7182930415', 'account_name' => 'UMCO HOUSE PEKANBARU'],
        ]);

        if (is_string($bankAccounts)) {
            $bankAccounts = json_decode($bankAccounts, true) ?: [];
        }

        $qrisImage = Settings::get('payment.qris_image', '/images/qris-placeholder.svg');

        return view('livewire.reservation-page', [
            'menuCategories' => $menuItems,
            'bankAccounts' => $bankAccounts,
            'qrisImage' => $qrisImage,
            'availableSlots' => $this->availableSlots,
            'closedDate' => $this->closedDateNotice,
        ]);
    }
}
