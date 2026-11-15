@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Menu Andalan';
    $heading = $section?->heading ?: 'Racikan yang sering balik dipesan';
    $subheading = $section?->subheading ?: 'Lima sajian terfavorit di meja bar dan ruang diskusi UMCO.';
    $featuredFive = $favoriteMenuItems->take(5);
@endphp

<section
    id="signature-reel"
    data-nav-theme="light"
    data-module="signature-reel"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        {{-- Header Tengah Editorial --}}
        <div class="max-w-2xl mx-auto text-center space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[28px] leading-none text-[var(--mentega)]">
                {{ $eyebrow }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $subheading }}
            </p>
        </div>

        {{-- Strip 5 Kartu Portrait Ala Referensi di Desktop / Gulir Rapi di HP & Tablet --}}
        <div
            data-reel-track
            data-reveal-stagger
            class="grid grid-flow-col auto-cols-[64vw] sm:auto-cols-[36vw] lg:grid-flow-row lg:grid-cols-5 gap-4 sm:gap-5 overflow-x-auto lg:overflow-visible snap-x snap-mandatory scrollbar-none pb-2 cursor-grab lg:cursor-default"
        >
            @foreach ($featuredFive as $item)
                <article data-reveal="fade" data-reel-card class="snap-start flex flex-col">
                    <a href="{{ route('menu.show', $item->slug) }}" wire:navigate class="group block space-y-3.5">
                        <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/5]">
                            <x-umco.menu-picture
                                :item="$item"
                                sizes="(min-width: 1024px) 19vw, (min-width: 640px) 36vw, 64vw"
                                imgClass="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500"
                            />
                        </div>

                        <div class="pt-1 space-y-1.5">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="font-display font-bold text-xl sm:text-[1.35rem] leading-tight text-[var(--tinta)] group-hover:text-[var(--mentega)] transition-colors truncate">
                                    {{ $item->name }}
                                </h3>
                                <span class="text-xs sm:text-sm font-semibold text-[var(--mentega-tua)] tabular-nums shrink-0">
                                    {{ (int) round($item->price / 1000) }}rb
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-[var(--tinta-70)] line-clamp-2 leading-snug">
                                {{ $item->ingredients ?: $item->description }}
                            </p>
                            <span class="inline-block font-hand text-lg text-[var(--mentega)] group-hover:translate-x-1 transition-transform">
                                lihat info & pesan &rarr;
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div data-reveal="fade" class="text-center">
            <a href="{{ url('/menu') }}" wire:navigate class="btn-umco btn-umco-secondary">
                <span>Jelajahi seluruh menu</span>
                <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
            </a>
        </div>
    </div>
</section>
