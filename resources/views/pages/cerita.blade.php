@php
    $hero = $sections['hero'] ?? null;
    $altimeter = $sections['altimeter'] ?? null;
    $collab = $sections['collab'] ?? null;

    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = preg_replace('/\D+/', '', (string) $rawWa);
    if (str_starts_with($waDigits, '0')) {
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
    description="Dari Kampus 2 UMRI Pekanbaru: asal biji kopi Nusantara, tiga hal yang kami pegang, teman jajan sore, dan info kolaborasi."
>
    {{-- 1. Pembuka --}}
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-14 sm:pb-20">
        <div class="umco-container space-y-10">
            <div class="max-w-3xl space-y-4">
                <p class="font-hand text-2xl sm:text-3xl text-[var(--cabai)]">
                    {{ $hero?->subheading ?: 'Kampus 2 UMRI, Jalan Tuanku Tambusai Ujung.' }}
                </p>
                <h1 data-reveal="rise" class="type-h1 font-display font-extrabold">
                    {{ $hero?->heading ?: 'Dari kampus, buat yang lagi butuh tempat.' }}
                </h1>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-2xl">
                    {{ $hero?->body ?: 'Kami membangun UMCO agar mahasiswa, dosen, dan tamu punya meja yang enak buat duduk lama maupun singgah sebentar.' }}
                </p>
            </div>

            <div class="overflow-hidden max-h-[62svh]">
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

    {{-- 2. Asal biji + altimeter gulir --}}
    <section
        id="biji"
        data-nav-theme="light"
        data-module="altimeter"
        class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)]"
    >
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-3">
                    <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                        {{ $altimeter?->heading ?: 'Dari mana bijinya datang' }}
                    </h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        {{ $altimeter?->subheading ?: 'Gulir pelan untuk melihat ketinggian kebun biji yang sering masuk ke grinder bar.' }}
                    </p>
                    <div class="pt-4 flex items-baseline gap-2">
                        <span data-altimeter-num class="font-led text-5xl sm:text-7xl tracking-tight text-[var(--cabai)] tabular-nums">1.650</span>
                        <span class="font-sans text-sm font-semibold text-[var(--tinta-70)]">mdpl</span>
                    </div>
                </div>

                <div class="lg:col-span-7 divide-y divide-[var(--garis)]">
                    @foreach ($beanOrigins as $origin)
                        <div
                            data-origin-altitude="{{ $origin->altitude }}"
                            class="py-6 flex flex-col sm:flex-row sm:items-baseline justify-between gap-3 transition-opacity duration-300"
                        >
                            <div class="space-y-1 max-w-lg">
                                <h3 class="font-display font-bold text-2xl sm:text-3xl">{{ $origin->name }}</h3>
                                <p class="text-sm font-semibold text-[var(--tinta-70)]">{{ $origin->region }}</p>
                                @if ($origin->note)
                                    <p class="text-sm sm:text-base text-[var(--tinta-70)] pt-1">{{ $origin->note }}</p>
                                @endif
                            </div>
                            <span class="font-led text-2xl sm:text-3xl text-[var(--tinta)] tabular-nums shrink-0">
                                {{ number_format($origin->altitude, 0, ',', '.') }} mdpl
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Tiga hal yang kami pegang (bukan kartu) --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--mentega)] text-[var(--tinta)] edge-perforated-top">
        <div class="umco-container max-w-4xl space-y-10">
            <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                Tiga hal yang kami pegang
            </h2>

            <div class="divide-y divide-[var(--tinta)]/20" data-reveal-stagger>
                @foreach ($storyBlocks as $idx => $block)
                    <article class="py-7 sm:py-9 flex flex-col sm:flex-row gap-4 sm:gap-10">
                        <span class="font-display font-extrabold text-4xl sm:text-5xl text-[var(--tinta)]/35 tabular-nums shrink-0">
                            {{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="space-y-2">
                            <h3 class="type-h3 font-display font-bold">{{ $block->title }}</h3>
                            <p class="type-body text-[var(--tinta)]/85 leading-relaxed">{{ $block->body }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. Teman jajan gaya tabel nota --}}
    @if ($streetStalls->isNotEmpty())
        <section data-nav-theme="light" class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)]">
            <div class="umco-container space-y-8">
                <div class="max-w-xl space-y-2">
                    <h2 data-reveal="rise" class="type-h2 font-display font-bold">Teman jajan</h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        Stan makanan di sekitar halaman UMCO yang bisa dipesan langsung di tempat.
                    </p>
                </div>

                <div class="border border-[var(--tinta)] bg-[var(--kertas)] overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm sm:text-base">
                        <thead>
                            <tr class="border-b border-[var(--tinta)]">
                                <th class="py-3.5 px-4 font-semibold">Nama stan</th>
                                <th class="py-3.5 px-4 font-semibold">Menu jagoan</th>
                                <th class="py-3.5 px-4 font-semibold text-right whitespace-nowrap">Jam buka</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--garis)]">
                            @foreach ($streetStalls as $stall)
                                <tr>
                                    <td class="py-4 px-4">
                                        <p class="font-display font-bold text-lg">{{ $stall->name }}</p>
                                        @if ($stall->description)
                                            <p class="text-xs sm:text-sm text-[var(--tinta-70)] mt-0.5">{{ $stall->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-hand text-2xl leading-none">{{ $stall->signature_item }}</td>
                                    <td class="py-4 px-4 text-right tabular-nums whitespace-nowrap text-sm text-[var(--tinta-70)]">{{ $stall->active_hours }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    {{-- 5. Galeri tambahan + ajakan kolaborasi --}}
    <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)] border-t border-[var(--garis)]">
        <div class="umco-container space-y-12">
            @if ($galleryItems->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach ($galleryItems->take(6) as $g)
                        @php
                            $src = str_starts_with($g->image_path, 'http') || str_starts_with($g->image_path, '/')
                                ? $g->image_path
                                : asset('storage/' . ltrim($g->image_path, '/'));
                        @endphp
                        <figure class="space-y-1.5">
                            <div class="aspect-[4/3] overflow-hidden bg-[var(--garis)]/30">
                                <img src="{{ $src }}" alt="{{ $g->caption ?: 'Potret UMCO' }}" loading="lazy" class="w-full h-full object-cover">
                            </div>
                            @if ($g->caption)
                                <figcaption class="font-hand text-xl text-[var(--tinta-70)]">{{ $g->caption }}</figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif

            <div class="bg-[var(--blush)] karcis-serrated p-7 sm:p-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <h2 class="type-h2 font-display font-bold">
                        {{ $collab?->heading ?: 'Mau bikin acara atau kumpul komunitas?' }}
                    </h2>
                    <p class="type-body text-[var(--tinta)]/85">
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
