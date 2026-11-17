@php
    $eyebrow = $section?->data['eyebrow'] ?? 'arah jalan ke sini';
    $heading = $section?->heading ?: 'Temukan UMCO di Kampus 2 UMRI';
    $subheading = $section?->subheading ?: 'Berada di kawasan Kampus 2 UMRI Pekanbaru. Geser, perbesar, aktifkan sudut 3D, atau pindah langsung ke tampilan Google Maps.';
@endphp

<x-umco.interactive-map
    surface="bg-[var(--kertas)]"
    :eyebrow="$eyebrow"
    :heading="$heading"
    :subheading="$subheading"
/>
