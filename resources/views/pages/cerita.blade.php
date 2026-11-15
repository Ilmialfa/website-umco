@php
    $hero = $sections['hero'] ?? null;
    $history = $sections['history'] ?? null;
    $founder = $sections['founder'] ?? null;
    $values = $sections['values'] ?? null;
    $facilitiesSec = $sections['facilities'] ?? null;
    $stallsSec = $sections['stalls'] ?? null;
    $gallerySec = $sections['gallery_preview'] ?? null;
    $collab = $sections['collab'] ?? null;

    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = method_exists(\App\Support\PhoneNumber::class, 'normalizeBusiness')
        ? \App\Support\PhoneNumber::normalizeBusiness($rawWa)
        : preg_replace('/\D+/', '', (string) $rawWa);
    if ($waDigits && str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $collabTemplate = \App\Support\Settings::get('contact.collab_template', 'Halo UMCO, saya ingin kolaborasi atau bikin acara di UMCO.');
    $collabUrl = 'https://wa.me/' . $waDigits . '?text=' . rawurlencode($collabTemplate);

    $resolveImg = function (?string $p, string $fallback): string {
        $src = $p ?: $fallback;
        if (str_starts_with($src, 'http') || str_starts_with($src, '/')) {
            return asset($src);
        }
        return asset('storage/' . ltrim($src, '/'));
    };

    $heroImg = $resolveImg($hero?->media['desktop'] ?? null, '/images/bistro-patio-table.jpg');
    $historyImg = $resolveImg($history?->media['desktop'] ?? null, '/images/hero-banner-full.jpg');
    $founderImg = $resolveImg($founder?->media['desktop'] ?? null, '/images/hero-iced-coffee-splash.jpg');
    $valuesImg = $resolveImg($values?->media['desktop'] ?? null, '/images/drinks-trio-banner.webp');
    $collabImg = $resolveImg($collab?->media['desktop'] ?? null, '/images/bistro-patio-table.jpg');

    $milestones = $history?->data['milestones'] ?? [
        '2024' => 'Gagasan kedai kampus untuk ruang nugas dan diskusi mahasiswa UMRI mulai dirumuskan.',
        '2025' => 'Resmi menyeduh harian di Jalan Tuanku Tambusai Ujung dengan bar kopi manual dan menu hangat.',
        '2026' => 'Menghadirkan sistem reservasi meja online plus pesan menu lebih dulu agar antrean jam padat lebih rapi.',
    ];
    if (! is_array($milestones)) {
        $milestones = [];
    }

    $ownerName = $founder?->data['owner_name'] ?? 'Tim Pendiri UMCO House';
    $ownerRole = $founder?->data['owner_role'] ?? 'Founder & Pengelola Harian';
    $ownerQuote = $founder?->data['owner_quote'] ?? 'Kopi yang baik harusnya menenangkan kepala, bukan bikin dompet ikut tegang.';
@endphp

<x-layouts.public
    page="cerita"
    initial-nav-theme="light"
    title="Cerita Kami — UMCO"
    description="Dari Kampus 2 UMRI Pekanbaru: sejarah singkat UMCO, profil pengelola, tiga nilai kerja, fasilitas ruang, teman jajan, dan peluang kolaborasi komunitas."
>
    {{-- 1. Pembuka --}}
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-14 sm:pb-20">
        <div class="umco-container space-y-10">
            <div class="max-w-3xl space-y-4">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-3xl text-[var(--mentega)]">
                    {{ $hero?->subheading ?: 'Kampus 2 UMRI, Jalan Tuanku Tambusai Ujung.' }}
                </p>
                <h1 data-reveal="rise" class="type-h1 font-display font-semibold">
                    {{ $hero?->heading ?: 'Dari kampus, buat yang lagi butuh tempat.' }}
                </h1>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-2xl">
                    {{ $hero?->body ?: 'Kami membangun UMCO agar mahasiswa, dosen, dan tamu punya meja yang enak buat duduk lama maupun singgah sebentar.' }}
                </p>
            </div>

            <div data-reveal-media class="rounded-2xl overflow-hidden max-h-[62svh] bg-[var(--batu)]">
                <img
                    src="{{ $heroImg }}"
                    alt="Suasana kedai kopi UMCO di Kampus 2 UMRI"
                    loading="eager"
                    decoding="async"
                    class="w-full h-full object-cover"
                >
            </div>
        </div>
    </section>

    {{-- 2. Sejarah Singkat & Tonggak Perjalanan (Editable Section: cerita/history) --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--batu)]/60 text-[var(--tinta)]">
        <div class="umco-container space-y-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <div class="lg:col-span-6 space-y-4">
                    <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] text-[var(--mentega)]">
                        {{ $history?->subheading ?: 'Sejarah Singkat UMCO' }}
                    </p>
                    <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                        {{ $history?->heading ?: 'Berawal dari meja kecil yang selalu penuh oleh obrolan kuliah' }}
                    </h2>
                    <p data-reveal="fade" class="type-body text-[var(--tinta-70)] leading-relaxed">
                        {{ $history?->body ?: 'UMCO lahir dari kebutuhan sederhana di sekitar Kampus 2 UMRI: mencari kedai kopi dengan harga masuk akal di kantong mahasiswa, seduhan yang konsisten setiap cangkir, serta colokan dan Wi-Fi yang selalu siap menemani tugas akhir maupun diskusi kelompok.' }}
                    </p>
                </div>

                <div data-reveal-media class="lg:col-span-6 rounded-2xl overflow-hidden aspect-[16/11] bg-[var(--kertas-terang)]">
                    <img
                        src="{{ $historyImg }}"
                        alt="Sejarah ruang seduh UMCO Kampus 2 UMRI"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            @if (! empty($milestones))
                <div data-reveal-stagger class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($milestones as $year => $desc)
                        <div class="rounded-2xl bg-[var(--kertas-terang)] p-6 space-y-2">
                            <p class="font-display font-bold text-3xl text-[var(--mentega)] tabular-nums">{{ $year }}</p>
                            <p class="text-sm sm:text-[15px] text-[var(--tinta-70)] leading-relaxed">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 3. Profil Owner & Pengelola (Editable Section: cerita/founder) --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)]">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div data-reveal-media class="lg:col-span-5">
                    <div class="rounded-2xl overflow-hidden aspect-[4/5] bg-[var(--batu)]">
                        <img
                            src="{{ $founderImg }}"
                            alt="{{ $ownerName }}"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-5">
                    <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] text-[var(--mentega)]">
                        {{ $founder?->subheading ?: 'Di Balik UMCO' }}
                    </p>
                    <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                        {{ $founder?->heading ?: 'Dibangun bersama orang-orang yang juga melewati bangku kuliah' }}
                    </h2>
                    <p data-reveal="fade" class="type-body text-[var(--tinta-70)] leading-relaxed">
                        {{ $founder?->body ?: 'Kami ingin siapa pun yang masuk lewat pintu UMCO merasa tidak buru-buru disuruh pulang. Entah kamu datang sendiri membawa laptop selama empat jam atau rapat himpunan dengan lima teman, mejamu punya tempat yang jelas.' }}
                    </p>

                    <blockquote class="rounded-2xl bg-[var(--kertas-terang)] p-6 sm:p-7 space-y-3">
                        <p class="font-hand text-2xl sm:text-[1.85rem] leading-snug text-[var(--mentega-tua)]">
                            “{{ $ownerQuote }}”
                        </p>
                        <footer class="text-sm">
                            <span class="font-display font-bold text-base text-[var(--tinta)]">{{ $ownerName }}</span>
                            <span class="text-[var(--tinta-70)]"> · {{ $ownerRole }}</span>
                        </footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Tiga hal yang kami pegang + Foto pendamping (Editable via StoryBlocks & Section values) --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--batu)] text-[var(--tinta)]">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-5">
                    <p class="font-hand text-2xl text-[var(--mentega)]">
                        {{ $values?->subheading ?: 'Nilai Kerja Harian' }}
                    </p>
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        {{ $values?->heading ?: 'Tiga hal yang kami pegang' }}
                    </h2>
                    <div data-reveal-media class="rounded-2xl overflow-hidden aspect-[4/3] bg-[var(--kertas)]">
                        <img
                            src="{{ $valuesImg }}"
                            alt="Tiga sajian minuman unggulan UMCO"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-4" data-reveal-stagger>
                    @foreach ($storyBlocks as $idx => $block)
                        @php $numLabel = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT); @endphp
                        <article data-reveal="rise" class="rounded-2xl bg-[var(--kertas-terang)] p-6 sm:p-8 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-6">
                            <span
                                aria-hidden="true"
                                class="font-display font-bold text-4xl sm:text-5xl leading-[0.88] text-[var(--mentega)]/35 tabular-nums select-none shrink-0"
                            >{{ $numLabel }}</span>
                            <div class="space-y-2.5 flex-1 min-w-0">
                                <h3 class="type-h3 font-display font-semibold text-[var(--tinta)]">{{ $block->title }}</h3>
                                <p class="type-body text-[var(--tinta-70)] leading-relaxed">{{ $block->body }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Ruang & Fasilitas (Editable via Filament Facilities Resource & Section facilities) --}}
    @if (($facilities ?? collect())->isNotEmpty() && ($facilitiesSec?->is_active ?? true))
        <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)]">
            <div class="umco-container space-y-8">
                <div class="max-w-2xl space-y-2.5">
                    <p data-reveal="fade" class="font-hand text-2xl text-[var(--mentega)]">
                        {{ $facilitiesSec?->subheading ?: 'Ruang & Fasilitas' }}
                    </p>
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        {{ $facilitiesSec?->heading ?: 'Dirancang nyaman buat kerja maupun ngobrol santai' }}
                    </h2>
                    @if ($facilitiesSec?->body)
                        <p class="type-body text-[var(--tinta-70)]">{{ $facilitiesSec->body }}</p>
                    @endif
                </div>

                <div data-reveal-stagger class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @foreach ($facilities as $idx => $fac)
                        @php
                            $lblLower = mb_strtolower((string) $fac->label);
                            $iconType = match (true) {
                                str_contains($lblLower, 'wifi') || str_contains($lblLower, 'wi-fi') || str_contains($lblLower, 'internet') => 'wifi',
                                str_contains($lblLower, 'colok') || str_contains($lblLower, 'listrik') || str_contains($lblLower, 'daya') => 'bolt',
                                str_contains($lblLower, 'meja') || str_contains($lblLower, 'diskusi') || str_contains($lblLower, 'kursi') => 'desk',
                                str_contains($lblLower, 'musik') || str_contains($lblLower, 'audio') || str_contains($lblLower, 'suara') => 'music',
                                str_contains($lblLower, 'teras') || str_contains($lblLower, 'indoor') || str_contains($lblLower, 'outdoor') || str_contains($lblLower, 'ac') => 'space',
                                default => 'compass',
                            };
                            $facNum = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div data-reveal="rise" class="rounded-2xl bg-[var(--kertas-terang)] p-5 sm:p-6 flex items-start justify-between gap-4 transition-colors duration-200 hover:bg-[var(--aksen-soft)]/45">
                            <div class="flex items-start gap-4 min-w-0">
                                <span class="w-11 h-11 rounded-2xl bg-[var(--aksen-soft)]/75 text-[var(--mentega-tua)] flex items-center justify-center shrink-0" aria-hidden="true">
                                    @switch($iconType)
                                        @case('wifi')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><circle cx="12" cy="19.5" r="1" fill="currentColor"/></svg>
                                            @break
                                        @case('bolt')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
                                            @break
                                        @case('desk')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h18M5 10v8M19 10v8M8 10V7h8v3"/></svg>
                                            @break
                                        @case('music')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                                            @break
                                        @case('space')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V5h16v6M4 11h16v7H4v-7Zm4 0v7"/></svg>
                                            @break
                                        @default
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.5 9.5 13 13l-3.5 1.5L11 11l3.5-1.5z"/></svg>
                                    @endswitch
                                </span>
                                <div class="space-y-1 min-w-0">
                                    <span class="block text-[11px] font-semibold tracking-[0.14em] uppercase text-[var(--mentega)]">Fasilitas {{ $facNum }}</span>
                                    <p class="font-display font-semibold text-xl leading-snug text-[var(--tinta)]">{{ $fac->label }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 6. Teman jajan --}}
    @if ($streetStalls->isNotEmpty() && ($stallsSec?->is_active ?? true))
        <section data-nav-theme="light" class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]">
            <div class="umco-container space-y-8">
                <div class="max-w-xl space-y-2">
                    <p class="font-hand text-2xl text-[var(--mentega)]">{{ $stallsSec?->subheading ?: 'Sekitar Halaman Kedai' }}</p>
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">{{ $stallsSec?->heading ?: 'Teman jajan' }}</h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        {{ $stallsSec?->body ?: 'Stan makanan di sekitar halaman UMCO yang bisa dipesan langsung di tempat.' }}
                    </p>
                </div>

                <div data-reveal-stagger class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($streetStalls as $idx => $stall)
                        @php $stallNum = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT); @endphp
                        <article data-reveal="rise" class="rounded-3xl bg-[var(--kertas)] p-6 sm:p-7 flex flex-col justify-between gap-6 transition-transform duration-300 hover:-translate-y-1">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-[11px] font-semibold tracking-[0.14em] uppercase text-[var(--mentega)]">Stan {{ $stallNum }}</span>
                                    @if ($stall->active_hours)
                                        <span class="rounded-full bg-[var(--kertas-terang)] px-3 py-1 text-xs font-medium text-[var(--tinta-70)] tabular-nums">{{ $stall->active_hours }}</span>
                                    @endif
                                </div>
                                <h3 class="font-display font-bold text-2xl sm:text-[1.75rem] leading-tight text-[var(--tinta)]">{{ $stall->name }}</h3>
                                @if ($stall->description)
                                    <p class="type-body text-[var(--tinta-70)] leading-relaxed">{{ $stall->description }}</p>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-[var(--kertas-terang)] px-4 py-3.5 space-y-0.5">
                                <span class="block text-[10px] font-semibold tracking-[0.14em] uppercase text-[var(--tinta-40)]">Menu Jagoan</span>
                                <p class="font-hand text-2xl sm:text-[1.7rem] leading-none text-[var(--mentega-tua)]">{{ $stall->signature_item }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 7. Cuplikan Galeri + Kolaborasi, Acara & Komunitas (Editable Section: cerita/gallery_preview & cerita/collab) --}}
    @if (($galleryItems->isNotEmpty() && ($gallerySec?->is_active ?? true)) || ($collab?->is_active ?? true))
        <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)]">
            <div class="umco-container space-y-14">
                @if ($galleryItems->isNotEmpty() && ($gallerySec?->is_active ?? true))
                    <div class="space-y-6">
                        <div class="flex flex-wrap items-end justify-between gap-4">
                            <div class="space-y-1.5">
                                <p class="font-hand text-2xl text-[var(--mentega)]">{{ $gallerySec?->subheading ?: 'Potret Ruang' }}</p>
                                <h2 class="type-h2 font-display font-semibold">{{ $gallerySec?->heading ?: 'Suasana dari waktu ke waktu' }}</h2>
                            </div>
                            <a href="{{ url($gallerySec?->cta_url ?: '/galeri') }}" wire:navigate class="text-sm font-semibold underline underline-offset-4 hover:text-[var(--mentega)]">
                                {{ $gallerySec?->cta_label ?: 'Buka seluruh galeri' }} &rarr;
                            </a>
                        </div>

                    <div data-reveal-stagger class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
                        @foreach ($galleryItems->take(6) as $g)
                            @php
                                $src = str_starts_with($g->image_path, 'http') || str_starts_with($g->image_path, '/')
                                    ? $g->image_path
                                    : asset('storage/' . ltrim($g->image_path, '/'));
                            @endphp
                            <figure class="space-y-2">
                                <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-[var(--batu)]">
                                    <img src="{{ $src }}" alt="{{ $g->caption ?: 'Potret UMCO' }}" loading="lazy" class="w-full h-full object-cover">
                                </div>
                                @if ($g->caption)
                                    <figcaption class="text-xs sm:text-sm text-[var(--tinta-70)]">{{ $g->caption }}</figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($collab?->is_active ?? true)
                <div class="rounded-2xl bg-[var(--kertas-terang)] p-7 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-3">
                        <p class="font-hand text-2xl text-[var(--mentega)]">
                            {{ $collab?->subheading ?: 'Kolaborasi & Reservasi Rombongan' }}
                        </p>
                        <h2 class="type-h2 font-display font-semibold">
                            {{ $collab?->heading ?: 'Mau bikin acara, bedah buku, atau kumpul komunitas?' }}
                        </h2>
                        <p class="type-body text-[var(--tinta-70)] leading-relaxed">
                            {{ $collab?->body ?: 'Diskusi organisasi mahasiswa, komunitas kreatif, buka puasa bareng jurusan, sampai mini workshop sering mampir ke UMCO. Kabari kami beberapa hari sebelumnya untuk atur tata meja dan paket minuman.' }}
                        </p>
                        <div class="pt-2 flex flex-wrap gap-3">
                            <a
                                href="{{ $collabUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-umco btn-umco-primary"
                            >
                                <span>{{ $collab?->cta_label ?: 'Hubungi tim kolaborasi' }}</span>
                                <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                            </a>
                            <a
                                href="{{ url($collab?->cta_url ?: '/reservasi') }}"
                                wire:navigate
                                class="btn-umco bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)]"
                            >
                                <span>{{ $collab?->data['secondary_label'] ?? 'Pesan meja grup' }}</span>
                            </a>
                        </div>
                    </div>

                    <div data-reveal-media class="lg:col-span-5 rounded-2xl overflow-hidden aspect-[4/3] bg-[var(--batu)]">
                        <img
                            src="{{ $collabImg }}"
                            alt="Kolaborasi acara komunitas di UMCO"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif
</x-layouts.public>
