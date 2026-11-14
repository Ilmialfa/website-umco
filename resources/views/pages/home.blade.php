@php
    $partialMap = [
        'hero' => 'sections.home.s01-hero',
        'marquee' => 'sections.home.s02-marquee',
        'led_board' => 'sections.home.s03-led-board',
        'menu_nota' => 'sections.home.s04-menu-nota',
        'signature_reel' => 'sections.home.s05-signature-reel',
        'story_line' => 'sections.home.s06-story-line',
        'brew' => 'sections.home.s07-brew-toy',
        'crowd' => 'sections.home.s08-crowd-chart',
        'karcis_teaser' => 'sections.home.s09-karcis-teaser',
        'rules' => 'sections.home.s10-rules',
        'testimonials' => 'sections.home.s11-testimonials',
        'gallery' => 'sections.home.s12-gallery',
        'location' => 'sections.home.s13-location',
    ];

    $orderedSections = ($sections ?? collect())->isNotEmpty()
        ? $sections
        : collect(array_keys($partialMap))->map(fn ($key) => (object) ['key' => $key, 'heading' => null, 'subheading' => null, 'body' => null, 'cta_label' => null, 'cta_url' => null, 'media' => null, 'data' => null]);
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
