@php
    $quoteData = $this->quote;
    $dateObj = $this->date ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->date, 'Asia/Jakarta')->locale('id') : null;
    $dateDisplay = $dateObj ? $dateObj->isoFormat('ddd, D MMM') : 'Belum dipilih';
    $timeDisplay = $this->time ? str_replace(':', '.', $this->time) . ' WIB' : 'Belum dipilih';
    $selectedSeating = $this->seatingOptions->firstWhere('id', $this->seatingOptionId);
    $seatingDisplay = $selectedSeating ? $selectedSeating->label : 'Bebas di mana saja';
    $itemsSummaryText = collect($quoteData['items'])
        ->map(fn ($i) => $i['qty'] . 'x ' . $i['name_snapshot'])
        ->implode(', ');
    if ($itemsSummaryText === '') {
        $itemsSummaryText = 'Belum ada pesanan';
    }
    $payChoiceLabel = $this->paymentType === 'full' ? 'Lunas' : 'DP ' . $quoteData['dp_percentage'] . '%';
    $stepTitles = [
        1 => 'Kapan',
        2 => 'Tempat',
        3 => 'Menu',
        4 => 'Data',
        5 => 'Bayar',
    ];
    $minSpendPct = $quoteData['min_spend'] > 0
        ? min(100, (int) round(($quoteData['subtotal'] / $quoteData['min_spend']) * 100))
        : 100;
@endphp

