@php
    $reservation = $this->reservation;
    $stamp = $this->stampInfo;
    $dateObj = \Carbon\Carbon::parse($reservation->date)->locale('id');
    $dateLabel = $dateObj->isoFormat('ddd, D MMM YYYY');
    $timeLabel = str_replace(':', '.', substr((string) $reservation->time, 0, 5)) . ' WIB';
    $seatingLabel = $reservation->seatingOption?->label ?? 'Bebas di mana saja';
    $itemsSummary = $reservation->items
        ->map(fn ($i) => $i->qty . 'x ' . $i->name_snapshot)
        ->implode(', ');
    if ($itemsSummary === '') {
        $itemsSummary = '-';
    }
    $payChoiceLabel = match ($reservation->payment_type) {
        'full' => 'Lunas',
        'cash' => 'Tunai kasir',
        default => 'DP',
    };
@endphp

<div
    data-module="reservasi-tools"
    data-has-query-items="1"
    wire:poll.30s="refreshStatus"
    x-data="{
        compressing: false,
        async handleReupload(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.compressing = true;
            try {
                const compressed = window.umcoCompressProof
                    ? await window.umcoCompressProof(file)
                    : file;
                $wire.upload('newProof', compressed, () => {
                    this.compressing = false;
                }, () => {
                    this.compressing = false;
                });
            } catch {
                this.compressing = false;
                $wire.upload('newProof', file);
            }
        }
    }"
    class="pt-24 pb-20 min-h-screen bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container max-w-4xl">
        <div class="pb-6 border-b border-[var(--garis)] flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <h1 class="font-display font-extrabold tracking-tight text-3xl sm:text-4xl">
                    Status karcis {{ $reservation->code }}
                </h1>
                <p class="mt-1.5 text-sm text-[var(--tinta-70)]">
                    Halaman ini memperbarui status pembayaran secara otomatis tiap 30 detik.
                </p>
            </div>
            <a href="{{ url('/') }}" wire:navigate class="text-sm font-medium underline">Beranda</a>
        </div>

        @if (! $verified)
            <div class="mt-8 max-w-md p-6 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-4">
                <div>
                    <h2 class="font-display font-bold text-xl">Verifikasi singkat</h2>
                    <p class="mt-1 text-sm text-[var(--tinta-70)]">
                        Masukkan 4 digit terakhir nomor WhatsApp yang kamu pakai waktu memesan.
                    </p>
                </div>

                <form wire:submit="verifyDigits" class="space-y-4">
                    <div>
                        <label for="digitsInput" class="block text-sm font-semibold mb-1.5">4 digit terakhir WA</label>
                        <input
                            id="digitsInput"
                            type="tel"
                            inputmode="numeric"
                            maxlength="4"
                            wire:model="digitsInput"
                            placeholder="5678"
                            class="w-full px-4 py-3 text-base tracking-widest font-display font-bold bg-[var(--kertas)] border border-[var(--garis)] focus:border-[var(--tinta)] focus:outline-none"
                        >
                        @error('digitsInput')
                            <p class="mt-1.5 text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                    >
                        Buka karcis
                    </button>
                </form>
            </div>
        @else
            @php
                $karcisPayload = [
                    'code' => $reservation->code,
                    'stamp' => $stamp['label'],
                    'stampHex' => $stamp['hex'],
                    'dateLabel' => $dateLabel,
                    'time' => str_replace(':', '.', substr((string) $reservation->time, 0, 5)),
                    'partySize' => $reservation->party_size,
                    'seatingLabel' => $seatingLabel,
                    'name' => $reservation->name,
                    'phoneDisplay' => '+' . $reservation->phone_wa,
                    'itemsSummary' => $itemsSummary,
                    'subtotal' => $reservation->subtotal,
                    'payLabel' => $payChoiceLabel,
                    'amountDue' => $reservation->amount_due,
                ];
            @endphp

            <div class="mt-8 grid lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7">
                    <x-umco.karcis
                        title="Karcis meja"
                        :code="$reservation->code"
                        :stamp="$stamp['label']"
                        :stampColor="$stamp['class']"
                        wire:key="status-karcis-{{ $stamp['label'] }}"
                    >
                        <x-umco.karcis-line label="Hari" :value="$dateLabel" />
                        <x-umco.karcis-line label="Jam" :value="$timeLabel" />
                        <x-umco.karcis-line label="Orang" :value="$reservation->party_size . ' orang'" />
                        <x-umco.karcis-line label="Tempat" :value="$seatingLabel" />
                        <x-umco.karcis-line label="Nama" :value="$reservation->name" />
                        <x-umco.karcis-line label="Titipan" :value="$itemsSummary" />
                        <x-umco.karcis-line label="Total" :value="\App\Support\IndonesianFormat::rupiah($reservation->subtotal)" />
                        <x-umco.karcis-line label="Bayar" :value="$payChoiceLabel . ' · ' . \App\Support\IndonesianFormat::rupiah($reservation->amount_due)" />
                    </x-umco.karcis>
                </div>

                <div class="lg:col-span-5 space-y-5">
                    @if ($reservation->payment_status === 'rejected')
                        <div class="p-5 bg-[var(--kertas-terang)] border border-[var(--stempel)] space-y-4">
                            <div>
                                <h2 class="font-display font-bold text-lg text-[var(--stempel)]">Bukti pembayaran perlu diunggah ulang</h2>
                                @if ($reservation->payment_rejected_reason)
                                    <p class="mt-1 text-sm text-[var(--tinta)]">Alasan kasir: {{ $reservation->payment_rejected_reason }}</p>
                                @endif
                            </div>

                            <form wire:submit="reuploadProof" class="space-y-3">
                                <input
                                    type="file"
                                    accept="image/*,.heic,.heif"
                                    @change="handleReupload($event)"
                                    class="block w-full text-sm text-[var(--tinta-70)] file:mr-3 file:py-2 file:px-4 file:rounded-full file:border file:border-[var(--tinta)] file:text-xs file:font-semibold file:bg-[var(--kertas)]"
                                >
                                <p x-show="compressing" class="text-xs text-[var(--tinta-70)]">Merapikan gambar...</p>
                                @error('newProof')
                                    <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
                                @enderror

                                <button
                                    type="submit"
                                    @disabled(! $newProof)
                                    class="w-full py-2.5 rounded-full bg-[var(--mentega)] text-[var(--tinta)] font-display font-bold text-sm disabled:opacity-40"
                                >
                                    Kirim bukti baru
                                </button>
                            </form>
                        </div>
                    @endif

                    @if ($reuploadSuccess)
                        <div class="p-4 bg-[var(--matcha)]/40 border border-[var(--tinta)]/25 text-sm">
                            Bukti bayar baru sudah kami terima. Kasir sedang mengecek ulang.
                        </div>
                    @endif

                    @if ($reservation->status === 'cancelled' && $reservation->cancelled_reason)
                        <div class="p-4 bg-[var(--kertas-terang)] border border-[var(--stempel)] text-sm">
                            Alasan pembatalan: {{ $reservation->cancelled_reason }}
                        </div>
                    @endif

                    <div class="p-5 bg-[var(--kertas-terang)] border border-[var(--garis)] space-y-3 text-sm">
                        <p class="font-semibold">Aksi cepat</p>
                        <div class="flex flex-col gap-2.5">
                            <button
                                type="button"
                                @click="if (window.umcoDownloadKarcis) window.umcoDownloadKarcis(@js($karcisPayload))"
                                class="w-full py-2.5 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm hover:bg-[var(--mentega)] hover:text-[var(--tinta)] transition-colors"
                            >
                                Simpan karcis
                            </button>

                            <a
                                href="{{ route('reservation.calendar', ['code' => $reservation->code]) }}"
                                class="w-full py-2.5 rounded-full border border-[var(--tinta)] text-center font-semibold text-sm hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                            >
                                Tambah ke kalender
                            </a>

                            <a
                                href="{{ $this->umcoWaLink }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full py-2.5 rounded-full bg-[var(--mentega)] text-[var(--tinta)] text-center font-semibold text-sm hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                            >
                                Chat WhatsApp UMCO
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
