@php
    $banners = ($activeBanners ?? collect())->toBase()->values();
    $defaults = collect([
        (object) [
            'title' => $section?->heading ?: 'Meja lapang buat diskusi dan tugas sore',
            'description' => $section?->subheading ?: 'Meja panjang bercatu daya, seduhan segar dari pagi, dan menu hangat yang bisa dipesan sekaligus saat reservasi meja.',
            'image_path' => $section?->media['banner'] ?? '/images/bistro-patio-table.webp',
            'cta_label' => $section?->cta_label ?: 'Lihat selengkapnya',
            'cta_url' => $section?->cta_url ?: '/cerita',
        ],
        (object) [
            'title' => 'Pesan meja sekalian menu tanpa antre',
            'description' => 'Pilih jadwal kedatangan minimal 2 orang, tentukan permintaan tempat duduk, lalu kunci karcis meja lewat QRIS.',
            'image_path' => '/images/hero-banner-full.webp',
            'cta_label' => 'Lihat selengkapnya',
            'cta_url' => '/reservasi',
        ],
        (object) [
            'title' => 'Racikan kopi susu & penyegar non-kopi',
            'description' => 'Dari Aren Coffee klasik, Matcha dingin, hingga sajian camilan hangat untuk teman kelompok.',
            'image_path' => '/images/drinks-trio-banner.webp',
            'cta_label' => 'Lihat selengkapnya',
            'cta_url' => '/menu',
        ],
    ]);

    if ($banners->isEmpty()) {
        $banners = $defaults;
    } elseif ($banners->count() < 3) {
        $existingTitles = $banners->pluck('title')->all();
        foreach ($defaults as $def) {
            if (! in_array($def->title, $existingTitles, true)) {
                $banners->push($def);
            }
            if ($banners->count() >= 3) break;
        }
    }

    $bannerSlides = $banners->map(function ($b) {
        $img = $b->image_path ?: '/images/bistro-patio-table.webp';
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
    })->values()->all();

    $sectionEyebrow = $section?->data['eyebrow'] ?? 'Sorotan UMCO';
    $sectionHeading = $section?->heading ?: 'Kabar terbaru dari UMCO';
@endphp

