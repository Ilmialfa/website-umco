@php
    $googleRating = str_replace('.', ',', \App\Support\Settings::get('rating.google_rating', '4.9'));
    $reviewsCount = \App\Support\Settings::get('rating.google_reviews_count', '86');
@endphp

<section
    id="kata-mereka"
    data-nav-theme="light"
    data-module="testimonials-slider"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            <div class="lg:col-span-4 space-y-5">
                <div class="space-y-1">
                    <div class="flex items-baseline gap-2.5">
                        <p class="font-display font-semibold text-[3.75rem] sm:text-[4.75rem] leading-none tracking-[-0.03em] tabular-nums">
                            {{ $googleRating }}
                        </p>
                        <span class="w-3 h-3 rounded-full bg-[var(--mentega)]"></span>
                    </div>
                    <p class="text-sm text-[var(--tinta-70)]">
                        dari {{ $reviewsCount }} ulasan di Google Maps
                    </p>
                </div>

                <div class="flex items-center gap-4 text-sm font-semibold">
                    <button type="button" data-quote-prev class="inline-flex items-center gap-1.5 underline underline-offset-4 hover:text-[var(--mentega-tua)] cursor-pointer">
                        <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                        <span>Sebelumnya</span>
                    </button>
                    <span data-quote-index class="font-sans text-xs font-medium text-[var(--tinta-70)] tabular-nums">1 / {{ max(1, $testimonials->count()) }}</span>
                    <button type="button" data-quote-next class="inline-flex items-center gap-1.5 underline underline-offset-4 hover:text-[var(--mentega-tua)] cursor-pointer">
                        <span>Lanjut</span>
                        <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                    </button>
                </div>

                <div class="hidden sm:block aspect-[16/9] max-w-xs rounded-2xl overflow-hidden bg-[var(--batu)]">
                    <img
                        src="{{ asset('/images/drinks-trio-banner.webp') }}"
                        alt="Tiga sajian minuman favorit pengunjung UMCO"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            <div class="lg:col-span-8 rounded-2xl bg-[var(--kertas-terang)] p-7 sm:p-10">
                @forelse ($testimonials as $idx => $t)
                    <blockquote
                        data-quote-slide
                        class="{{ $idx === 0 ? '' : 'hidden' }} space-y-6"
                        aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}"
                    >
                        <p class="font-display font-medium text-[1.25rem] sm:text-[1.75rem] lg:text-[2.1rem] leading-[1.3] tracking-[-0.015em]">
                            “{{ $t->body }}”
                        </p>
                        <footer class="text-sm text-[var(--tinta-70)] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[var(--mentega)]"></span>
                            <span class="font-semibold text-[var(--tinta)]">{{ $t->name }}</span>
                            @if ($t->role)
                                <span>· {{ $t->role }}</span>
                            @endif
                        </footer>
                    </blockquote>
                @empty
                    <p class="text-[var(--tinta-70)]">Belum ada ulasan yang ditampilkan.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
