@php
    $dayChips = [];
    $dayIndo = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $monthIndo = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    for ($i = 0; $i < 7; $i++) {
        $d = now()->addDays($i);
        $ymd = $d->format('Y-m-d');
        $label = match ($i) {
            0 => 'Hari ini',
            1 => 'Besok',
            default => $dayIndo[$d->dayOfWeek],
        };
        $sub = $d->day . ' ' . $monthIndo[$d->month];
        $dayChips[] = [
            'date' => $ymd,
            'label' => $label,
            'sub' => $sub,
            'fullLabel' => $dayIndo[$d->dayOfWeek] . ', ' . $sub,
            'closedReason' => $closedDates[$ymd] ?? null,
        ];
    }

    $tierPayload = $spendTiers->map(fn ($t) => [
        'party_min' => $t->party_min,
        'party_max' => $t->party_max,
        'min_spend' => $t->min_spend,
        'per_extra_person' => $t->per_extra_person,
    ])->values()->all();

    $defaultDate = collect($dayChips)->first(fn ($c) => empty($c['closedReason']))['date'] ?? now()->format('Y-m-d');
@endphp

<section
    id="karcis-teaser"
    data-nav-theme="light"
    x-data="{
        days: @js($dayChips),
        tiers: @js($tierPayload),
        selectedDate: @js($defaultDate),
        partySize: 2,
        selectedDayText() {
            const d = this.days.find(x => x.date === this.selectedDate);
            return d ? d.fullLabel : this.selectedDate;
        },
        minSpend() {
            const n = this.partySize;
            if (!this.tiers.length) {
                if (n <= 2) return 50000;
                if (n === 3) return 100000;
                if (n === 4) return 125000;
                if (n === 5) return 150000;
                return 150000 + (n - 5) * 25000;
            }
            for (const t of this.tiers) {
                if (n >= t.party_min && (t.party_max === null || n <= t.party_max)) {
                    if (t.per_extra_person && n > t.party_min) {
                        return t.min_spend + ((n - t.party_min) * t.per_extra_person);
                    }
                    return t.min_spend;
                }
            }
            const last = this.tiers[this.tiers.length - 1];
            if (n > last.party_min && last.per_extra_person) {
                return last.min_spend + ((n - last.party_min) * last.per_extra_person);
            }
            return last.min_spend;
        },
        formatRupiah(v) {
            return window.umcoFormatters.formatRupiah(v);
        },
        nextUrl() {
            const nota = window.umcoFormatters.loadNota();
            let url = '{{ url('/reservasi') }}?tanggal=' + encodeURIComponent(this.selectedDate) + '&orang=' + this.partySize;
            if (nota.length) {
                url += '&items=' + encodeURIComponent(nota.map(r => r.slug + ':' + r.qty).join(','));
            }
            return url;
        }
    }"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        <div class="max-w-2xl mx-auto text-center space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[28px] leading-none text-[var(--mentega)]">
                {{ $section?->data['eyebrow'] ?? 'Reservasi Meja' }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $section?->heading ?: 'Pesan meja, karcis terisi sendiri' }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $section?->subheading ?: 'Pilih hari dan jumlah orang dulu di sini, lalu lanjut atur jam, tempat duduk, dan pembayaran.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            {{-- Karcis preview --}}
            <div data-reveal-media class="order-2 lg:order-2 lg:col-span-5">
                <div class="sm:max-w-sm lg:ml-auto">
                    <x-umco.karcis code="No. 0127" title="Karcis meja">
                        <x-umco.karcis-line label="Hari" bind="selectedDayText()" />
                        <x-umco.karcis-line label="Orang" bind="partySize + ' orang'" />
                        <x-umco.karcis-line label="Min. jajan" bind="formatRupiah(minSpend())" />
                        <div class="hidden sm:block space-y-2.5 pt-0.5">
                            <x-umco.karcis-line label="Tempat" value="dipilih di langkah berikut" />
                            <x-umco.karcis-line label="Menu" value="wajib sesuai minimal" />
                        </div>
                    </x-umco.karcis>
                </div>
            </div>

            {{-- Pilihan cepat + Ringkasan Ketentuan --}}
            <div data-reveal="rise" class="order-1 lg:order-1 lg:col-span-7 space-y-7">

                <div class="space-y-3">
                    <p class="text-sm font-semibold">Mau datang hari apa?</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="d in days" :key="d.date">
                            <button
                                type="button"
                                :disabled="Boolean(d.closedReason)"
                                @click="selectedDate = d.date"
                                class="px-4 py-2.5 rounded-full text-left transition-colors cursor-pointer disabled:opacity-45 disabled:cursor-not-allowed"
                                :class="selectedDate === d.date ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                            >
                                <span class="block text-sm font-semibold" :class="d.closedReason ? 'line-through' : ''" x-text="d.label"></span>
                                <span class="block text-[11px] opacity-75" x-text="d.closedReason ? d.closedReason : d.sub"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-sm font-semibold">Berapa orang? (minimal 2)</p>
                    <div class="flex flex-wrap items-center gap-5">
                        <div class="inline-flex items-center gap-4">
                            <button
                                type="button"
                                @click="partySize = Math.max(2, partySize - 1)"
                                :disabled="partySize <= 2"
                                class="w-11 h-11 rounded-full bg-[var(--batu)] text-[var(--tinta)] font-bold text-lg hover:bg-[var(--tinta)] hover:text-[var(--kertas)] disabled:opacity-35 cursor-pointer transition-colors"
                                aria-label="Kurangi jumlah orang"
                            >
                                -
                            </button>
                            <span class="font-display font-semibold text-2xl sm:text-3xl w-10 text-center tabular-nums" x-text="partySize"></span>
                            <button
                                type="button"
                                @click="partySize = Math.min(12, partySize + 1)"
                                :disabled="partySize >= 12"
                                class="w-11 h-11 rounded-full bg-[var(--batu)] text-[var(--tinta)] font-bold text-lg hover:bg-[var(--tinta)] hover:text-[var(--kertas)] disabled:opacity-35 cursor-pointer transition-colors"
                                aria-label="Tambah jumlah orang"
                            >
                                +
                            </button>
                        </div>

                        <p class="text-sm text-[var(--tinta-70)]">
                            Untuk <span class="font-semibold text-[var(--tinta)]" x-text="partySize"></span> orang, minimal belanja
                            <span class="font-semibold text-[var(--tinta)] tabular-nums" x-text="formatRupiah(minSpend())"></span>
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <a :href="nextUrl()" wire:navigate class="btn-umco btn-umco-primary w-full sm:w-auto">
                        <span class="btn-roll">
                            <span>{{ $section?->cta_label ?: 'Lanjut pilih jam, tempat, dan menu' }}</span>
                            <span aria-hidden="true">{{ $section?->cta_label ?: 'Lanjut pilih jam, tempat, dan menu' }}</span>
                        </span>
                        <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                    </a>
                </div>

                @if ($rules->isNotEmpty())
                    <div class="border-t border-[var(--garis)] pt-6 space-y-2" x-data="{ openRule: null }">
                        <p class="text-xs font-semibold tracking-[0.06em] text-[var(--tinta-70)] pb-1">Ketentuan Singkat Kedai</p>
                        <div class="divide-y divide-[var(--garis)]">
                            @foreach ($rules->take(5) as $rIdx => $rule)
                                <div class="py-2.5">
                                    <button
                                        type="button"
                                        @click="openRule = openRule === {{ $rIdx + 1 }} ? null : {{ $rIdx + 1 }}"
                                        class="w-full flex items-center justify-between gap-3 text-left text-sm font-medium hover:text-[var(--mentega)] cursor-pointer"
                                    >
                                        <span>{{ $rIdx + 1 }}. {{ $rule->title }}</span>
                                        <span class="text-xs text-[var(--tinta-70)] shrink-0" x-text="openRule === {{ $rIdx + 1 }} ? 'Tutup' : 'Baca'"></span>
                                    </button>
                                    <p x-show="openRule === {{ $rIdx + 1 }}" x-transition class="pt-1.5 text-xs sm:text-sm text-[var(--tinta-70)] leading-relaxed">
                                        {{ $rule->body }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
