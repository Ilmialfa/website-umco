{{-- LANGKAH 4: DATA KAMU --}}
<section
    class="{{ $step !== 4 ? 'hidden' : 'space-y-5 umco-step-enter' }}"
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
            class="w-full px-4 py-3 text-base rounded-2xl bg-[var(--kertas-terang)] border border-transparent focus:border-[var(--tinta)]/40 focus:outline-none transition-colors"
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
            class="w-full px-4 py-3 text-base rounded-2xl bg-[var(--kertas-terang)] border border-transparent focus:border-[var(--tinta)]/40 focus:outline-none transition-colors"
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
            class="w-full px-4 py-3 text-base rounded-2xl bg-[var(--kertas-terang)] border border-transparent focus:border-[var(--tinta)]/40 focus:outline-none transition-colors"
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
            class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-white transition-colors"
        >
            Lanjut ke pembayaran
        </button>
    </div>
</section>
