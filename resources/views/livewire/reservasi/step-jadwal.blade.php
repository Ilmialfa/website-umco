{{-- LANGKAH 1: KAPAN & BERAPA ORANG --}}
<section
    class="{{ $step !== 1 ? 'hidden' : 'space-y-6 umco-step-enter' }}"
    aria-hidden="{{ $step !== 1 ? 'true' : 'false' }}"
    @if ($step !== 1) inert @endif
>
    <div>
        <h2 class="font-display font-bold text-xl sm:text-2xl">1. Kapan dan berapa orang</h2>
        <p class="mt-1 text-sm text-[var(--tinta-70)]">Pilih tanggal hingga {{ \App\Support\Settings::get('reservation.max_advance_days', 14) }} hari ke depan.</p>
    </div>

    {{-- Jumlah orang --}}
    <div class="p-4 sm:p-5 rounded-2xl bg-[var(--kertas-terang)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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
            class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-white transition-colors"
        >
            Lanjut ke pilih tempat
        </button>
    </div>
</section>
