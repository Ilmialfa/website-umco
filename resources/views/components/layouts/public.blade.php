@props([
    'title' => null,
    'description' => null,
    'page' => 'default',
    'initialNavTheme' => 'light',
])

@php
    $brandName = \App\Support\Settings::get('brand.brand_name', 'UMCO');
    $brandShort = \App\Support\Settings::get('brand.brand_short') ?: 'UMCO';
    $tagline = \App\Support\Settings::get('brand.tagline') ?: 'Kedai kopi di Kampus 2 UMRI Pekanbaru';
    $storySnippet = trim((string) \App\Support\Settings::get('brand.story_snippet', ''));
    $metaTitle = $title ?? \App\Support\Settings::get('seo.meta_title', 'UMCO — Kedai Kopi di Kampus UMRI Pekanbaru');
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

    <div
        data-module="scroll-meter"
        class="hidden lg:flex fixed bottom-5 right-6 z-40 items-center px-3.5 h-9 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis-kuwat)] text-xs font-medium tabular-nums select-none pointer-events-none"
        aria-hidden="true"
    >
        0 cm
    </div>

    @include('partials.public.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