<section
    id="banner-utama"
    data-nav-theme="light"
    x-data="{
        active: 0,
        slides: @js($bannerSlides),
        glideClass: '',
        touchX: null,
        prevIdx() { return (this.active - 1 + this.slides.length) % this.slides.length; },
        nextIdx() { return (this.active + 1) % this.slides.length; },
        triggerGlide(direction) {
            this.glideClass = '';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    this.glideClass = direction === 'prev' ? 'umco-banner-glide-prev' : 'umco-banner-glide-next';
                });
            });
        },
        prev() {
            this.active = this.prevIdx();
            this.triggerGlide('prev');
        },
        next() {
            this.active = this.nextIdx();
            this.triggerGlide('next');
        },
        go(idx) {
            if (idx === this.active) return;
            const direction = idx > this.active ? 'next' : 'prev';
            this.active = idx;
            this.triggerGlide(direction);
        },
        onTouchStart(e) {
            this.touchX = e.touches[0]?.clientX ?? null;
        },
        onTouchEnd(e) {
            if (this.touchX === null) return;
            const diff = (e.changedTouches[0]?.clientX ?? this.touchX) - this.touchX;
            this.touchX = null;
            if (Math.abs(diff) > 42) {
                diff < 0 ? this.next() : this.prev();
            }
        }
    }"
    class="bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    {{-- Showcase Banner 3-Kartu Lanskap dengan Animasi Geser & Dukungan Swipe --}}
    <div class="umco-section pt-10 sm:pt-14 pb-10 sm:pb-12">
        <div class="umco-container space-y-8 sm:space-y-10">
            <div class="max-w-4xl mx-auto text-center space-y-2">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[28px] leading-none text-[var(--mentega)]">
                    {{ $sectionEyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-display-l font-display font-bold text-[var(--tinta)]">
                    {{ $sectionHeading }}
                </h2>
            </div>

            <div
                data-reveal-media
                class="relative"
                @touchstart.passive="onTouchStart($event)"
                @touchend.passive="onTouchEnd($event)"
            >
                {{-- Desktop & Tablet: Panggung 3 Banner dengan Transisi Geser --}}
                <div :class="glideClass" class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 items-start">
                    {{-- Slot Kiri --}}
                    <article
                        @click="prev()"
                        class="hidden md:block md:col-span-3 space-y-3.5 opacity-60 hover:opacity-90 transition-all duration-300 cursor-pointer"
                    >
                        <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[16/10]">
                            <img :src="slides[prevIdx()].image" :alt="slides[prevIdx()].title" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5 px-1">
                            <h3 class="font-display font-bold text-xl leading-tight line-clamp-1" x-text="slides[prevIdx()].title"></h3>
                            <p class="text-xs text-[var(--tinta-70)] line-clamp-2 leading-relaxed" x-text="slides[prevIdx()].description"></p>
                            <a
                                :href="slides[prevIdx()].cta_url"
                                @click.stop
                                :target="slides[prevIdx()].external ? '_blank' : null"
                                :rel="slides[prevIdx()].external ? 'noopener noreferrer' : null"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--mentega)] pt-0.5"
                            >
                                <span x-text="slides[prevIdx()].cta_label"></span>
                                <svg class="w-3.5 h-3.5"><use href="#icon-arrow-right"/></svg>
                            </a>
                        </div>
                    </article>

                    {{-- Slot Tengah (Sorotan Utama) --}}
                    <article class="col-span-1 md:col-span-6 space-y-4">
                        <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[16/10]">
                            <img
                                :src="slides[active].image"
                                :alt="slides[active].title"
                                class="w-full h-full object-cover transition-transform duration-500"
                            >
                        </div>
                        <div class="space-y-2 text-center md:text-left px-1">
                            <h3 class="font-display font-bold text-2xl sm:text-[1.95rem] leading-tight text-[var(--tinta)]" x-text="slides[active].title">
                                {{ $bannerSlides[0]['title'] }}
                            </h3>
                            <p class="text-sm sm:text-[15px] text-[var(--tinta-70)] leading-relaxed max-w-xl mx-auto md:mx-0" x-text="slides[active].description">
                                {{ $bannerSlides[0]['description'] }}
                            </p>
                            <div class="pt-1">
                                <a
                                    :href="slides[active].cta_url"
                                    :target="slides[active].external ? '_blank' : null"
                                    :rel="slides[active].external ? 'noopener noreferrer' : null"
                                    class="inline-flex items-center gap-2 rounded-full bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)] px-5 h-10 text-sm font-semibold transition-colors"
                                >
                                    <span x-text="slides[active].cta_label">{{ $bannerSlides[0]['cta_label'] }}</span>
                                    <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>

                    {{-- Slot Kanan --}}
                    <article
                        @click="next()"
                        class="hidden md:block md:col-span-3 space-y-3.5 opacity-60 hover:opacity-90 transition-all duration-300 cursor-pointer"
                    >
                        <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[16/10]">
                            <img :src="slides[nextIdx()].image" :alt="slides[nextIdx()].title" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5 px-1">
                            <h3 class="font-display font-bold text-xl leading-tight line-clamp-1" x-text="slides[nextIdx()].title"></h3>
                            <p class="text-xs text-[var(--tinta-70)] line-clamp-2 leading-relaxed" x-text="slides[nextIdx()].description"></p>
                            <a
                                :href="slides[nextIdx()].cta_url"
                                @click.stop
                                :target="slides[nextIdx()].external ? '_blank' : null"
                                :rel="slides[nextIdx()].external ? 'noopener noreferrer' : null"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--mentega)] pt-0.5"
                            >
                                <span x-text="slides[nextIdx()].cta_label"></span>
                                <svg class="w-3.5 h-3.5"><use href="#icon-arrow-right"/></svg>
                            </a>
                        </div>
                    </article>
                </div>

                {{-- Indikator & Tombol Navigasi (di bawah panggung agar tidak menutupi teks kartu) --}}
                <div class="mt-7 flex items-center justify-center gap-4">
                    <button
                        type="button"
                        @click="prev()"
                        aria-label="Banner sebelumnya"
                        class="w-10 h-10 rounded-full bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)] inline-flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <template x-for="(_, i) in slides" :key="'b-dot-' + i">
                            <button
                                type="button"
                                @click="go(i)"
                                :aria-label="'Pilih banner ' + (i + 1)"
                                class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                :class="active === i ? 'w-7 bg-[var(--tinta)]' : 'w-2 bg-[var(--tinta)]/25 hover:bg-[var(--tinta)]/50'"
                            ></button>
                        </template>
                    </div>
                    <button
                        type="button"
                        @click="next()"
                        aria-label="Banner berikutnya"
                        class="w-10 h-10 rounded-full bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)] inline-flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
