@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Kata Pengunjung';
    $heading = $section?->heading ?: 'Kenapa banyak yang balik lagi';
    $subheading = $section?->subheading ?: 'Suara nyata mahasiswa, pekerja lepas, dan komunitas yang sering singgah.';
    $googleRating = str_replace('.', ',', \App\Support\Settings::get('rating.google_rating', '4.9'));
    $reviewsCount = \App\Support\Settings::get('rating.google_reviews_count', '86');

    $list = ($testimonials ?? collect())->values();
    if ($list->isEmpty()) {
        $list = collect([
            (object) ['name' => 'Rani', 'role' => 'Mahasiswi UMRI', 'rating' => 5, 'body' => 'Aren Coffee-nya konsisten dan Nasi Ayam Sambal Matah-nya bikin betah makan siang di kampus.'],
            (object) ['name' => 'Fajar Ramadhan', 'role' => 'Mahasiswa Teknik', 'rating' => 5, 'body' => 'Meja kerjanya luas dan colokannya gampang dijangkau buat diskusi sore.'],
            (object) ['name' => 'Sarah Nabilah', 'role' => 'Pekerja lepas', 'rating' => 5, 'body' => 'Praktis banget bisa pesan meja sekalian menu langsung dari website sebelum datang.'],
        ]);
    }

    // Distribusikan rata ke 3 kolom paralaks berlawanan (kiri naik, tengah turun, kanan naik)
    $columns = [[], [], []];
    $minNeeded = max(6, $list->count());
    $expanded = collect();
    while ($expanded->count() < $minNeeded) {
        $expanded = $expanded->merge($list);
    }
    foreach ($expanded->take(9)->values() as $idx => $t) {
        $columns[$idx % 3][] = $t;
    }
@endphp

<section
    id="kata-mereka"
    data-nav-theme="light"
    data-module="testimonials-parallax"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        <div class="max-w-2xl mx-auto text-center space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $subheading }}
            </p>
            <div data-reveal="fade" class="pt-1 inline-flex items-center gap-2 rounded-full bg-[var(--kertas-terang)] px-4 py-1.5 text-xs sm:text-sm">
                <span class="font-display font-bold text-base text-[var(--mentega)] tabular-nums">{{ $googleRating }}</span>
                <span class="text-[var(--tinta-70)]">dari {{ $reviewsCount }} ulasan di Google Maps</span>
            </div>
        </div>

        {{-- Tablet & Desktop (>=640px): 3 Kolom Paralaks Infinite (Kiri Atas, Tengah Bawah, Kanan Atas) --}}
        <div data-reveal-media class="relative hidden sm:grid grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6 h-[470px] lg:h-[520px] overflow-hidden">
            {{-- Mask gradasi atas/bawah agar kartu keluar-masuk halus --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 h-16 z-10 bg-gradient-to-b from-[var(--kertas)] to-transparent"></div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 z-10 bg-gradient-to-t from-[var(--kertas)] to-transparent"></div>

            @foreach ($columns as $cIdx => $colItems)
                @php
                    $loopClass = $cIdx === 1 ? 'umco-col-loop-down' : 'umco-col-loop-up';
                    $colVisibility = $cIdx === 2 ? 'hidden lg:block' : '';
                @endphp
                <div data-parallax-col class="{{ $colVisibility }} overflow-hidden">
                    <div class="{{ $loopClass }}">
                        @for ($dup = 0; $dup < 2; $dup++)
                            <div class="space-y-5 pb-5" @if ($dup === 1) aria-hidden="true" @endif>
                                @foreach ($colItems as $t)
                                    <article class="rounded-2xl bg-[var(--kertas-terang)] p-6 space-y-4">
                                        <div class="flex items-center gap-1 text-[var(--mentega)]" aria-label="{{ $t->rating ?? 5 }} bintang">
                                            @for ($s = 0; $s < (int) ($t->rating ?? 5); $s++)
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.2 1 5.9L10 15l-5.2 2.8 1-5.9-4.3-4.2 5.9-.9L10 1.5z"/></svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm sm:text-[15px] leading-relaxed text-[var(--tinta)]">
                                            “{{ $t->body }}”
                                        </p>
                                        <footer class="pt-2 flex items-center justify-between gap-2 text-xs text-[var(--tinta-70)]">
                                            <span class="font-semibold text-[var(--tinta)]">{{ $t->name }}</span>
                                            @if ($t->role)
                                                <span class="truncate">{{ $t->role }}</span>
                                            @endif
                                        </footer>
                                    </article>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        {{-- HP (<640px): Marquee Kartu Horizontal Ringkas agar tidak memakan tinggi layar --}}
        <div data-reveal-media class="sm:hidden overflow-hidden -mx-5 px-5">
            <div class="umco-marquee-track gap-4" style="animation-duration: 34s;">
                @for ($dup = 0; $dup < 2; $dup++)
                    <div class="flex gap-4 pr-4" @if ($dup === 1) aria-hidden="true" @endif>
                        @foreach ($list->take(6) as $t)
                            <article class="w-[78vw] max-w-[300px] shrink-0 rounded-2xl bg-[var(--kertas-terang)] p-5 space-y-3">
                                <div class="flex items-center gap-1 text-[var(--mentega)]">
                                    @for ($s = 0; $s < (int) ($t->rating ?? 5); $s++)
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.2 1 5.9L10 15l-5.2 2.8 1-5.9-4.3-4.2 5.9-.9L10 1.5z"/></svg>
                                    @endfor
                                </div>
                                <p class="text-sm leading-relaxed text-[var(--tinta)]">
                                    “{{ $t->body }}”
                                </p>
                                <p class="pt-1 text-xs">
                                    <span class="font-semibold text-[var(--tinta)]">{{ $t->name }}</span>
                                    @if ($t->role)
                                        <span class="text-[var(--tinta-70)]"> · {{ $t->role }}</span>
                                    @endif
                                </p>
                            </article>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>
