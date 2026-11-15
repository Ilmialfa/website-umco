@php
    $partialMap = [
        'hero' => 'sections.home.s01-hero',
        'marquee' => 'sections.home.s02-collage-marquee',
        'promo_banner' => 'sections.home.s03-feature-banner',
        'signature_reel' => 'sections.home.s04-signature-cards',
        'menu_nota' => 'sections.home.s04-menu-nota',
        'karcis_teaser' => 'sections.home.s09-karcis-teaser',
        'gallery_portrait' => 'sections.home.s07-portrait-gallery',
        'gallery_diagonal' => 'sections.home.s08-diagonal-gallery',
        'testimonials' => 'sections.home.s11-testimonials',
        'cta_footer' => 'sections.home.s10-cta',
    ];

    $orderedSections = ($sections ?? collect())->isNotEmpty()
        ? $sections
        : collect(array_keys($partialMap))->map(fn ($key) => (object) [
            'key' => $key,
            'heading' => null,
            'subheading' => null,
            'body' => null,
            'cta_label' => null,
            'cta_url' => null,
            'media' => null,
            'data' => null,
        ]);
@endphp

<x-layouts.public
    page="home"
    initial-nav-theme="dark"
    :title="\App\Support\Settings::get('seo.meta_title', 'UMCO — Kedai Kopi di Kampus UMRI Pekanbaru')"
>
    @foreach ($orderedSections as $section)
        @if (isset($partialMap[$section->key]))
            @include($partialMap[$section->key], ['section' => $section])
        @endif
    @endforeach
</x-layouts.public>
