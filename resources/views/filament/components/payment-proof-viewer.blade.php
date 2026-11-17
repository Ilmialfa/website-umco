@php
    $rawState = isset($proofPath) ? $proofPath : ($getState() ?? null);
    $paths = collect(is_array($rawState) ? $rawState : [$rawState])
        ->filter(fn ($p): bool => is_string($p) && trim($p) !== '')
        ->values();

    $reservationCode = $code ?? (isset($getRecord) ? ($getRecord()?->code ?? 'UMCO') : 'UMCO');
    $inModal = (bool) ($isModal ?? false);
@endphp

@if ($paths->isEmpty())
    <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
        Belum ada bukti yang diunggah.
    </div>
@else
    <div
        x-data="{ openSrc: null, openName: '' }"
        @keydown.escape.window="openSrc = null"
        class="space-y-4"
    >
        @if ($inModal && isset($amountDueText))
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-slate-50 px-4 py-2.5 text-sm">
                <div>
                    <span class="text-slate-500">Tagihan:</span>
                    <span class="font-semibold text-slate-900">{{ $amountDueText }}</span>
                </div>
                @if (! empty($paymentStatusLabel))
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                        {{ $paymentStatusLabel }}
                    </span>
                @endif
            </div>
        @endif

        <div class="{{ $paths->count() > 1 ? 'grid grid-cols-1 sm:grid-cols-2 gap-4' : ($inModal ? '' : 'max-w-md') }}">
            @foreach ($paths as $index => $path)
                @php
                    $cleanPath = ltrim(trim($path), '/');
                    $url = str_starts_with($cleanPath, 'http') || str_starts_with($cleanPath, '/')
                        ? $cleanPath
                        : asset('storage/'.$cleanPath);
                    $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION) ?: 'jpg');
                    $suffix = $paths->count() > 1 ? '-'.($index + 1) : '';
                    $downloadName = 'bukti-'.$reservationCode.$suffix.'.'.$ext;
                @endphp

                <div class="rounded-xl bg-slate-50 p-3 space-y-3">
                    <button
                        type="button"
                        @click="openSrc = @js($url); openName = @js($downloadName)"
                        class="group relative block w-full overflow-hidden rounded-lg bg-white text-left focus:outline-none focus:ring-2 focus:ring-emerald-700/40 cursor-zoom-in"
                        title="Klik untuk memperbesar bukti pembayaran"
                    >
                        <img
                            src="{{ $url }}"
                            alt="Bukti transfer {{ $reservationCode }}"
                            loading="lazy"
                            class="mx-auto w-full {{ $inModal ? 'max-h-[60vh]' : 'max-h-72' }} object-contain transition-transform duration-200 group-hover:scale-[1.01]"
                        />
                        <span class="pointer-events-none absolute bottom-2 right-2 rounded-full bg-slate-900/75 px-2.5 py-1 text-[11px] font-medium text-white opacity-90 group-hover:opacity-100">
                            Klik untuk perbesar
                        </span>
                    </button>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            @click="openSrc = @js($url); openName = @js($downloadName)"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer"
                        >
                            <x-heroicon-o-magnifying-glass-plus class="h-4 w-4 text-slate-500" />
                            <span>Perbesar</span>
                        </button>

                        <a
                            href="{{ $url }}"
                            download="{{ $downloadName }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-900"
                        >
                            <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                            <span>Simpan Bukti</span>
                        </a>

                        <a
                            href="{{ $url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4 text-slate-500" />
                            <span>Tab Baru</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Lightbox perbesar layar penuh --}}
        <template x-teleport="body">
            <div
                x-show="openSrc !== null"
                x-transition.opacity.duration.150ms
                @click.self="openSrc = null"
                style="display: none;"
                class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-950/85 p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                aria-label="Pratinjau Bukti Pembayaran"
            >
                <div class="mb-3 flex w-full max-w-4xl items-center justify-between gap-3">
                    <span class="truncate text-sm font-semibold text-white" x-text="openName"></span>
                    <div class="flex items-center gap-2 shrink-0">
                        <a
                            :href="openSrc"
                            :download="openName"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-600"
                        >
                            <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                            <span>Simpan Gambar</span>
                        </a>
                        <a
                            :href="openSrc"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3.5 py-2 text-xs font-semibold text-white hover:bg-white/25"
                        >
                            <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                            <span>Tab Baru</span>
                        </a>
                        <button
                            type="button"
                            @click="openSrc = null"
                            class="inline-flex items-center gap-1 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 hover:bg-slate-100 cursor-pointer"
                        >
                            <x-heroicon-o-x-mark class="h-4 w-4" />
                            <span>Tutup</span>
                        </button>
                    </div>
                </div>

                <div @click.self="openSrc = null" class="flex max-h-[84vh] w-full max-w-4xl items-center justify-center overflow-auto rounded-xl bg-slate-900/50 p-2">
                    <img
                        :src="openSrc"
                        :alt="openName"
                        class="max-h-[80vh] max-w-full rounded-lg object-contain"
                    />
                </div>
            </div>
        </template>
    </div>
@endif
