{{-- LANGKAH 5: BAYAR --}}
<section
    class="{{ $step !== 5 ? 'hidden' : 'space-y-6 umco-step-enter' }}"
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
            class="p-4 rounded-2xl text-left transition-colors {{ $paymentType === 'dp'
                ? 'bg-[var(--tinta)] text-[var(--kertas-terang)]'
                : 'bg-[var(--kertas-terang)] hover:bg-white' }}"
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
                class="p-4 rounded-2xl text-left transition-colors {{ $paymentType === 'full'
                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)]'
                    : 'bg-[var(--kertas-terang)] hover:bg-white' }}"
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
    <div class="p-5 rounded-2xl bg-[var(--kertas-terang)] grid sm:grid-cols-12 gap-6 items-center">
        <div class="sm:col-span-5 flex flex-col items-center">
            <img
                src="{{ $this->qrisUrl }}"
                alt="QRIS statis pembayaran UMCO House"
                class="w-48 h-auto rounded-xl bg-white p-2"
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

            @php $paymentNotes = trim((string) \App\Support\Settings::get('payment.payment_notes', '')); @endphp
            @if ($paymentNotes !== '')
                <p class="pt-2 border-t border-[var(--garis)] text-xs text-[var(--tinta-70)] leading-relaxed">
                    {{ $paymentNotes }}
                </p>
            @endif
        </div>
    </div>

    {{-- Unggah bukti bayar --}}
    <div class="space-y-2">
        <label for="proofInput" class="block text-sm font-semibold">
            Unggah bukti transfer (wajib)
        </label>
        <div class="p-5 rounded-2xl bg-[var(--kertas-terang)] border border-dashed border-[var(--tinta)]/35 text-center space-y-3">
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

    {{-- Persetujuan aturan cafe --}}
    <div class="space-y-2">
        <label class="inline-flex items-start gap-3 cursor-pointer">
            <input
                type="checkbox"
                wire:model="acceptedRules"
                class="mt-1 w-4 h-4 accent-[var(--tinta)]"
            >
            <span class="text-sm text-[var(--tinta)]">
                Saya sudah baca aturan main cafe (datang tepat waktu, karcis berlaku sampai {{ \App\Support\Settings::get('reservation.grace_minutes', 15) }} menit setelah jam pesan).
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
            <ol class="p-4 rounded-2xl bg-[var(--kertas-terang)] space-y-2 text-xs text-[var(--tinta-70)] list-decimal list-inside">
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
            class="px-8 py-3 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-sm hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="submitReservation">Kirim & cetak karcis</span>
            <span wire:loading wire:target="submitReservation">Mengirim reservasi...</span>
        </button>
    </div>
</section>
