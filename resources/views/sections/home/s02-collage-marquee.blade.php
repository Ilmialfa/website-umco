@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Suasana Cafe';
    $heading = $section?->heading ?: 'Semua buat duduk tenang';
    $subheading = $section?->subheading ?: 'Dari kopi pagi sebelum kelas sampai meja panjang untuk diskusi malam di Kampus 2 UMRI.';
    $operatingDays = trim((string) \App\Support\Settings::get('hours.operating_days', '')) ?: 'Buka tiap hari 08.00 - 22.00 WIB';
    $phrases = $section?->data['phrases'] ?? [
        $operatingDays,
        'Aren Coffee Rp 25.500',
        'Minimal pesan meja ' . max(2, (int) \App\Support\Settings::get('reservation.min_party', 2)) . ' orang',
        'Nasi Ayam Sambal Matah Rp 24.000',
        'Colokan di banyak sudut',
    ];
    if (empty($phrases)) {
        $phrases = $favoriteMenuItems->pluck('name')->all();
    }

    $collagePhotos = [
        ['src' => '/images/bistro-patio-table.webp', 'alt' => 'Meja outdoor UMCO di bawah pepohonan'],
        ['src' => '/images/hero-banner-full.webp', 'alt' => 'Suasana pagi depan cafe kampus'],
        ['src' => '/images/drinks-trio-banner.webp', 'alt' => 'Tiga pilihan minuman dingin di meja kayu'],
        ['src' => '/storage/menu/matcha-800.webp', 'alt' => 'Es Matcha racikan bar UMCO'],
        ['src' => '/storage/menu/nasi-ayam-sambal-matah-800.webp', 'alt' => 'Nasi Ayam Sambal Matah hangat'],
    ];
@endphp

<section
    id="marquee"
    data-nav-theme="light"
    data-module="marquee-collage"
    class="bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    {{-- Strip Marquee Paralaks Utama tepat di bawah Banner (Bisa Diubah Owner lewat CMS PageSection marquee) --}}
    <div class="bg-[var(--umco-hutan)] text-white py-4 sm:py-5 overflow-hidden select-none">
        <div data-marquee-parallax>
            <div class="umco-marquee-track font-display font-semibold text-[1.4rem] sm:text-[1.9rem] lg:text-[2.2rem] leading-none tracking-[0.01em]">
                @for ($copy = 0; $copy < 2; $copy++)
                    <div class="flex items-center gap-7 sm:gap-10 pr-7 sm:pr-10" @if ($copy === 1) aria-hidden="true" @endif>
                        @foreach ($phrases as $idx => $phrase)
                            <span class="{{ $idx % 2 === 1 ? 'text-[var(--aksen-soft)]' : 'text-white' }} whitespace-nowrap">{{ $phrase }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--aksen-soft)] shrink-0" aria-hidden="true"></span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Kolase Editorial dengan Judul Tengah Tegas Maksimal 1-2 Baris --}}
    <div class="umco-section">
        <div class="umco-container space-y-10 sm:space-y-14">
            <div class="max-w-5xl mx-auto text-center space-y-3">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[28px] leading-none text-[var(--mentega)]">
                    {{ $eyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-display-l font-display font-bold text-[var(--tinta)]">
                    {{ $heading }}
                </h2>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl mx-auto">
                    {{ $subheading }}
                </p>
            </div>

            {{-- Kolase 5 Foto Parallax (rapi di HP, Tablet, & Desktop tanpa tumpuk) --}}
            <div class="grid grid-cols-2 sm:grid-cols-12 gap-3 sm:gap-4 lg:gap-5 items-center">
                <div data-collage-card data-reveal-media class="col-span-1 sm:col-span-3 rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[3/4] sm:mt-8">
                    <img src="{{ asset($collagePhotos[0]['src']) }}" alt="{{ $collagePhotos[0]['alt'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>

                <div data-collage-card data-reveal-media class="col-span-1 sm:col-span-4 rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/3] sm:aspect-[5/4] sm:-mt-4">
                    <img src="{{ asset($collagePhotos[1]['src']) }}" alt="{{ $collagePhotos[1]['alt'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>

                <div data-collage-card data-reveal-media class="col-span-2 sm:col-span-2 sm:flex sm:flex-col gap-3 sm:gap-4 hidden">
                    <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-square">
                        <img src="{{ asset($collagePhotos[3]['src']) }}" alt="{{ $collagePhotos[3]['alt'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                    </div>
                    <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/3]">
                        <img src="{{ asset($collagePhotos[4]['src']) }}" alt="{{ $collagePhotos[4]['alt'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                    </div>
                </div>

                <div data-collage-card data-reveal-media class="col-span-2 sm:col-span-3 rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[16/9] sm:aspect-[3/4] sm:mt-6">
                    <img src="{{ asset($collagePhotos[2]['src']) }}" alt="{{ $collagePhotos[2]['alt'] }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>
