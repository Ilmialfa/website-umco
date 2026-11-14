@php
    $googleRating = str_replace('.', ',', \App\Support\Settings::get('rating.google_rating', '4.9'));
    $reviewsCount = \App\Support\Settings::get('rating.google_reviews_count', '86');
@endphp

<section
    id="kata-mereka"
    data-nav-theme="light"
    data-module="testimonials-slider"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] border-t border-[var(--garis)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            {{-- Angka Google rating di samping --}}
            <div class="lg:col-span-4 space-y-2">
                <p class="font-display font-extrabold text-[4.5rem] sm:text-[5.5rem] leading-none tracking-[-0.04em] tabular-nums">
                    {{ $googleRating }}
                </p>
                <p class="text-sm text-[var(--tinta-70)]">
                    dari {{ $reviewsCount }} ulasan di Google Maps
                </p>
                <div class="pt-4 flex items-center gap-5 text-sm font-semibold">
                    <button type="button" data-quote-prev class="inline-flex items-center gap-1.5 underline underline-offset-4 cursor-pointer">
                        <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                        <span>Sebelumnya</span>
                    </button>
                    <span data-quote-index class="font-led text-xs text-[var(--tinta-70)]">01 / {{ str_pad((string) max(1, $testimonials->count()), 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" data-quote-next class="inline-flex items-center gap-1.5 underline underline-offset-4 cursor-pointer">
                        <span>Lanjut</span>
                        <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                    </button>
                </div>
            </div>

            {{-- Satu kutipan besar bergantian --}}
            <div class="lg:col-span-8">
                @forelse ($testimonials as $idx => $t)
                    <blockquote
                        data-quote-slide
                        class="{{ $idx === 0 ? '' : 'hidden' }} space-y-5"
                        aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}"
                    >
                        <p class="font-display font-bold text-[1.375rem] sm:text-[2rem] lg:text-[2.35rem] leading-[1.2] tracking-[-0.02em]">
                            “{{ $t->body }}”
                        </p>
                        <footer class="text-sm text-[var(--tinta-70)]">
                            <span class="font-semibold text-[var(--tinta)]">{{ $t->name }}</span>
                            @if ($t->role)
                                · {{ $t->role }}
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
