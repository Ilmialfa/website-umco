@php
    $hero = $sections['hero'] ?? null;
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
    $heroImg = $hero?->media['desktop'] ?? '/images/bistro-patio-table.jpg';
@endphp

<x-layouts.public
    page="cerita"
    initial-nav-theme="light"
    title="Cerita Kami — UMCO"
    description="Dari Kampus 2 UMRI Pekanbaru: cerita ruang seduh, tiga prinsip kami, teman jajan sore, dan info kolaborasi."
>
    {{-- 1. Pembuka --}}
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-14 sm:pb-20">
        <div class="umco-container space-y-10">
            <div class="max-w-3xl space-y-4">
                <p class="font-hand text-2xl sm:text-3xl text-[var(--mentega-tua)]">
                    {{ $hero?->subheading ?: 'Kampus 2 UMRI, Jalan Tuanku Tambusai Ujung.' }}
                </p>
                <h1 data-reveal="rise" class="type-h1 font-display font-semibold">
                    {{ $hero?->heading ?: 'Dari kampus, buat yang lagi butuh tempat.' }}
                </h1>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-2xl">
                    {{ $hero?->body ?: 'Kami membangun UMCO agar mahasiswa, dosen, dan tamu punya meja yang enak buat duduk lama maupun singgah sebentar.' }}
                </p>
            </div>

            <div class="rounded-2xl overflow-hidden max-h-[62svh] bg-[var(--batu)]">
                <img
                    src="{{ asset($heroImg) }}"
                    alt="Suasana kedai kopi UMCO di Kampus 2 UMRI"
                    loading="eager"
                    decoding="async"
                    class="w-full h-full object-cover"
                >
            </div>
        </div>
    </section>

    {{-- 2. Tiga hal yang kami pegang + Foto pendamping --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--batu)] text-[var(--tinta)]">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-6">
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        Tiga hal yang kami pegang
                    </h2>
                    <div class="rounded-2xl overflow-hidden aspect-[4/3] bg-[var(--kertas)]">
                        <img
                            src="{{ asset('/images/drinks-trio-banner.webp') }}"
                            alt="Tiga sajian minuman unggulan UMCO"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <div class="lg:col-span-7 divide-y divide-[var(--garis)]" data-reveal-stagger>
                    @foreach ($storyBlocks as $block)
                        <article class="py-7 sm:py-8 space-y-2.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[var(--mentega)] shrink-0"></span>
                                <h3 class="type-h3 font-display font-semibold">{{ $block->title }}</h3>
                            </div>
                            <p class="type-body text-[var(--tinta-70)] leading-relaxed pl-4.5">{{ $block->body }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Teman jajan --}}
    @if ($streetStalls->isNotEmpty())
        <section data-nav-theme="light" class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]">
            <div class="umco-container space-y-8">
                <div class="max-w-xl space-y-2">
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">Teman jajan</h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        Stan makanan di sekitar halaman UMCO yang bisa dipesan langsung di tempat.
                    </p>
                </div>

                <div class="rounded-2xl border border-[var(--garis)] bg-[var(--kertas)] overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm sm:text-base">
                        <thead>
                            <tr class="border-b border-[var(--garis)]">
                                <th class="py-3.5 px-5 font-semibold">Nama stan</th>
                                <th class="py-3.5 px-5 font-semibold">Menu jagoan</th>
                                <th class="py-3.5 px-5 font-semibold text-right whitespace-nowrap">Jam buka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--garis)]">
                            @foreach ($streetStalls as $stall)
                                <tr>
                                    <td class="py-4 px-5">
                                        <p class="font-display font-semibold text-base sm:text-lg">{{ $stall->name }}</p>
                                        @if ($stall->description)
                                            <p class="text-xs sm:text-sm text-[var(--tinta-70)] mt-0.5">{{ $stall->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 font-hand text-2xl leading-none text-[var(--mentega-tua)]">{{ $stall->signature_item }}</td>
                                    <td class="py-4 px-5 text-right tabular-nums whitespace-nowrap text-sm text-[var(--tinta-70)]">{{ $stall->active_hours }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Galeri tambahan + ajakan kolaborasi --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)]">
        <div class="umco-container space-y-12">
            @if ($galleryItems->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
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
            @endif

            <div class="rounded-2xl bg-[var(--kertas-terang)] border-t-[3px] border-t-[var(--mentega)] p-7 sm:p-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <h2 class="type-h2 font-display font-semibold">
                        {{ $collab?->heading ?: 'Mau bikin acara atau kumpul komunitas?' }}
                    </h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        {{ $collab?->body ?: 'Diskusi buku, buka puasa bareng jurusan, sampai sesi ngobrol santai sering mampir ke sini.' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ $collabUrl }}" target="_blank" rel="noopener noreferrer" class="btn-umco btn-umco-secondary">
                        {{ $collab?->cta_label ?: 'Tanya lewat WhatsApp' }}
                    </a>
                    <a href="{{ url('/reservasi') }}" wire:navigate class="btn-umco btn-umco-primary">
                        Pesan meja langsung
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
