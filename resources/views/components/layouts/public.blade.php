@props([
    'title' => null,
    'description' => null,
    'page' => 'default',
    'initialNavTheme' => 'light',
])

@php
    $brandName = \App\Support\Settings::get('brand.brand_name', 'UMCO');
    $brandShort = \App\Support\Settings::get('brand.brand_short') ?: 'UMCO';
    $tagline = \App\Support\Settings::get('brand.tagline') ?: 'Cafe di Kampus 2 UMRI Pekanbaru';
    $storySnippet = trim((string) \App\Support\Settings::get('brand.story_snippet', ''));
    $metaTitle = $title ?? \App\Support\Settings::get('seo.meta_title', 'UMCO — Cafe di Kampus UMRI Pekanbaru');
    $metaDescription = $description ?? (\App\Support\Settings::get('seo.meta_description') ?: $storySnippet ?: ($tagline . '. Tempat nongkrong, nugas, dan pesan meja dengan menu siap saji.'));
    $ogImageRaw = trim((string) \App\Support\Settings::get('seo.og_image', ''));
    $ogImageUrl = $ogImageRaw !== ''
        ? (str_starts_with($ogImageRaw, 'http') || str_starts_with($ogImageRaw, '/') ? asset($ogImageRaw) : asset('storage/' . ltrim($ogImageRaw, '/')))
        : asset('/images/og-cover.webp');
    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = method_exists(\App\Support\PhoneNumber::class, 'normalizeBusiness')
        ? \App\Support\PhoneNumber::normalizeBusiness($rawWa)
        : preg_replace('/\D+/', '', (string) $rawWa);
    if ($waDigits && str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $waLink = $waDigits ? 'https://wa.me/' . $waDigits : '#';
    $mapsUrl = \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru');
    $instagramUrl = \App\Support\Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house');
    $openTime = \App\Support\Settings::get('hours.opening_time', '08.00');
    $closeTime = \App\Support\Settings::get('hours.closing_time', '22.00');
    $operatingDays = trim((string) \App\Support\Settings::get('hours.operating_days', '')) ?: "Buka setiap hari pukul {$openTime} - {$closeTime} WIB";
    $address = \App\Support\Settings::get('contact.address', 'Kampus 2 UMRI, Jl. Tuanku Tambusai Ujung, Delima, Pekanbaru');
    $coordinates = trim((string) \App\Support\Settings::get('contact.coordinates', ''));
    $plusCode = trim((string) \App\Support\Settings::get('contact.plus_code', ''));
    $priceRange = trim((string) \App\Support\Settings::get('rating.price_range', '')) ?: 'Rp 13.000 - Rp 25.500';
    $smoothScroll = \App\Support\Settings::get('motion.smooth_scroll', true) ? '1' : '0';

    $navItems = [
        ['label' => 'Menu', 'href' => url('/menu'), 'active' => request()->is('menu*')],
        ['label' => 'Galeri', 'href' => url('/galeri'), 'active' => request()->is('galeri*')],
        ['label' => 'Cerita', 'href' => url('/cerita'), 'active' => request()->is('cerita*')],
    ];
    $collabTemplate = \App\Support\Settings::get('contact.collab_template', 'Halo UMCO, saya ingin menghubungi terkait acara atau kolaborasi.');
    $collabWaLink = $waDigits ? ('https://wa.me/' . $waDigits . '?text=' . rawurlencode($collabTemplate)) : $waLink;
    $footerCutout = \App\Support\Settings::get('brand.footer_cutout_image', '/images/footer-cutout.webp') ?: '/images/footer-cutout.webp';
    $footerWordmark = \App\Support\Settings::get('brand.footer_wordmark', 'UMCO') ?: 'UMCO';

    $geoData = null;
    if ($coordinates !== '' && str_contains($coordinates, ',')) {
        [$rawLat, $rawLng] = array_map('trim', explode(',', $coordinates, 2));
        if (is_numeric($rawLat) && is_numeric($rawLng)) {
            $geoData = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $rawLat,
                'longitude' => (float) $rawLng,
            ];
        }
    }

    $jsonLdPayload = [
        '@context' => 'https://schema.org',
        '@type' => 'CafeOrCoffeeShop',
        'name' => $brandName,
        'alternateName' => $brandShort,
        'description' => $metaDescription,
        'image' => $ogImageUrl,
        'url' => url('/'),
        'telephone' => $rawWa,
        'priceRange' => $priceRange,
        'hasMap' => $mapsUrl,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $address,
            'addressLocality' => 'Pekanbaru',
            'addressRegion' => 'Riau',
            'postalCode' => '28291',
            'addressCountry' => 'ID',
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'opens' => str_replace('.', ':', substr((string) $openTime, 0, 5)),
                'closes' => str_replace('.', ':', substr((string) $closeTime, 0, 5)),
            ],
        ],
    ];
    if ($geoData !== null) {
        $jsonLdPayload['geo'] = $geoData;
    }
