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
                class="flex-1 py-2.5 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-sm"
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
    style="display: none;"
    class="sm:hidden fixed inset-0 z-50 bg-[var(--tinta)]/60 flex items-end"
    @click.self="mobileSheetOpen = false"
    @wheel.self.prevent
    @touchmove.self.prevent
>
    <div data-lenis-prevent class="w-full bg-[var(--kertas)] p-4 pt-5 max-h-[85vh] overflow-y-auto overscroll-contain">
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
