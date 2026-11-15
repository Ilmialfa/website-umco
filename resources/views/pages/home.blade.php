@php
    $partialMap = [
        'hero' => 'sections.home.s01-hero',
        'promo_banner' => 'sections.home.s03-feature-banner',
        'marquee' => 'sections.home.s02-collage-marquee',
        'signature_reel' => 'sections.home.s04-signature-cards',
        'karcis_teaser' => 'sections.home.s09-karcis-teaser',
        'gallery_diagonal' => 'sections.home.s08-diagonal-gallery',
        'faq' => 'sections.home.s12-faq',
        'testimonials' => 'sections.home.s11-testimonials',
        'instagram_feed' => 'sections.home.s13-instagram',
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