@endphp

<!DOCTYPE html>
<html lang="id">
@include('partials.public.head')
<body
    data-page="{{ $page }}"
    data-smooth-scroll="{{ $smoothScroll }}"
    class="min-h-screen flex flex-col bg-[var(--kertas)] text-[var(--tinta)] font-sans antialiased overflow-x-clip"
>
    @include('partials.icons-sprite')

    <div
        id="umco-nav-skeleton"
        class="hidden fixed inset-0 z-[70] bg-[var(--kertas)] pt-24 px-5 sm:px-8 pointer-events-none"
        aria-hidden="true"
    >
        <div class="umco-container space-y-6">
            <div class="skeleton-block h-10 w-56 sm:w-80"></div>
            <div class="space-y-3 max-w-xl">
                <div class="skeleton-block h-5 w-full"></div>
                <div class="skeleton-block h-5 w-3/4"></div>
            </div>
            <div class="skeleton-block h-56 sm:h-72 w-full"></div>
        </div>
    </div>

    @include('partials.public.navbar')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @if (! request()->routeIs('menu', 'reservation', 'reservation.status'))
        <div
            x-data="{
                nota: [],
                open: false,
                sync() {
                    this.nota = window.umcoFormatters ? window.umcoFormatters.loadNota() : [];
                    if (!this.nota.length) this.open = false;
                },
                init() {
                    this.sync();
                    window.addEventListener('umco:nota-updated', () => this.sync());
                    window.addEventListener('storage', (e) => { if (e.key === 'umco_nota_v1') this.sync(); });
                    document.addEventListener('livewire:navigated', () => this.sync());
                },
                qtyCount() {
                    return this.nota.reduce((s, r) => s + Number(r.qty || 1), 0);
                },
                totalAmount() {
                    return this.nota.reduce((s, r) => s + (Number(r.price || 0) * Number(r.qty || 1)), 0);
                },
                formatRupiah(v) {
                    return window.umcoFormatters ? window.umcoFormatters.formatRupiah(v) : ('Rp ' + Number(v || 0).toLocaleString('id-ID'));
                },
                inc(idx) {
                    if (!this.nota[idx]) return;
                    this.nota[idx].qty = Number(this.nota[idx].qty || 1) + 1;
                    window.umcoFormatters?.saveNota(this.nota);
                },
                dec(idx) {
                    if (!this.nota[idx]) return;
                    if (Number(this.nota[idx].qty || 1) > 1) {
                        this.nota[idx].qty = Number(this.nota[idx].qty) - 1;
                    } else {
                        this.nota.splice(idx, 1);
                    }
                    window.umcoFormatters?.saveNota(this.nota);
                },
                clearAll() {
                    this.nota = [];
                    this.open = false;
                    window.umcoFormatters?.saveNota([]);
                },
                reserveUrl() {
                    const encoded = this.nota.map(r => r.slug + ':' + r.qty).join(',');
                    return encoded ? '{{ url('/reservasi') }}?items=' + encodeURIComponent(encoded) : '{{ url('/reservasi') }}';
                }
            }"
            x-show="qtyCount() > 0"
            @keydown.escape.window="open = false"
            style="display: none;"
            class="fixed bottom-5 right-4 sm:right-6 z-40 flex flex-col items-end gap-2.5 max-w-[calc(100vw-2rem)]"
        >
            {{-- Mini-drawer daftar menu tersimpan di Nota --}}
            <div
                x-show="open"
                x-transition.opacity.duration.180ms
                @click.outside="open = false"
                class="w-[330px] sm:w-[370px] max-w-full rounded-2xl bg-[var(--kertas-terang)] text-[var(--tinta)] p-4 sm:p-5 space-y-3.5"
                role="dialog"
                aria-label="Nota pesanan tersimpan"
            >
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="font-display font-bold text-base leading-tight">Nota pesananmu</p>
                        <p class="text-xs text-[var(--tinta-70)]">Otomatis terbawa saat pesan meja.</p>
                    </div>
                    <button type="button" @click="open = false" class="text-xs font-semibold text-[var(--tinta-70)] hover:text-[var(--tinta)] underline underline-offset-4 cursor-pointer">
                        Tutup
                    </button>
                </div>

                <div class="max-h-56 overflow-y-auto divide-y divide-[var(--garis)] pr-0.5">
                    <template x-for="(row, idx) in nota" :key="(row.key || row.slug) + idx">
                        <div class="py-2.5 flex items-center justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-semibold truncate" x-text="row.name"></p>
                                <p class="text-xs text-[var(--tinta-70)] truncate" x-text="[row.variant_label, row.temperature, formatRupiah(row.price)].filter(Boolean).join(' · ')"></p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="dec(idx)" aria-label="Kurangi jumlah" class="w-7 h-7 rounded-full bg-[var(--batu)] hover:bg-[var(--tinta)] hover:text-white text-xs font-bold transition-colors cursor-pointer">-</button>
                                <span class="w-5 text-center text-xs font-semibold tabular-nums" x-text="row.qty"></span>
                                <button type="button" @click="inc(idx)" aria-label="Tambah jumlah" class="w-7 h-7 rounded-full bg-[var(--batu)] hover:bg-[var(--tinta)] hover:text-white text-xs font-bold transition-colors cursor-pointer">+</button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-2 border-t border-[var(--garis)] flex items-baseline justify-between">
                    <span class="text-xs font-medium text-[var(--tinta-70)]">Estimasi menu</span>
                    <span class="font-display font-bold text-base tabular-nums text-[var(--mentega-tua)]" x-text="formatRupiah(totalAmount())"></span>
                </div>

                <div class="flex items-center gap-2 pt-0.5">
                    <button type="button" @click="clearAll()" class="px-3.5 h-10 rounded-full bg-[var(--batu)] hover:bg-[var(--garis-kuwat)] text-xs font-semibold text-[var(--tinta-70)] transition-colors cursor-pointer">
                        Kosongkan
                    </button>
                    <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-primary flex-1 min-h-0 h-10 px-4 text-xs sm:text-sm">
                        <span>Lanjut reservasi</span>
                        <svg class="w-3.5 h-3.5 shrink-0"><use href="#icon-arrow-right"/></svg>
                    </a>
                </div>
            </div>

            {{-- Floating pill ringkas di pojok kanan bawah --}}
            <div class="inline-flex items-center gap-1.5 rounded-full bg-[#141916] text-white p-1 pl-4">
                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex items-center gap-2 text-xs font-medium tabular-nums cursor-pointer pr-1.5 py-1"
                >
                    <span class="w-2 h-2 rounded-full bg-[#34D399] shrink-0" aria-hidden="true"></span>
                    <span>Nota · <strong x-text="qtyCount()"></strong> item</span>
                    <span class="text-white/60">·</span>
                    <span class="font-semibold text-[#DCEBE1]" x-text="formatRupiah(totalAmount())"></span>
                </button>
                <a
                    :href="reserveUrl()"
                    wire:navigate
                    class="inline-flex items-center gap-1 rounded-full bg-[var(--mentega)] hover:bg-white hover:text-[#141916] text-white text-xs font-semibold px-3.5 h-8 transition-colors whitespace-nowrap"
                >
                    <span>Lanjut Reservasi</span>
                    <svg class="w-3 h-3 shrink-0"><use href="#icon-arrow-right"/></svg>
                </a>
            </div>
        </div>
    @endif

    @include('partials.public.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
