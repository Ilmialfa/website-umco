<?php

namespace App\Livewire;

use App\Actions\Reservations\CreateReservation;
use App\Actions\Reservations\QuoteReservation;
use App\Jobs\NotifyUmcoWhatsApp;
use App\Models\ClosedDate;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\Rule;
use App\Models\SeatingOption;
use App\Models\SpendTier;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * @property-read int $minParty
 * @property-read int $maxPartyOnline
 * @property-read bool $fullPaymentEnabled
 * @property-read bool $menuRequired
 * @property-read list<array{date: string, day_short: string, date_short: string, full_label: string, closed: bool, reason: ?string}> $dateOptions
 * @property-read list<array{time: string, label: string, remaining: int, too_soon: bool, full: bool, available: bool}> $timeSlots
 * @property-read Collection<int, SeatingOption> $seatingOptions
 * @property-read Collection<int, MenuCategory> $categories
 * @property-read Collection<int, MenuItem> $menus
 * @property-read array<string, mixed> $quote
 * @property-read string|null $normalizedPhonePreview
 * @property-read Collection<int, Rule> $rulesList
 * @property-read string $qrisUrl
 * @property-read array<int, array<string, mixed>> $bankAccounts
 * @property-read string|null $floorPlanUrl
 */
#[Layout('components.layouts.public')]
#[Title('Pesan Meja — UMCO House')]
class ReservationPage extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public int $maxVisitedStep = 1;

    // Step 1: Kapan & orang
    public string $date = '';

    public string $time = '';

    public int $partySize = 2;

    // Step 2: Mau duduk di mana
    public ?int $seatingOptionId = null;

    public string $seatingNote = '';

    // Step 3: Pilih menu
    /** @var array<string, array{menu_item_id: int, name: string, variant_label: ?string, temperature: ?string, unit_price: int, qty: int}> */
    public array $selectedItems = [];

    public int $activeCategoryId = 0;

    public string $menuSearch = '';

    /** @var array<int, string> */
    public array $pickedVariants = [];

    /** @var array<int, string> */
    public array $pickedTemps = [];

    // Step 4: Data kamu
    public string $name = '';

    public string $phoneInput = '';

    public string $note = '';

    // Step 5: Bayar
    public string $paymentType = 'dp';

    /** @var TemporaryUploadedFile|null */
    public $paymentProof = null;

    public bool $acceptedRules = false;

    public bool $showRulesDrawer = false;

    // Step 6: Selesai
    #[Locked]
    public ?string $createdCode = null;

    #[Locked]
    public ?string $createdWaLink = null;

    #[Locked]
    public ?int $createdReservationId = null;

    public bool $hasQueryItems = false;

    public bool $mobileSheetOpen = false;

    public bool $tabletKarcisExpanded = false;

    public function mount(Request $request): void
    {
        $minParty = $this->minParty;
        $maxParty = $this->maxPartyOnline;

        $reqParty = (int) $request->query('party', (string) $minParty);
        $this->partySize = max($minParty, min($maxParty, $reqParty));

        $reqSeating = (int) $request->query('seating', 0);
        if ($reqSeating > 0 && SeatingOption::where('id', $reqSeating)->where('is_active', true)->exists()) {
            $this->seatingOptionId = $reqSeating;
        }

        // Default tanggal: pakai ?date= kalau valid & buka, else tanggal terbuka pertama
        $dates = $this->dateOptions;
        $reqDate = trim((string) $request->query('date', ''));
        $matchedDate = collect($dates)->first(fn ($d) => $d['date'] === $reqDate && ! $d['closed']);

        if ($matchedDate) {
            $this->date = $matchedDate['date'];
        } else {
            $this->date = '';
            foreach ($dates as $opt) {
                if ($opt['closed']) {
                    continue;
                }
                $this->date = $opt['date'];
                unset($this->timeSlots);
                if (collect($this->timeSlots)->contains(fn ($s) => $s['available'])) {
                    break;
                }
            }
            if ($this->date === '') {
                $firstOpen = collect($dates)->first(fn ($d) => ! $d['closed']);
                $this->date = $firstOpen['date'] ?? Carbon::now()->format('Y-m-d');
            }
        }

        // Default jam: pakai ?time= kalau tersedia
        $reqTime = substr(trim((string) $request->query('time', '')), 0, 5);
        $slots = $this->timeSlots;
        $matchedSlot = collect($slots)->first(fn ($s) => $s['time'] === $reqTime && $s['available']);
        if ($matchedSlot) {
            $this->time = $matchedSlot['time'];
        } else {
            $firstAvail = collect($slots)->first(fn ($s) => $s['available']);
            $this->time = $firstAvail['time'] ?? '';
        }

        // Prefill dari query ?items=...
        $rawItemsQuery = $request->query('items');
        if (! empty($rawItemsQuery)) {
            $parsed = [];
            if (is_string($rawItemsQuery)) {
                $decoded = json_decode($rawItemsQuery, true);
                if (is_array($decoded)) {
                    $parsed = $decoded;
                } else {
                    foreach (explode(',', $rawItemsQuery) as $pair) {
                        if (str_contains($pair, ':')) {
                            [$keyOrId, $qty] = explode(':', $pair, 2);
                            $keyOrId = trim($keyOrId);
                            if (ctype_digit($keyOrId)) {
                                $parsed[] = ['id' => (int) $keyOrId, 'qty' => (int) $qty];
                            } elseif ($keyOrId !== '') {
                                $parsed[] = ['slug' => $keyOrId, 'qty' => (int) $qty];
                            }
                        }
                    }
                }
            } elseif (is_array($rawItemsQuery)) {
                $parsed = $rawItemsQuery;
            }

            if ($parsed !== []) {
                $this->importNotaItems($parsed);
                $this->hasQueryItems = true;
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $notaItems
     */
    public function importNotaItems(array $notaItems): void
    {
        if ($this->selectedItems !== [] || $this->createdCode !== null) {
            return;
        }

        $ids = collect($notaItems)
            ->map(fn ($row) => (int) ($row['menu_item_id'] ?? $row['id'] ?? 0))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $slugs = collect($notaItems)
            ->map(fn ($row) => trim((string) ($row['slug'] ?? '')))
            ->filter(fn ($slug) => $slug !== '')
            ->unique()
            ->values();

        if ($ids->isEmpty() && $slugs->isEmpty()) {
            return;
        }

        $menuRows = MenuItem::query()
            ->where('is_active', true)
            ->where('is_available', true)
            ->where(function ($q) use ($ids, $slugs) {
                if ($ids->isNotEmpty()) {
                    $q->whereIn('id', $ids);
                }
                if ($slugs->isNotEmpty()) {
                    $q->orWhereIn('slug', $slugs);
                }
            })
            ->get();

        $menusById = $menuRows->keyBy('id');
        $menusBySlug = $menuRows->keyBy('slug');

        foreach ($notaItems as $row) {
            $menuId = (int) ($row['menu_item_id'] ?? $row['id'] ?? 0);
            $slug = trim((string) ($row['slug'] ?? ''));
            $qty = max(0, (int) ($row['qty'] ?? 1));
            if ($qty <= 0) {
                continue;
            }

            /** @var MenuItem|null $menu */
            $menu = $menuId > 0
                ? $menusById->get($menuId)
                : ($slug !== '' ? $menusBySlug->get($slug) : null);

            if (! $menu) {
                continue;
            }
            $variant = isset($row['variant_label']) ? trim((string) $row['variant_label']) : (isset($row['variant']) ? trim((string) $row['variant']) : null);
            $variant = $variant !== '' ? $variant : null;
            $temp = isset($row['temperature']) ? trim((string) $row['temperature']) : (isset($row['temp']) ? trim((string) $row['temp']) : null);
            $temp = in_array($temp, ['Panas', 'Es'], true) ? $temp : ($menu->has_temperature ? 'Es' : null);

            $unitPrice = $this->resolveMenuPrice($menu, $variant);
            $key = $this->lineKey($menu->id, $variant, $temp);

            if (isset($this->selectedItems[$key])) {
                $this->selectedItems[$key]['qty'] += $qty;
            } else {
                $this->selectedItems[$key] = [
                    'menu_item_id' => $menu->id,
                    'name' => $menu->name,
                    'variant_label' => $variant,
                    'temperature' => $temp,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                ];
            }
        }
    }

    #[Computed]
    public function minParty(): int
    {
        return max(2, (int) Settings::get('reservation.min_party', 2));
    }

    #[Computed]
    public function maxPartyOnline(): int
    {
        return max($this->minParty, (int) Settings::get('reservation.max_party_size_online', 12));
    }

    #[Computed]
    public function fullPaymentEnabled(): bool
    {
        return (bool) Settings::get('reservation.full_payment_enabled', true);
    }

    #[Computed]
    public function menuRequired(): bool
    {
        return (bool) Settings::get('reservation.menu_required', true);
    }

    /**
     * @return list<array{date: string, day_short: string, date_short: string, full_label: string, closed: bool, reason: ?string}>
     */
    #[Computed]
    public function dateOptions(): array
    {
        $now = Carbon::now()->startOfDay();
        $maxDays = max(1, (int) Settings::get('reservation.max_advance_days', 14));
        $end = $now->copy()->addDays($maxDays);

        $closedDates = ClosedDate::query()
            ->whereBetween('date', [$now->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($c) => $c->date->format('Y-m-d'));

        $list = [];
        for ($i = 0; $i <= $maxDays; $i++) {
            $d = $now->copy()->addDays($i);
            $d->locale('id');
            $key = $d->format('Y-m-d');
            $closed = $closedDates->get($key);

            $list[] = [
                'date' => $key,
                'day_short' => $i === 0 ? 'Hari ini' : $d->isoFormat('ddd'),
                'date_short' => $d->isoFormat('D MMM'),
                'full_label' => $d->isoFormat('ddd, D MMM YYYY'),
                'closed' => (bool) $closed,
                'reason' => $closed?->reason,
            ];
        }

        return $list;
    }

    /**
     * @return list<array{time: string, label: string, remaining: int, too_soon: bool, full: bool, available: bool}>
     */
    #[Computed]
    public function timeSlots(): array
    {
        if ($this->date === '') {
            return [];
        }

        $openStr = substr((string) Settings::get('hours.opening_time', '08:00'), 0, 5);
        $closeStr = substr((string) Settings::get('hours.closing_time', '22:00'), 0, 5);
        $interval = max(15, (int) Settings::get('reservation.slot_interval', 30));
        $lastBeforeClose = max(0, (int) Settings::get('reservation.last_slot_before_close', 60));
        $capacity = max(1, (int) Settings::get('reservation.capacity_per_slot', 40));
        $minAdvance = max(0, (int) Settings::get('reservation.min_advance_minutes', 60));

        $now = Carbon::now();
        $cutoff = $now->copy()->addMinutes($minAdvance);

        try {
            $cursor = Carbon::createFromFormat('Y-m-d H:i', $this->date.' '.$openStr);
            $last = Carbon::createFromFormat('Y-m-d H:i', $this->date.' '.$closeStr)
                ->subMinutes($lastBeforeClose);
        } catch (\Throwable) {
            return [];
        }

        $bookedByTime = Reservation::query()
            ->selectRaw('time, SUM(party_size) as total_guests')
            ->whereDate('date', $this->date)
            ->whereIn('status', ['pending', 'confirmed', 'arrived'])
            ->groupBy('time')
            ->pluck('total_guests', 'time');

        $slots = [];
        while ($cursor->lte($last)) {
            $timeStr = $cursor->format('H:i');
            $booked = (int) ($bookedByTime[$timeStr] ?? 0);
            $remaining = max(0, $capacity - $booked);
            $tooSoon = $cursor->lt($cutoff);
            $available = ! $tooSoon && $remaining >= $this->partySize;

            $slots[] = [
                'time' => $timeStr,
                'label' => str_replace(':', '.', $timeStr),
                'remaining' => $remaining,
                'too_soon' => $tooSoon,
                'full' => $remaining < $this->partySize,
                'available' => $available,
            ];

            $cursor->addMinutes($interval);
        }

        return $slots;
    }

    /**
     * @return Collection<int, SeatingOption>
     */
    #[Computed]
    public function seatingOptions(): Collection
    {
        return SeatingOption::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, MenuCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return MenuCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, MenuItem>
     */
    #[Computed]
    public function menus(): Collection
    {
        $query = MenuItem::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_available', true);

        if ($this->activeCategoryId > 0) {
            $query->where('category_id', $this->activeCategoryId);
        }

        $search = trim($this->menuSearch);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('is_favorite')->orderBy('sort')->orderBy('id')->get();
    }

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function quote(): array
    {
        return QuoteReservation::calculate(
            $this->partySize,
            array_values($this->selectedItems),
            $this->paymentType
        );
    }

    #[Computed]
    public function normalizedPhonePreview(): ?string
    {
        try {
            $clean = PhoneNumber::normalize($this->phoneInput);
        } catch (\InvalidArgumentException) {
            return null;
        }

        $local = substr($clean, 2);

        return '+62 '.substr($local, 0, 3).'-'.substr($local, 3, 4).'-'.substr($local, 7);
    }

    /**
     * @return Collection<int, Rule>
     */
    #[Computed]
    public function rulesList(): Collection
    {
        return Rule::query()->orderBy('sort')->orderBy('id')->get();
    }

    #[Computed]
    public function qrisUrl(): string
    {
        $path = trim((string) Settings::get('payment.qris_image_path', ''));
        if ($path === '') {
            return asset('/images/qris-placeholder.svg');
        }
        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function bankAccounts(): array
    {
        $raw = Settings::get('payment.bank_accounts', []);
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }

    #[Computed]
    public function floorPlanUrl(): ?string
    {
        $path = trim((string) Settings::get('reservation.floor_plan_image_path', ''));
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public function selectDate(string $date): void
    {
        $this->date = $date;
        unset($this->timeSlots);

        $slots = $this->timeSlots;
        $stillValid = collect($slots)->contains(fn ($s) => $s['time'] === $this->time && $s['available']);
        if (! $stillValid) {
            $first = collect($slots)->first(fn ($s) => $s['available']);
            $this->time = $first['time'] ?? '';
        }
    }

    public function incrementParty(): void
    {
        if ($this->partySize < $this->maxPartyOnline) {
            $this->partySize++;
            unset($this->timeSlots, $this->quote);
        }
    }

    public function decrementParty(): void
    {
        if ($this->partySize > $this->minParty) {
            $this->partySize--;
            unset($this->timeSlots, $this->quote);
        }
    }

    public function setMenuOption(int $menuId, ?string $variant = null, ?string $temp = null): void
    {
        if ($variant !== null) {
            $this->pickedVariants[$menuId] = $variant;
        }
        if ($temp !== null) {
            $this->pickedTemps[$menuId] = $temp;
        }
    }

    public function addMenu(int $menuId): void
    {
        $menu = MenuItem::query()
            ->where('id', $menuId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->first();

        if (! $menu) {
            return;
        }

        $variants = is_array($menu->variants) ? $menu->variants : [];
        $variant = $this->pickedVariants[$menuId]
            ?? ($variants !== [] && isset($variants[0]['label']) ? (string) $variants[0]['label'] : null);
        $temp = $menu->has_temperature ? ($this->pickedTemps[$menuId] ?? 'Es') : null;
        $price = $this->resolveMenuPrice($menu, $variant);
        $key = $this->lineKey($menu->id, $variant, $temp);

        if (isset($this->selectedItems[$key])) {
            $this->selectedItems[$key]['qty']++;
        } else {
            $this->selectedItems[$key] = [
                'menu_item_id' => $menu->id,
                'name' => $menu->name,
                'variant_label' => $variant,
                'temperature' => $temp,
                'unit_price' => $price,
                'qty' => 1,
            ];
        }

        unset($this->quote);
    }

    public function incrementLine(string $key): void
    {
        if (isset($this->selectedItems[$key])) {
            $this->selectedItems[$key]['qty']++;
            unset($this->quote);
        }
    }

    public function decrementLine(string $key): void
    {
        if (! isset($this->selectedItems[$key])) {
            return;
        }

        if ($this->selectedItems[$key]['qty'] <= 1) {
            unset($this->selectedItems[$key]);
        } else {
            $this->selectedItems[$key]['qty']--;
        }

        unset($this->quote);
    }

    public function goToStep(int $target): void
    {
        if ($this->createdCode !== null) {
            return;
        }
        if ($target >= 1 && $target <= $this->maxVisitedStep) {
            $this->step = $target;
            $this->resetErrorBag();
            $this->dispatch('umco-step-changed');
        }
    }

    public function nextStep(): void
    {
        $this->resetErrorBag();

        if ($this->step === 1) {
            $this->validateStep1();
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 3;
        } elseif ($this->step === 3) {
            $this->validateStep3();
            $this->step = 4;
        } elseif ($this->step === 4) {
            $this->validateStep4();
            $this->step = 5;
        }

        $this->maxVisitedStep = max($this->maxVisitedStep, $this->step);
        $this->dispatch('umco-step-changed');
    }

    public function prevStep(): void
    {
        if ($this->step > 1 && $this->createdCode === null) {
            $this->step--;
            $this->resetErrorBag();
            $this->dispatch('umco-step-changed');
        }
    }

    public function submitReservation(): void
    {
        $this->resetErrorBag();

        $throttleKey = 'umco:res-submit:'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('acceptedRules', "Terlalu banyak kiriman dalam waktu singkat. Coba lagi {$seconds} detik lagi ya.");

            return;
        }
        RateLimiter::hit($throttleKey, 60);

        $this->validateStep1();
        $this->validateStep3();
        $this->validateStep4();

        if (! $this->acceptedRules) {
            $this->addError('acceptedRules', 'Centang dulu tanda sudah baca aturan kedai.');

            return;
        }

        try {
            $reservation = CreateReservation::execute([
                'source' => 'web',
                'date' => $this->date,
                'time' => $this->time,
                'party_size' => $this->partySize,
                'seating_option_id' => $this->seatingOptionId,
                'seating_note' => $this->seatingNote,
                'name' => $this->name,
                'phone_input' => $this->phoneInput,
                'note' => $this->note,
                'payment_type' => $this->paymentType,
                'items' => array_values($this->selectedItems),
            ], $this->paymentProof);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $mappedField = match ($field) {
                    'phone_input' => 'phoneInput',
                    'payment_proof' => 'paymentProof',
                    'party_size' => 'partySize',
                    default => $field,
                };
                $this->addError($mappedField, $messages[0] ?? 'Periksa isian ini.');
            }

            return;
        }

        session()->put("umco_status_verified_{$reservation->code}", true);
        $this->createdReservationId = $reservation->id;
        $this->createdCode = $reservation->code;
        $this->createdWaLink = NotifyUmcoWhatsApp::waLink($reservation);
        $this->step = 6;
        $this->maxVisitedStep = 6;

        $this->dispatch('umco-open-wa', url: $this->createdWaLink);
    }

    private function validateStep1(): void
    {
        if ($this->date === '') {
            throw ValidationException::withMessages(['date' => 'Pilih tanggal kedatangan dulu.']);
        }
        $closed = ClosedDate::whereDate('date', $this->date)->first();
        if ($closed) {
            throw ValidationException::withMessages(['date' => "Tanggal ini tutup: {$closed->reason}"]);
        }
        if ($this->time === '') {
            throw ValidationException::withMessages(['time' => 'Pilih jam kedatangan yang tersedia.']);
        }
        $slot = collect($this->timeSlots)->first(fn ($s) => $s['time'] === $this->time);
        if (! $slot || ! $slot['available']) {
            throw ValidationException::withMessages(['time' => 'Slot jam ini tidak tersedia. Pilih jam lain ya.']);
        }
        if ($this->partySize < $this->minParty) {
            throw ValidationException::withMessages(['partySize' => "Minimal {$this->minParty} orang untuk reservasi."]);
        }
    }

    private function validateStep3(): void
    {
        $quote = $this->quote;
        if ($this->menuRequired) {
            if (empty($quote['items'])) {
                throw ValidationException::withMessages(['items' => 'Pilih minimal satu menu dulu ya.']);
            }
            if (! $quote['meets_min_spend']) {
                $minFmt = 'Rp '.number_format($quote['min_spend'], 0, ',', '.');
                $kurangFmt = 'Rp '.number_format($quote['shortfall'], 0, ',', '.');
                throw ValidationException::withMessages([
                    'items' => "Minimal belanja {$this->partySize} orang adalah {$minFmt} (kurang {$kurangFmt}).",
                ]);
            }
        }
    }

    private function validateStep4(): void
    {
        if (mb_strlen(trim($this->name)) < 2) {
            throw ValidationException::withMessages(['name' => 'Tulis namamu minimal 2 huruf.']);
        }
        try {
            PhoneNumber::normalize($this->phoneInput);
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages(['phoneInput' => 'Nomor WhatsApp belum valid. Contoh: 082212345678.']);
        }
    }

    private function resolveMenuPrice(MenuItem $menu, ?string $variantLabel): int
    {
        if ($variantLabel && is_array($menu->variants)) {
            foreach ($menu->variants as $v) {
                if (is_array($v) && isset($v['label']) && mb_strtolower((string) $v['label']) === mb_strtolower($variantLabel)) {
                    return (int) ($v['price'] ?? $menu->price);
                }
            }
        }

        return (int) $menu->price;
    }

    private function lineKey(int $menuId, ?string $variant, ?string $temp): string
    {
        return $menuId.'|'.($variant ?? '').'|'.($temp ?? '');
    }

    public function render(): View
    {
        $minSpend = SpendTier::minSpendFor($this->partySize);

        return view('livewire.reservation-page', [
            'minSpendForParty' => $minSpend,
        ]);
    }
}
