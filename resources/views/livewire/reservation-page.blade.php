@php
    $quoteData = $this->quote;
    $dateObj = $this->date ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->date)->locale('id') : null;
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
    id="reservasi-wizard"
    data-module="reservasi-tools"
    data-nav-bg="#F6F4ED"
    data-has-query-items="{{ $hasQueryItems ? '1' : '0' }}"
    @umco-step-changed.window="window.scrollTo({ top: 0, behavior: 'smooth' })"
    @keydown.escape.window="mobileSheetOpen = false"
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
                        Tunjukkan kode karcis di kasir waktu datang. Kami tunggu {{ \App\Support\Settings::get('reservation.grace_minutes', 15) }} menit setelah jam pesan.
                    @else
                        Pilih meja, tambah menu sekalian bila perlu, lalu bayar DP {{ $quoteData['dp_percentage'] }}% atau lunas.
                    @endif
                </p>
            </div>
        </div>

        @if ($step < 6)
            {{-- Bar langkah horizontal (1 Kapan · 2 Tempat · 3 Menu · 4 Data · 5 Bayar) --}}
            <nav aria-label="Langkah reservasi" class="py-4 overflow-x-auto no-scrollbar">
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
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-sm transition-colors duration-[180ms] {{ $isCurrent
                                    ? 'bg-[var(--mentega)] text-white font-semibold'
                                    : ($canJump
                                        ? 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)] cursor-pointer'
                                        : 'text-[var(--tinta-40)] cursor-default') }}"
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
            <div class="hidden sm:block lg:hidden sticky top-16 z-30 mt-4 rounded-2xl bg-[var(--kertas-terang)] text-[var(--tinta)] border-t-[3px] border-t-[var(--mentega)] px-5 py-3.5">
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
                        class="shrink-0 px-3.5 py-1.5 rounded-full bg-[var(--batu)] text-xs font-semibold hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
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

            {{-- GRID UTAMA: Kiri 7 kol langkah aktif (tanpa wrapper 500% agar scroll selalu alami), Kanan 5 kol Karcis sticky --}}
            <div class="mt-6 lg:mt-8 lg:grid lg:grid-cols-12 lg:gap-10 items-start">
                <div class="lg:col-span-7 min-w-0">
                    <div>
                        @include('livewire.reservasi.step-jadwal')
                        @include('livewire.reservasi.step-tempat')
                        @include('livewire.reservasi.step-menu')
                        @include('livewire.reservasi.step-data')
                        @include('livewire.reservasi.step-bayar')
                    </div>
                </div>

                @include('livewire.reservasi.ringkasan-karcis')
            </div>

            @include('livewire.reservasi.mobile-bar')

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
                    <div class="p-5 rounded-2xl bg-[var(--kertas-terang)] space-y-2">
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
                            class="px-6 py-3 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-sm text-center hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                        >
                            Kirim ke WhatsApp UMCO
                        </a>

                        <button
                            type="button"
                            @click="if (window.umcoDownloadKarcis) window.umcoDownloadKarcis(@js($karcisPayload))"
                            class="px-6 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-white transition-colors"
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
