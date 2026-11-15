@php
    $banners = ($activeBanners ?? collect())->values();
    $fallbackBanner = (object) [
        'title' => $section?->heading ?: 'Meja lapang buat diskusi dan tugas sore',
        'description' => $section?->subheading ?: 'Ruang nyaman di kawasan kampus buat nugas sendirian, kerja kelompok, atau diskusi komunitas.',
        'image_path' => $section?->media['banner'] ?? '/images/bistro-patio-table.jpg',
        'cta_label' => $section?->cta_label ?: 'Lihat selengkapnya',
        'cta_url' => $section?->cta_url ?: '/cerita',
    ];
    if ($banners->isEmpty()) {
        $banners = collect([$fallbackBanner]);
    }

    $bannerSlides = $banners->map(function ($b) {
        $img = $b->image_path ?: '/images/bistro-patio-table.jpg';
        if (! str_starts_with($img, 'http') && ! str_starts_with($img, '/')) {
            $img = '/storage/' . ltrim($img, '/');
        }
        return [
            'title' => $b->title,
            'description' => $b->description,
            'image' => asset($img),
            'cta_label' => $b->cta_label ?: 'Lihat selengkapnya',
            'cta_url' => $b->cta_url ? url($b->cta_url) : url('/cerita'),
            'external' => $b->cta_url && str_starts_with($b->cta_url, 'http'),
        ];
    })->all();

    $ribbonItems = [
        'Wi-Fi kencang di seluruh meja',
        'Colokan listrik mudah dijangkau',
        'Pesan meja sekaligus menu online',
        'Kopi single-origin & minuman non-kopi',
        'Cocok buat diskusi & kerja kelompok',
    ];
@endphp

<section
    id="banner-utama"
    data-nav-theme="dark"
    x-data="{ active: 0, slides: @js($bannerSlides) }"
    class="relative bg-[var(--umco-hutan)] text-white overflow-hidden"
>
    <div class="relative min-h-[460px] sm:min-h-[540px] lg:min-h-[620px] flex items-center">
        <template x-for="(slide, idx) in slides" :key="idx">
            <div x-show="active === idx" class="absolute inset-0">
                <img
                    :src="slide.image"
                    :alt="slide.title"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-cover object-center"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#0E2B1E]/90 via-[#0E2B1E]/55 to-[#0E2B1E]/15"></div>
            </div>
        </template>

        <div class="relative z-10 umco-container py-16 sm:py-20">
            <div class="max-w-xl space-y-5">
                <div class="flex items-center gap-3 text-xs font-semibold tracking-[0.08em] text-[var(--aksen-soft)]">
                    <span>Sorotan UMCO</span>
                    <template x-if="slides.length > 1">
                        <div class="inline-flex items-center gap-1.5 ml-2">
                            <template x-for="(_, i) in slides" :key="'dot-' + i">
                                <button
                                    type="button"
                                    @click="active = i"
                                    :aria-label="'Tampilkan banner ' + (i + 1)"
                                    class="h-2 rounded-full transition-all cursor-pointer"
                                    :class="active === i ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"
                                ></button>
                            </template>
                        </div>
                    </template>
                </div>

                <h2
                    data-reveal="rise"
                    class="type-display font-display font-bold text-white"
                    x-text="slides[active].title"
                >
                    {{ $bannerSlides[0]['title'] }}
                </h2>

                <p
                    class="type-body sm:type-body-l text-white/90 max-w-md leading-relaxed"
                    x-text="slides[active].description"
                >
                    {{ $bannerSlides[0]['description'] }}
                </p>

                <div class="pt-1">
                    <a
                        :href="slides[active].cta_url"
                        :target="slides[active].external ? '_blank' : null"
                        :rel="slides[active].external ? 'noopener noreferrer' : null"
                        class="btn-umco bg-[#F6F4ED] text-[#141916] border border-[#F6F4ED] hover:bg-white"
                    >
                        <span x-text="slides[active].cta_label">{{ $bannerSlides[0]['cta_label'] }}</span>
                        <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Strip fitur bawah menyatu elegan dengan tepi banner --}}
    <div class="relative z-10 bg-[#13422E] border-t border-white/15 py-3 overflow-hidden select-none">
        <div class="umco-marquee-track text-xs sm:text-sm font-medium text-white/90">
            @for ($c = 0; $c < 2; $c++)
                <div class="flex items-center gap-8 pr-8" @if ($c === 1) aria-hidden="true" @endif>
                    @foreach ($ribbonItems as $ri)
                        <span class="inline-flex items-center gap-2.5 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--aksen-soft)]"></span>
                            <span>{{ $ri }}</span>
                        </span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</section>
