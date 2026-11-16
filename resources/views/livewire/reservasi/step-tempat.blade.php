{{-- LANGKAH 2: MAU DUDUK DI MANA --}}
<section
    class="{{ $step !== 2 ? 'hidden' : 'space-y-6 umco-step-enter' }}"
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
            class="w-full px-4 py-3 text-base rounded-2xl bg-[var(--kertas-terang)] border border-transparent focus:border-[var(--tinta)]/40 focus:outline-none transition-colors"
        ></textarea>
        <p class="mt-2 text-xs sm:text-sm text-[var(--tinta-70)]">
            Kami usahakan sesuai permintaan, tapi tergantung meja yang kosong.
        </p>
    </div>

    @if ($this->floorPlanUrl)
        <div class="p-4 rounded-2xl bg-[var(--kertas-terang)] space-y-2">
            <p class="text-xs font-semibold text-[var(--tinta-70)]">Denah cafe (panduan area)</p>
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
            class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-white transition-colors"
        >
            Lanjut pilih menu
        </button>
    </div>
</section>