<div
    data-module="reservasi-tools"
    data-has-query-items="{{ $hasQueryItems ? '1' : '0' }}"
    x-data="{
        mobileSheetOpen: @entangle('mobileSheetOpen'),
        tabletExpanded: @entangle('tabletKarcisExpanded'),
        copied: false,
        compressing: false,
        copyAmount(val) {
            navigator.clipboard?.writeText(String(val));
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 1800);
        },
        async handleProofUpload(event, wireProp) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.compressing = true;
            try {
                const compressed = window.umcoCompressProof
                    ? await window.umcoCompressProof(file)
                    : file;
                $wire.upload(wireProp, compressed, () => {
                    this.compressing = false;
                }, () => {
                    this.compressing = false;
                });
            } catch {
                this.compressing = false;
                $wire.upload(wireProp, file);
            }
        }
    }"
    class="pt-20 pb-36 sm:pb-16 lg:pb-20 min-h-screen bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container">
        {{-- Header halaman --}}
        <div class="pb-6 border-b border-[var(--garis)] flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="font-display font-extrabold tracking-tight text-3xl sm:text-4xl lg:text-5xl leading-[1.02]">
                    {{ $step === 6 ? 'Karcismu sudah tercatat.' : 'Pesan meja di UMCO.' }}
                </h1>
                <p class="mt-2 text-sm sm:text-base text-[var(--tinta-70)] max-w-xl">
                    @if ($step === 6)
                        Tunjukkan kode karcis di kasir waktu datang. Kami tunggu 15 menit setelah jam pesan.
                    @else
                        Minimal {{ $this->minParty }} orang, pilih menu sekalian, lalu bayar DP {{ $quoteData['dp_percentage'] }}% atau lunas.
                    @endif
                </p>
            </div>

            @if ($step < 6)
                <div class="flex items-center gap-3 text-xs sm:text-sm text-[var(--tinta-70)]">
                    <span>Minimal belanja {{ $partySize }} orang:</span>
                    <span class="font-display font-bold text-[var(--tinta)] px-2.5 py-1 bg-[var(--kertas-terang)] border border-[var(--garis)]">
                        {{ \App\Support\IndonesianFormat::rupiah($quoteData['min_spend']) }}
                    </span>
                </div>
            @endif
        </div>

        @if ($step < 6)
            {{-- Bar langkah horizontal (1 Kapan · 2 Tempat · 3 Menu · 4 Data · 5 Bayar) --}}
            <nav aria-label="Langkah reservasi" class="py-4 border-b border-[var(--garis)] overflow-x-auto no-scrollbar">
                <ol class="flex items-center gap-2 sm:gap-4 min-w-max">
                    @foreach ($stepTitles as $num => $label)
                        @php
                            $isCurrent = $step === $num;
                            $canJump = $num <= $maxVisitedStep && ! $isCurrent;
                        @endphp
                        <li class="flex items-center gap-2 sm:gap-3">
                            <button
                                type="button"
                                wire:click="goToStep({{ $num }})"
                                @disabled(! $canJump && ! $isCurrent)
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm transition-colors duration-[180ms] {{ $isCurrent
                                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold'
                                    : ($canJump
                                        ? 'bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis)] hover:border-[var(--tinta)] cursor-pointer'
                                        : 'text-[var(--tinta-40)] border border-transparent cursor-default') }}"
                            >
                                <span class="font-display font-bold text-xs">{{ $num }}</span>
                                <span>{{ $label }}</span>
                            </button>
                            @if ($num < 5)
                                <span class="w-4 h-px bg-[var(--garis)]" aria-hidden="true"></span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            {{-- TABLET (640px-1023px): Kartu ringkas karcis menempel & bisa dilipat --}}
            <div class="hidden sm:block lg:hidden sticky top-16 z-30 mt-4 rounded-2xl bg-[var(--kertas-terang)] text-[var(--tinta)] border-t-[3px] border-t-[var(--mentega)] border border-[var(--garis)] px-5 py-3.5">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-display font-semibold text-sm truncate">
                            {{ $dateDisplay }} · {{ $timeDisplay }} · {{ $partySize }} orang
                        </p>
                        <p class="text-xs text-[var(--tinta)]/75 truncate">
                            {{ $seatingDisplay }} · Total {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="tabletExpanded = !tabletExpanded"
                        class="shrink-0 px-3 py-1.5 rounded-full border border-[var(--tinta)] text-xs font-semibold hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                    >
                        <span x-text="tabletExpanded ? 'Tutup karcis' : 'Lihat karcis'">Lihat karcis</span>
                    </button>
                </div>

                <div x-show="tabletExpanded" x-collapse class="mt-4 pt-4 border-t border-dashed border-[var(--tinta)]/30 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <x-umco.karcis-line label="Hari" :value="$dateDisplay" />
                    <x-umco.karcis-line label="Jam" :value="$timeDisplay" />
                    <x-umco.karcis-line label="Orang" :value="$partySize . ' orang'" />
                    <x-umco.karcis-line label="Tempat" :value="$seatingDisplay" />
                    <x-umco.karcis-line label="Nama" :value="$name !== '' ? $name : '......'" />
                    <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($quoteData['amount_due'])" />
                    <div class="col-span-2 pt-1">
                        <x-umco.karcis-line label="Titipan" :value="$itemsSummaryText" />
                    </div>
                </div>
            </div>

            {{-- GRID UTAMA: Kiri 7 kol area langkah bergeser, Kanan 5 kol Karcis sticky (Desktop >=1024px) --}}
            <div class="mt-6 lg:mt-8 lg:grid lg:grid-cols-12 lg:gap-10 items-start">
                <div class="lg:col-span-7 overflow-hidden">
                    <div
                        class="flex items-start transition-transform duration-[420ms] ease-[cubic-bezier(.22,1,.36,1)]"
                        style="width: 500%; transform: translateX(-{{ ($step - 1) * 20 }}%);"
                    >
                        {{-- LANGKAH 1: KAPAN & BERAPA ORANG --}}
                        <section
                            class="w-1/5 shrink-0 pr-1 space-y-6 {{ $step !== 1 ? 'invisible max-h-[360px] overflow-hidden' : '' }}"
                            aria-hidden="{{ $step !== 1 ? 'true' : 'false' }}"
                            @if ($step !== 1) inert @endif
                        >
                            <div>
                                <h2 class="font-display font-bold text-xl sm:text-2xl">1. Kapan dan berapa orang</h2>
                                <p class="mt-1 text-sm text-[var(--tinta-70)]">Pilih tanggal hingga {{ \App\Support\Settings::get('reservation.max_advance_days', 14) }} hari ke depan.</p>
                            </div>

                            {{-- Jumlah orang --}}
                            <div class="p-4 sm:p-5 bg-[var(--kertas-terang)] border border-[var(--garis)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-base">Jumlah orang</p>
                                    <p class="text-xs sm:text-sm text-[var(--tinta-70)] mt-0.5">
                                        Minimal {{ $this->minParty }} orang, maksimal {{ $this->maxPartyOnline }} orang online.
                                    </p>
                                </div>
                                <div class="inline-flex items-center gap-4 self-start sm:self-auto">
                                    <button
                                        type="button"
                                        wire:click="decrementParty"
                                        aria-label="Kurangi jumlah orang"
                                        @disabled($partySize <= $this->minParty)
                                        class="w-11 h-11 rounded-full border border-[var(--tinta)] font-display font-bold text-xl flex items-center justify-center disabled:opacity-30 hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                                    >−</button>
                                    <span class="font-display font-extrabold text-3xl w-10 text-center tabular-nums">{{ $partySize }}</span>
                                    <button
                                        type="button"
                                        wire:click="incrementParty"
                                        aria-label="Tambah jumlah orang"
                                        @disabled($partySize >= $this->maxPartyOnline)
                                        class="w-11 h-11 rounded-full border border-[var(--tinta)] font-display font-bold text-xl flex items-center justify-center disabled:opacity-30 hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                                    >+</button>
                                </div>
                            </div>
                            @error('partySize')
                                <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                            @enderror

                            {{-- Chip tanggal --}}
                            <div>
                                <p class="text-sm font-semibold mb-2.5">Tanggal datang</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($this->dateOptions as $opt)
                                        @php $activeDate = $date === $opt['date']; @endphp
                                        <button
                                            type="button"
                                            wire:click="selectDate('{{ $opt['date'] }}')"
                                            @disabled($opt['closed'])
                                            title="{{ $opt['closed'] ? ($opt['reason'] ?? 'Tutup') : $opt['full_label'] }}"
                                            class="px-3.5 py-2 rounded-full border text-left transition-colors {{ $opt['closed']
                                                ? 'border-[var(--garis)] text-[var(--tinta-40)] line-through cursor-not-allowed'
                                                : ($activeDate
                                                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                                    : 'bg-[var(--kertas-terang)] border-[var(--garis)] hover:border-[var(--tinta)]') }}"
                                        >
                                            <span class="block text-xs font-semibold leading-tight">{{ $opt['day_short'] }}</span>
                                            <span class="block text-xs opacity-80 leading-tight">{{ $opt['date_short'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                @error('date')
                                    <p class="mt-2 text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Grid jam --}}
                            <div>
                                <p class="text-sm font-semibold mb-2.5">Jam datang</p>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                    @forelse ($this->timeSlots as $slot)
                                        @php $activeTime = $time === $slot['time']; @endphp
                                        <button
                                            type="button"
                                            wire:click="$set('time', '{{ $slot['time'] }}')"
                                            @disabled(! $slot['available'])
                                            class="py-2.5 px-2 rounded-full border text-center transition-colors {{ ! $slot['available']
                                                ? 'border-[var(--garis)] text-[var(--tinta-40)] line-through cursor-not-allowed'
                                                : ($activeTime
                                                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)] font-semibold'
                                                    : 'bg-[var(--kertas-terang)] border-[var(--garis)] hover:border-[var(--tinta)]') }}"
                                        >
                                            <span class="block text-sm tabular-nums leading-tight">{{ $slot['label'] }}</span>
                                            @if ($slot['available'] && $slot['remaining'] < 8)
                                                <span class="block text-[10px] opacity-80 leading-none mt-0.5">Sisa {{ $slot['remaining'] }} kursi</span>
                                            @endif
                                        </button>
                                    @empty
                                        <p class="col-span-full text-sm text-[var(--tinta-70)]">Belum ada slot jam terbuka untuk tanggal ini.</p>
                                    @endforelse
                                </div>
                                @error('time')
                                    <p class="mt-2 text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="hidden sm:flex justify-end pt-2">
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                                >
                                    Lanjut ke pilih tempat
                                </button>
                            </div>
                        </section>

                        {{-- LANGKAH 2: MAU DUDUK DI MANA --}}
                        <section
                            class="w-1/5 shrink-0 pr-1 space-y-6 {{ $step !== 2 ? 'invisible max-h-[360px] overflow-hidden' : '' }}"
                            aria-hidden="{{ $step !== 2 ? 'true' : 'false' }}"
                            @if ($step !== 2) inert @endif
                        >
                            <div>
                                <h2 class="font-display font-bold text-xl sm:text-2xl">2. Mau duduk di mana</h2>
                                <p class="mt-1 text-sm text-[var(--tinta-70)]">Pilih suasana area yang paling pas buat agendamulah hari ini.</p>
                            </div>

                            <div class="flex flex-wrap gap-2.5">
                                @foreach ($this->seatingOptions as $opt)
                                    @php $pickedSeat = $seatingOptionId === $opt->id; @endphp
                                    <button
                                        type="button"
                                        wire:click="$set('seatingOptionId', {{ $pickedSeat ? 'null' : $opt->id }})"
                                        class="px-5 py-3 rounded-full border text-sm font-medium transition-colors {{ $pickedSeat
                                            ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                            : 'bg-[var(--kertas-terang)] border-[var(--garis)] hover:border-[var(--tinta)]' }}"
                                    >
                                        {{ $opt->label }}
                                    </button>
                                @endforeach
                            </div>

                            <div>
                                <label for="seatingNote" class="block text-sm font-semibold mb-2">
                                    Catatan tempat duduk (opsional)
                                </label>
                                <textarea
                                    id="seatingNote"
                                    wire:model.live.debounce.300ms="seatingNote"
                                    rows="3"
                                    placeholder="Ada permintaan lain? Ulang tahun, butuh colokan banyak, dsb."
                                    class="w-full px-4 py-3 text-base bg-[var(--kertas-terang)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                                ></textarea>
                                <p class="mt-2 text-xs sm:text-sm text-[var(--tinta-70)]">
                                    Kami usahakan sesuai permintaan, tapi tergantung meja yang kosong.
                                </p>
                            </div>

                            @if ($this->floorPlanUrl)
                                <div class="p-4 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-2">
                                    <p class="text-xs font-semibold text-[var(--tinta-70)]">Denah kedai (panduan area)</p>
                                    <img
                                        src="{{ $this->floorPlanUrl }}"
                                        alt="Panduan denah area duduk UMCO"
                                        class="w-full max-h-72 object-contain"
                                        loading="lazy"
                                    >
                                </div>
                            @endif

                            <div class="hidden sm:flex items-center justify-between pt-2">
                                <button
                                    type="button"
                                    wire:click="prevStep"
                                    class="px-5 py-2.5 rounded-full border border-[var(--garis)] text-sm font-medium hover:border-[var(--tinta)]"
                                >Kembali</button>
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                                >
                                    Lanjut pilih menu
                                </button>
                            </div>
                        </section>

                        {{-- LANGKAH 3: PILIH MENU --}}
                        <section
                            class="w-1/5 shrink-0 pr-1 space-y-5 {{ $step !== 3 ? 'invisible max-h-[360px] overflow-hidden' : '' }}"
                            aria-hidden="{{ $step !== 3 ? 'true' : 'false' }}"
                            @if ($step !== 3) inert @endif
                        >
                            <div>
                                <h2 class="font-display font-bold text-xl sm:text-2xl">3. Pilih menu</h2>
                                <p class="mt-1 text-sm text-[var(--tinta-70)]">
                                    Total pesananmu harus memenuhi minimal belanja sebelum bayar.
                                </p>
                            </div>

                            {{-- Progress bar minimal belanja --}}
                            <div class="p-4 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-2.5">
                                <div class="flex flex-wrap items-baseline justify-between gap-2 text-sm">
                                    @if ($quoteData['meets_min_spend'])
                                        <span class="font-semibold text-[var(--tinta)]">
                                            {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }} dari minimal {{ \App\Support\IndonesianFormat::rupiah($quoteData['min_spend']) }}. Sudah cukup.
                                        </span>
                                    @else
                                        <span class="font-semibold text-[var(--tinta)]">
                                            {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }} dari minimal {{ \App\Support\IndonesianFormat::rupiah($quoteData['min_spend']) }}.
                                        </span>
                                        <span class="text-[var(--stempel)] font-semibold">
                                            Kurang {{ \App\Support\IndonesianFormat::rupiah($quoteData['shortfall']) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="w-full h-2 rounded-full bg-[var(--batu)] overflow-hidden">
                                    <div
                                        class="h-full transition-all duration-300 {{ $quoteData['meets_min_spend'] ? 'bg-[var(--tinta)]' : 'bg-[var(--mentega)]' }}"
                                        style="width: {{ $minSpendPct }}%"
                                    ></div>
                                </div>
                            </div>

                            @error('items')
                                <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                            @enderror

                            {{-- Pencarian & filter kategori --}}
                            <div class="space-y-3">
                                <input
                                    type="search"
                                    wire:model.live.debounce.250ms="menuSearch"
                                    placeholder="Cari kopi, matcha, makanan..."
                                    class="w-full px-4 py-2.5 text-base bg-[var(--kertas-terang)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                                >
                                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                                    <button
                                        type="button"
                                        wire:click="$set('activeCategoryId', 0)"
                                        class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border {{ $activeCategoryId === 0
                                            ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                            : 'bg-[var(--kertas-terang)] border-[var(--garis)]' }}"
                                    >Semua</button>
                                    @foreach ($this->categories as $cat)
                                        <button
                                            type="button"
                                            wire:click="$set('activeCategoryId', {{ $cat->id }})"
                                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border {{ $activeCategoryId === $cat->id
                                                ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                                : 'bg-[var(--kertas-terang)] border-[var(--garis)]' }}"
                                        >{{ $cat->name }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Keranjang aktif saat ini --}}
                            @if (! empty($selectedItems))
                                <div class="p-4 rounded-xl bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-2.5">
                                    <p class="text-xs font-semibold text-[var(--tinta-70)]">Pesananmu sekarang</p>
                                    @foreach ($selectedItems as $key => $line)
                                        <div wire:key="cart-line-{{ $key }}" class="flex items-center justify-between gap-3 text-sm">
                                            <div class="min-w-0">
                                                <span class="font-semibold">{{ $line['name'] }}</span>
                                                @if ($line['variant_label'] || $line['temperature'])
                                                    <span class="text-xs text-[var(--tinta-70)]">
                                                        ({{ collect([$line['variant_label'], $line['temperature']])->filter()->implode(' · ') }})
                                                    </span>
                                                @endif
                                                <span class="block text-xs text-[var(--tinta-70)] tabular-nums">
                                                    {{ \App\Support\IndonesianFormat::rupiah($line['unit_price'] * $line['qty']) }}
                                                </span>
                                            </div>
                                            <div class="inline-flex items-center gap-2 shrink-0">
                                                <button
                                                    type="button"
                                                    wire:click="decrementLine('{{ $key }}')"
                                                    aria-label="Kurangi"
                                                    class="w-7 h-7 rounded-full border border-[var(--tinta)] text-sm font-bold flex items-center justify-center"
                                                >−</button>
                                                <span class="w-5 text-center font-semibold tabular-nums">{{ $line['qty'] }}</span>
                                                <button
                                                    type="button"
                                                    wire:click="incrementLine('{{ $key }}')"
                                                    aria-label="Tambah"
                                                    class="w-7 h-7 rounded-full border border-[var(--tinta)] text-sm font-bold flex items-center justify-center"
                                                >+</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Daftar menu ringkas --}}
                            <div class="divide-y divide-[var(--garis)] border-y border-[var(--garis)]">
                                @forelse ($this->menus as $menu)
                                    @php
                                        $variants = is_array($menu->variants) ? $menu->variants : [];
                                        $activeVariant = $pickedVariants[$menu->id] ?? ($variants[0]['label'] ?? null);
                                        $activeTemp = $pickedTemps[$menu->id] ?? ($menu->has_temperature ? 'Es' : null);
                                        $displayPrice = (int) $menu->price;
                                        foreach ($variants as $v) {
                                            if (isset($v['label']) && $v['label'] === $activeVariant) {
                                                $displayPrice = (int) ($v['price'] ?? $displayPrice);
                                            }
                                        }
                                    @endphp
                                    <div wire:key="menu-pick-{{ $menu->id }}" class="py-3.5 flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1 space-y-1.5">
                                            <div class="flex flex-wrap items-baseline gap-x-2">
                                                <span class="font-semibold text-base text-[var(--tinta)]">{{ $menu->name }}</span>
                                                <span class="text-xs text-[var(--tinta-70)]">{{ $menu->category?->name }}</span>
                                            </div>
                                            @if ($menu->description)
                                                <p class="text-xs text-[var(--tinta-70)] line-clamp-2">{{ $menu->description }}</p>
                                            @endif

                                            @if ($variants !== [] || $menu->has_temperature)
                                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                                    @foreach ($variants as $v)
                                                        @if (isset($v['label']))
                                                            <button
                                                                type="button"
                                                                wire:click="setMenuOption({{ $menu->id }}, '{{ $v['label'] }}', null)"
                                                                class="px-2.5 py-0.5 rounded-full text-xs border {{ $activeVariant === $v['label']
                                                                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                                                    : 'border-[var(--garis)] text-[var(--tinta-70)]' }}"
                                                            >{{ $v['label'] }}</button>
                                                        @endif
                                                    @endforeach

                                                    @if ($menu->has_temperature)
                                                        @foreach (['Es', 'Panas'] as $tOpt)
                                                            <button
                                                                type="button"
                                                                wire:click="setMenuOption({{ $menu->id }}, null, '{{ $tOpt }}')"
                                                                class="px-2.5 py-0.5 rounded-full text-xs border {{ $activeTemp === $tOpt
                                                                    ? 'bg-[var(--mentega)] text-[var(--tinta)] border-[var(--tinta)] font-semibold'
                                                                    : 'border-[var(--garis)] text-[var(--tinta-70)]' }}"
                                                            >{{ $tOpt }}</button>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="shrink-0 text-right space-y-1.5">
                                            <p class="font-semibold text-sm tabular-nums">{{ \App\Support\IndonesianFormat::rupiah($displayPrice) }}</p>
                                            <button
                                                type="button"
                                                wire:click="addMenu({{ $menu->id }})"
                                                class="px-4 py-1.5 rounded-full border border-[var(--tinta)] text-xs font-semibold hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                                            >+ Tambah</button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="py-6 text-sm text-[var(--tinta-70)]">Menu tidak ditemukan. Coba kata kunci lain.</p>
                                @endforelse
                            </div>

                            <div class="hidden sm:flex items-center justify-between pt-2">
                                <button
                                    type="button"
                                    wire:click="prevStep"
                                    class="px-5 py-2.5 rounded-full border border-[var(--garis)] text-sm font-medium hover:border-[var(--tinta)]"
                                >Kembali</button>
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    @disabled($this->menuRequired && ! $quoteData['meets_min_spend'])
                                    class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm disabled:opacity-40 hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                                >
                                    Lanjut isi data
                                </button>
                            </div>
                        </section>

                        {{-- LANGKAH 4: DATA KAMU --}}
                        <section
                            class="w-1/5 shrink-0 pr-1 space-y-5 {{ $step !== 4 ? 'invisible max-h-[360px] overflow-hidden' : '' }}"
                            aria-hidden="{{ $step !== 4 ? 'true' : 'false' }}"
                            @if ($step !== 4) inert @endif
                        >
                            <div>
                                <h2 class="font-display font-bold text-xl sm:text-2xl">4. Data kamu</h2>
                                <p class="mt-1 text-sm text-[var(--tinta-70)]">Kami pakai untuk kirim konfirmasi karcis dan pengingat meja.</p>
                            </div>

                            <div>
                                <label for="resName" class="block text-sm font-semibold mb-1.5">Nama lengkap</label>
                                <input
                                    id="resName"
                                    type="text"
                                    wire:model.live.debounce.250ms="name"
                                    placeholder="Contoh: Dinda Kirana"
                                    class="w-full px-4 py-3 text-base bg-[var(--kertas-terang)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                                >
                                @error('name')
                                    <p class="mt-1.5 text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="resPhone" class="block text-sm font-semibold mb-1.5">Nomor WhatsApp</label>
                                <input
                                    id="resPhone"
                                    type="tel"
                                    inputmode="tel"
                                    wire:model.live.debounce.250ms="phoneInput"
                                    placeholder="082212345678"
                                    class="w-full px-4 py-3 text-base bg-[var(--kertas-terang)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                                >
                                @if ($this->normalizedPhonePreview)
                                    <p class="mt-1.5 text-xs sm:text-sm text-[var(--tinta-70)]">
                                        Kami hubungi ke <span class="font-semibold text-[var(--tinta)]">{{ $this->normalizedPhonePreview }}</span>
                                    </p>
                                @endif
                                @error('phoneInput')
                                    <p class="mt-1.5 text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="resNote" class="block text-sm font-semibold mb-1.5">Catatan tambahan (opsional)</label>
                                <textarea
                                    id="resNote"
                                    wire:model.live.debounce.300ms="note"
                                    rows="2"
                                    placeholder="Misal: datang bertahap, minta kopi dibuat pas kami datang."
                                    class="w-full px-4 py-3 text-base bg-[var(--kertas-terang)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                                ></textarea>
                            </div>

                            <div class="hidden sm:flex items-center justify-between pt-2">
                                <button
                                    type="button"
                                    wire:click="prevStep"
                                    class="px-5 py-2.5 rounded-full border border-[var(--garis)] text-sm font-medium hover:border-[var(--tinta)]"
                                >Kembali</button>
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                                >
                                    Lanjut ke pembayaran
                                </button>
                            </div>
                        </section>

                        {{-- LANGKAH 5: BAYAR --}}
                        <section
                            class="w-1/5 shrink-0 pr-1 space-y-6 {{ $step !== 5 ? 'invisible max-h-[360px] overflow-hidden' : '' }}"
                            aria-hidden="{{ $step !== 5 ? 'true' : 'false' }}"
                            @if ($step !== 5) inert @endif
                        >
                            <div>
                                <h2 class="font-display font-bold text-xl sm:text-2xl">5. Bayar dan unggah bukti</h2>
                                <p class="mt-1 text-sm text-[var(--tinta-70)]">Pilih bayar DP minimal {{ $quoteData['dp_percentage'] }}% atau langsung lunas.</p>
                            </div>

                            {{-- Pilihan DP / Lunas --}}
                            <div class="grid sm:grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('paymentType', 'dp')"
                                    class="p-4 text-left border transition-colors {{ $paymentType === 'dp'
                                        ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                        : 'bg-[var(--kertas-terang)] border-[var(--garis)] hover:border-[var(--tinta)]' }}"
                                >
                                    <span class="block text-xs opacity-75">Minimal {{ $quoteData['dp_percentage'] }}%</span>
                                    <span class="mt-1 block font-display font-extrabold text-xl">
                                        DP {{ $quoteData['dp_percentage'] }}% · {{ \App\Support\IndonesianFormat::rupiah($quoteData['dp_amount']) }}
                                    </span>
                                    <span class="mt-1 block text-xs opacity-75">Sisanya bayar di kasir waktu datang.</span>
                                </button>

                                @if ($this->fullPaymentEnabled)
                                    <button
                                        type="button"
                                        wire:click="$set('paymentType', 'full')"
                                        class="p-4 text-left border transition-colors {{ $paymentType === 'full'
                                            ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                            : 'bg-[var(--kertas-terang)] border-[var(--garis)] hover:border-[var(--tinta)]' }}"
                                    >
                                        <span class="block text-xs opacity-75">Bayar penuh</span>
                                        <span class="mt-1 block font-display font-extrabold text-xl">
                                            Lunas · {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }}
                                        </span>
                                        <span class="mt-1 block text-xs opacity-75">Tinggal duduk dan pesanannya langsung diproses.</span>
                                    </button>
                                @endif
                            </div>

                            {{-- Nominal + QRIS statis --}}
                            <div class="p-5 bg-[var(--kertas-terang)] border border-[var(--garis)] grid sm:grid-cols-12 gap-6 items-center">
                                <div class="sm:col-span-5 flex flex-col items-center">
                                    <img
                                        src="{{ $this->qrisUrl }}"
                                        alt="QRIS statis pembayaran UMCO House"
                                        class="w-48 h-auto border border-[var(--garis)] bg-[var(--kertas-terang)]"
                                    >
                                    <a
                                        href="{{ $this->qrisUrl }}"
                                        download="QRIS-UMCO.png"
                                        class="mt-3 px-4 py-1.5 rounded-full border border-[var(--tinta)] text-xs font-semibold hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                                    >
                                        Simpan QRIS
                                    </a>
                                </div>

                                <div class="sm:col-span-7 space-y-4">
                                    <div>
                                        <p class="text-xs text-[var(--tinta-70)]">Nominal yang dibayar sekarang</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-3">
                                            <span class="font-display font-extrabold text-3xl tabular-nums">
                                                {{ \App\Support\IndonesianFormat::rupiah($quoteData['amount_due']) }}
                                            </span>
                                            <button
                                                type="button"
                                                @click="copyAmount({{ $quoteData['amount_due'] }})"
                                                class="px-3 py-1 rounded-full border border-[var(--garis)] text-xs font-semibold hover:border-[var(--tinta)]"
                                            >
                                                <span x-text="copied ? 'Tersalin' : 'Salin nominal'">Salin nominal</span>
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-[var(--tinta-70)]">
                                            Terbilang: {{ \App\Support\IndonesianFormat::terbilang($quoteData['amount_due']) }}
                                        </p>
                                    </div>

                                    @if (! empty($this->bankAccounts))
                                        <div class="pt-3 border-t border-[var(--garis)] space-y-2 text-xs">
                                            <p class="font-semibold text-[var(--tinta)]">Alternatif transfer:</p>
                                            @foreach ($this->bankAccounts as $bank)
                                                @if (! empty($bank['account_number']) && ! str_contains(strtolower($bank['bank'] ?? ''), 'qris'))
                                                    <p class="text-[var(--tinta-70)]">
                                                        <strong class="text-[var(--tinta)]">{{ $bank['bank'] ?? '-' }}</strong>
                                                        · {{ $bank['account_number'] }}
                                                        @if (! empty($bank['account_name']))
                                                            (a.n {{ $bank['account_name'] }})
                                                        @endif
                                                    </p>
                                                @elseif (! empty($bank['notes']))
                                                    <p class="text-[var(--tinta-70)]">{{ $bank['notes'] }}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Unggah bukti bayar --}}
                            <div class="space-y-2">
                                <label for="proofInput" class="block text-sm font-semibold">
                                    Unggah bukti transfer (wajib)
                                </label>
                                <div class="p-5 bg-[var(--kertas-terang)] border border-dashed border-[var(--tinta)]/45 text-center space-y-3">
                                    <input
                                        id="proofInput"
                                        type="file"
                                        accept="image/*,.heic,.heif"
                                        @change="handleProofUpload($event, 'paymentProof')"
                                        class="block w-full text-sm text-[var(--tinta-70)] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border file:border-[var(--tinta)] file:text-xs file:font-semibold file:bg-[var(--kertas)] file:text-[var(--tinta)] hover:file:bg-[var(--tinta)] hover:file:text-[var(--kertas-terang)]"
                                    >
                                    <p x-show="compressing" class="text-xs text-[var(--tinta-70)]">
                                        Merapikan ukuran gambar biar cepat terunggah...
                                    </p>

                                    @if ($paymentProof)
                                        <div class="pt-2 flex flex-col items-center gap-2">
                                            @if (method_exists($paymentProof, 'temporaryUrl'))
                                                <img
                                                    src="{{ $paymentProof->temporaryUrl() }}"
                                                    alt="Pratinjau bukti pembayaran"
                                                    class="max-h-48 border border-[var(--garis)] object-contain"
                                                >
                                            @endif
                                            <p class="text-xs font-medium text-[var(--tinta)]">
                                                Bukti siap dikirim: {{ $paymentProof->getClientOriginalName() }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="text-xs text-[var(--tinta-70)]">Format JPG, PNG, WebP, atau HEIC (maksimal 5 MB).</p>
                                    @endif
                                </div>
                                @error('paymentProof')
                                    <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Persetujuan aturan kedai --}}
                            <div class="space-y-2">
                                <label class="inline-flex items-start gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="acceptedRules"
                                        class="mt-1 w-4 h-4 accent-[var(--tinta)]"
                                    >
                                    <span class="text-sm text-[var(--tinta)]">
                                        Saya sudah baca aturan main kedai (datang tepat waktu, karcis berlaku sampai {{ \App\Support\Settings::get('reservation.grace_minutes', 15) }} menit setelah jam pesan).
                                        <button
                                            type="button"
                                            wire:click="$toggle('showRulesDrawer')"
                                            class="underline font-semibold ml-1"
                                        >Baca aturan</button>
                                    </span>
                                </label>
                                @error('acceptedRules')
                                    <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror

                                @if ($showRulesDrawer)
                                    <ol class="p-4 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-2 text-xs text-[var(--tinta-70)] list-decimal list-inside">
                                        @foreach ($this->rulesList as $rule)
                                            <li><strong class="text-[var(--tinta)]">{{ $rule->title }}</strong> — {{ $rule->body }}</li>
                                        @endforeach
                                    </ol>
                                @endif
                            </div>

                            <div class="hidden sm:flex items-center justify-between pt-2">
                                <button
                                    type="button"
                                    wire:click="prevStep"
                                    class="px-5 py-2.5 rounded-full border border-[var(--garis)] text-sm font-medium hover:border-[var(--tinta)]"
                                >Kembali</button>
                                <button
                                    type="button"
                                    wire:click="submitReservation"
                                    wire:loading.attr="disabled"
                                    class="px-8 py-3 rounded-full bg-[var(--mentega)] text-[var(--tinta)] font-display font-bold text-sm hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="submitReservation">Kirim & cetak karcis</span>
                                    <span wire:loading wire:target="submitReservation">Mengirim reservasi...</span>
                                </button>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- DESKTOP (>=1024px): Kanan 5 kol Karcis sticky yang terisi live --}}
                <aside class="hidden lg:block lg:col-span-5 lg:sticky lg:top-24">
                    <x-umco.karcis
                        title="Karcis meja"
                        code="No. ......"
                    >
                        <x-umco.karcis-line label="Hari" :value="$dateDisplay" />
                        <x-umco.karcis-line label="Jam" :value="$timeDisplay" />
                        <x-umco.karcis-line label="Orang" :value="$partySize . ' orang'" />
                        <x-umco.karcis-line label="Tempat" :value="$seatingDisplay" />
                        <x-umco.karcis-line label="Nama" :value="$name !== '' ? $name : '......'" />
                        <x-umco.karcis-line label="Titipan" :value="$itemsSummaryText" />
                        <x-umco.karcis-line label="Total" :value="\App\Support\IndonesianFormat::rupiah($quoteData['subtotal'])" />
                        <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($quoteData['amount_due'])" />
                    </x-umco.karcis>
                </aside>
            </div>

            {{-- HP (<640px): Bar Lanjut + Strip Karcis 64px sticky + bottom sheet --}}
            <div class="sm:hidden fixed bottom-0 inset-x-0 z-40">
                <div class="px-4 py-2.5 bg-[var(--kertas)] border-t border-[var(--garis)] flex items-center justify-between gap-3">
                    @if ($step > 1)
                        <button
                            type="button"
                            wire:click="prevStep"
                            class="px-4 py-2 rounded-full border border-[var(--garis)] text-sm font-medium"
                        >Kembali</button>
                    @else
                        <span class="text-xs text-[var(--tinta-70)]">Langkah {{ $step }}/5</span>
                    @endif

                    @if ($step < 5)
                        <button
                            type="button"
                            wire:click="nextStep"
                            @disabled($step === 3 && $this->menuRequired && ! $quoteData['meets_min_spend'])
                            class="flex-1 py-2.5 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm disabled:opacity-40"
                        >Lanjut</button>
                    @else
                        <button
                            type="button"
                            wire:click="submitReservation"
                            wire:loading.attr="disabled"
                            class="flex-1 py-2.5 rounded-full bg-[var(--mentega)] text-[var(--tinta)] font-display font-bold text-sm"
                        >Kirim & cetak karcis</button>
                    @endif
                </div>

                {{-- Strip karcis tinggi 64px --}}
                <button
                    type="button"
                    @click="mobileSheetOpen = true"
                    class="w-full h-16 px-4 bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)] flex items-center justify-between gap-3 text-left"
                >
                    <span class="text-xs font-semibold truncate">
                        {{ $dateDisplay }} · {{ $this->time ? str_replace(':', '.', $this->time) : '-' }} · {{ $partySize }} orang · {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }}
                    </span>
                    <span class="shrink-0 text-xs font-bold underline">Lihat karcis</span>
                </button>
            </div>

            {{-- Bottom sheet karcis penuh untuk HP --}}
            <div
                x-show="mobileSheetOpen"
                x-transition.opacity
                class="sm:hidden fixed inset-0 z-50 bg-[var(--tinta)]/60 flex items-end"
                @click.self="mobileSheetOpen = false"
            >
                <div class="w-full bg-[var(--kertas)] p-4 pt-5 max-h-[85vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-display font-bold text-base">Isi karcismu</p>
                        <button type="button" @click="mobileSheetOpen = false" class="text-sm font-semibold underline">Tutup</button>
                    </div>
                    <x-umco.karcis title="Karcis meja" code="No. ......" compact>
                        <x-umco.karcis-line label="Hari" :value="$dateDisplay" />
                        <x-umco.karcis-line label="Jam" :value="$timeDisplay" />
                        <x-umco.karcis-line label="Orang" :value="$partySize . ' orang'" />
                        <x-umco.karcis-line label="Tempat" :value="$seatingDisplay" />
                        <x-umco.karcis-line label="Nama" :value="$name !== '' ? $name : '......'" />
                        <x-umco.karcis-line label="Titipan" :value="$itemsSummaryText" />
                        <x-umco.karcis-line label="Total" :value="\App\Support\IndonesianFormat::rupiah($quoteData['subtotal'])" />
                        <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($quoteData['amount_due'])" />
                    </x-umco.karcis>
                </div>
            </div>

        @else
            {{-- LANGKAH 6: SELESAI --}}
            @php
                $karcisPayload = [
                    'code' => $createdCode,
                    'stamp' => 'MENUNGGU CEK',
                    'stampHex' => '#C4472E',
                    'dateLabel' => $dateDisplay,
                    'time' => str_replace(':', '.', $this->time),
                    'partySize' => $partySize,
                    'seatingLabel' => $seatingDisplay,
                    'name' => $name,
                    'phoneDisplay' => $this->normalizedPhonePreview ?: $phoneInput,
                    'itemsSummary' => $itemsSummaryText,
                    'subtotal' => $quoteData['subtotal'],
                    'payLabel' => $payChoiceLabel,
                    'amountDue' => $quoteData['amount_due'],
                ];
            @endphp

            <div class="mt-8 grid lg:grid-cols-12 gap-10 items-start" x-init="if (window.umcoClearNota) window.umcoClearNota()">
                <div class="lg:col-span-6">
                    <x-umco.karcis
                        title="Karcis meja"
                        :code="$createdCode"
                        stamp="MENUNGGU CEK"
                        :torn="true"
                    >
                        <x-umco.karcis-line label="Hari" :value="$dateDisplay" />
                        <x-umco.karcis-line label="Jam" :value="$timeDisplay" />
                        <x-umco.karcis-line label="Orang" :value="$partySize . ' orang'" />
                        <x-umco.karcis-line label="Tempat" :value="$seatingDisplay" />
                        <x-umco.karcis-line label="Nama" :value="$name" />
                        <x-umco.karcis-line label="WA" :value="$this->normalizedPhonePreview ?: $phoneInput" />
                        <x-umco.karcis-line label="Titipan" :value="$itemsSummaryText" />
                        <x-umco.karcis-line label="Total" :value="\App\Support\IndonesianFormat::rupiah($quoteData['subtotal'])" />
                        <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($quoteData['amount_due'])" />

                        <x-slot:stub>
                            <p class="text-xs text-[var(--tinta)]/80 leading-relaxed">
                                Karcis disobek di kasir saat datang. Hangus {{ \App\Support\Settings::get('reservation.grace_minutes', 15) }} menit setelah jam pesan.
                            </p>
                        </x-slot:stub>
                    </x-umco.karcis>
                </div>

                <div class="lg:col-span-6 space-y-5">
                    <div class="p-5 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-2">
                        <p class="text-xs text-[var(--tinta-70)]">Kode booking kamu</p>
                        <p class="font-display font-extrabold text-3xl tracking-tight">{{ $createdCode }}</p>
                        <p class="text-sm text-[var(--tinta-70)]">
                            Bukti transfer sudah masuk ke kasir kami. Biasanya dicek dalam beberapa menit di jam buka kedai.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <a
                            href="{{ $createdWaLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-full bg-[var(--mentega)] text-[var(--tinta)] font-display font-bold text-sm text-center hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                        >
                            Kirim ke WhatsApp UMCO
                        </a>

                        <button
                            type="button"
                            @click="if (window.umcoDownloadKarcis) window.umcoDownloadKarcis(@js($karcisPayload))"
                            class="px-6 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                        >
                            Simpan karcis
                        </button>

                        <a
                            href="{{ route('reservation.calendar', ['code' => $createdCode]) }}"
                            class="px-5 py-3 rounded-full border border-[var(--tinta)] text-sm font-semibold text-center hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                        >
                            Tambah ke kalender
                        </a>
                    </div>

                    <div class="pt-3 border-t border-[var(--garis)] flex flex-wrap items-center justify-between gap-3 text-sm">
                        <a
                            href="{{ route('reservation.status', ['code' => $createdCode]) }}"
                            class="font-semibold underline hover:text-[var(--stempel)]"
                        >
                            Cek status karcis di {{ url('/reservasi/' . $createdCode) }}
                        </a>
                        <a href="{{ url('/') }}" wire:navigate class="text-[var(--tinta-70)] hover:text-[var(--tinta)]">Kembali ke beranda</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
