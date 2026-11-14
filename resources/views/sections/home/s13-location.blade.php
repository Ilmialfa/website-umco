@php
    $address = \App\Support\Settings::get('contact.address', 'Kampus 2 UMRI, Jl. Tuanku Tambusai No. 2, Delima, Pekanbaru');
    $openTime = str_replace(':', '.', \App\Support\Settings::get('hours.opening_time', '08:00'));
    $closeTime = str_replace(':', '.', \App\Support\Settings::get('hours.closing_time', '22:00'));
    $mapsUrl = \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru');
    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = preg_replace('/\D+/', '', (string) $rawWa);
    if (str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $waLink = 'https://wa.me/' . $waDigits;
    $coords = \App\Support\Settings::get('contact.coordinates', '0.4999292, 101.4155284');
    $previewImg = $section?->media['map_preview'] ?? '/images/bistro-patio-table.jpg';

    $weekDays = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];
    $todayIso = now()->dayOfWeekIso;
@endphp

<section
    id="lokasi"
    data-nav-theme="light"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] border-t border-[var(--garis)]"
    x-data="{ interactiveMap: false }"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            <div class="lg:col-span-6 space-y-6">
                <div class="space-y-3">
                    <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                        {{ $section?->heading ?: 'Singgah ke UMCO' }}
                    </h2>
                    <p class="font-display font-semibold text-xl sm:text-2xl leading-snug">
                        {{ $address }}
                    </p>
                </div>

                <div class="border-t border-[var(--garis)] pt-4 max-w-sm space-y-1.5 text-sm">
                    @foreach ($weekDays as $iso => $dayLabel)
                        <div class="flex justify-between py-1 {{ $iso === $todayIso ? 'font-bold text-[var(--tinta)]' : 'text-[var(--tinta-70)]' }}">
                            <span>{{ $dayLabel }}{{ $iso === $todayIso ? ' (hari ini)' : '' }}</span>
                            <span class="tabular-nums">{{ $openTime }} - {{ $closeTime }} WIB</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="btn-umco btn-umco-primary">
                        Buka di Google Maps
                    </a>
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn-umco btn-umco-secondary">
                        Chat WhatsApp
                    </a>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-3">
                <div class="relative aspect-[16/10] w-full bg-[var(--garis)]/40 overflow-hidden border border-[var(--garis)]">
                    <template x-if="!interactiveMap">
                        <div class="relative w-full h-full">
                            <img
                                src="{{ asset($previewImg) }}"
                                alt="Lokasi UMCO di dalam kawasan Kampus 2 UMRI Pekanbaru"
                                loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-x-4 bottom-4 flex flex-wrap items-center justify-between gap-3 bg-[var(--kertas-terang)] border border-[var(--tinta)] px-4 py-3">
                                <span class="text-xs sm:text-sm font-medium tabular-nums">Koordinat: {{ $coords }}</span>
                                <button
                                    type="button"
                                    @click="interactiveMap = true"
                                    class="text-sm font-semibold underline underline-offset-4 cursor-pointer"
                                >
                                    Tampilkan peta interaktif
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="interactiveMap">
                        <iframe
                            title="Peta lokasi UMCO Kampus 2 UMRI Pekanbaru"
                            src="https://maps.google.com/maps?q=0.4999292,101.4155284&z=16&output=embed"
                            loading="lazy"
                            class="w-full h-full border-0"
                        ></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>
