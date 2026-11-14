@php
    $verdicts = $section?->data['verdicts'] ?? [];
@endphp

<section
    id="seduh"
    data-nav-theme="light"
    data-module="brew-toy"
    data-verdicts='@json($verdicts)'
    class="umco-section bg-[var(--matcha)] text-[var(--tinta)] edge-torn-top"
>
    <div class="umco-container space-y-8">
        <div class="max-w-2xl space-y-2">
            <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                {{ $section?->heading ?: 'Coba takar seduhanmu' }}
            </h2>
            <p class="type-body text-[var(--tinta)]/85">
                {{ $section?->subheading ?: 'Geser tiga tuas di bawah ini untuk mengira-ngira rasa.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            {{-- Gelas SVG (atas di HP/tablet, kanan di desktop) --}}
            <div class="lg:col-span-6 lg:order-2 flex flex-col items-center text-center space-y-4 py-4">
                <svg viewBox="0 0 220 260" class="w-40 sm:w-52 lg:w-64 h-auto" aria-hidden="true">
                    <defs>
                        <clipPath id="brewCupClip">
                            <path d="M46 42 L174 42 L156 226 L64 226 Z" />
                        </clipPath>
                    </defs>
                    <rect data-brew-liquid x="40" y="96" width="140" height="135" fill="#6B4226" fill-opacity="0.88" clip-path="url(#brewCupClip)" />
                    <path d="M46 42 L174 42 L156 226 L64 226 Z" fill="none" stroke="#22263A" stroke-width="4" stroke-linejoin="round" />
                    <line x1="60" y1="238" x2="160" y2="238" stroke="#22263A" stroke-width="4" stroke-linecap="round" />
                </svg>

                <div class="space-y-1 max-w-md">
                    <p class="text-xs font-medium text-[var(--tinta)]/70 tabular-nums">
                        Rasio seduh <span data-brew-ratio class="font-semibold text-[var(--tinta)]">1 : 15,0</span>
                    </p>
                    <p data-brew-verdict class="font-hand text-3xl sm:text-4xl leading-none text-[var(--tinta)]">
                        Pas. Ini yang biasanya kami keluarkan di bar.
                    </p>
                </div>
            </div>

            {{-- 3 Tuas slider --}}
            <div class="lg:col-span-6 lg:order-1 space-y-6 bg-[var(--kertas-terang)] border border-[var(--tinta)]/20 p-6 sm:p-8">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold">
                        <label for="brew-coffee">Takaran kopi</label>
                        <span data-brew-coffee-val class="tabular-nums">15 gram</span>
                    </div>
                    <input id="brew-coffee" data-brew-coffee type="range" min="10" max="25" value="15" step="1" class="umco-range">
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold">
                        <label for="brew-temp">Suhu air panas</label>
                        <span data-brew-temp-val class="tabular-nums">92 C</span>
                    </div>
                    <input id="brew-temp" data-brew-temp type="range" min="80" max="100" value="92" step="1" class="umco-range">
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold">
                        <label for="brew-time">Lama tuang</label>
                        <span data-brew-time-val class="tabular-nums">3 menit</span>
                    </div>
                    <input id="brew-time" data-brew-time type="range" min="1" max="5" value="3" step="1" class="umco-range">
                </div>

                <p class="pt-2 border-t border-[var(--garis)] text-xs text-[var(--tinta-70)]">
                    Cuma buat seru-seruan di layar. Menu ini tidak bisa dipesan khusus.
                </p>
            </div>
        </div>
    </div>
</section>
