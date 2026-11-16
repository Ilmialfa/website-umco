@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Tanya Jawab';
    $heading = $section?->heading ?: 'Yang sering ditanyakan';
    $subheading = $section?->subheading ?: 'Semua yang perlu kamu tahu soal pesan meja, minimal belanja, DP QRIS, sampai acara komunitas.';
    $ctaLabel = $section?->cta_label ?: 'Tanya langsung via WhatsApp';

    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = method_exists(\App\Support\PhoneNumber::class, 'normalizeBusiness')
        ? \App\Support\PhoneNumber::normalizeBusiness($rawWa)
        : preg_replace('/\D+/', '', (string) $rawWa);
    if ($waDigits && str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $waAskLink = $waDigits
        ? ('https://wa.me/' . $waDigits . '?text=' . rawurlencode('Halo UMCO, saya ingin bertanya mengenai reservasi atau cafe.'))
        : '#';

    $faqList = ($faqs ?? collect())->values();
    if ($faqList->isEmpty()) {
        $faqList = collect([
            (object) [
                'question' => 'Bagaimana syarat pesan meja di UMCO secara online?',
                'answer' => 'Minimal reservasi 2 orang dengan memilih menu lebih dulu sesuai minimal belanja (mulai Rp 50.000), lalu mengunggah bukti bayar DP 50% atau lunas via QRIS.',
            ],
            (object) [
                'question' => 'Apakah bisa memilih posisi tempat duduk?',
                'answer' => 'Bisa. Kamu dapat memilih area duduk yang tersedia dan menambahkan catatan permintaan di langkah reservasi.',
            ],
            (object) [
                'question' => 'Berapa lama batas waktu keterlambatan karcis meja?',
                'answer' => 'Kami menahan meja hingga 15 menit setelah jam pesan. Bila ada kendala, kabari kami lewat WhatsApp sebelum batas waktu.',
            ],
        ]);
    }
@endphp

<section
    id="faq"
    data-nav-theme="light"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            {{-- Kiri: Pengantar & CTA WhatsApp --}}
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-5">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                    {{ $eyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                    {{ $heading }}
                </h2>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] leading-relaxed max-w-md">
                    {{ $subheading }}
                </p>
                <div data-reveal="fade" class="pt-1">
                    <a
                        href="{{ $waAskLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-umco bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white"
                    >
                        <span>{{ $ctaLabel }}</span>
                        <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                    </a>
                </div>
            </div>

            {{-- Kanan: Daftar Akordeon FAQ Interaktif --}}
            <div class="lg:col-span-7 space-y-3" x-data="{ openIdx: 0 }" data-reveal-stagger>
                @foreach ($faqList as $idx => $faq)
                    <div class="rounded-2xl bg-[var(--kertas-terang)] transition-colors">
                        <button
                            type="button"
                            @click="openIdx = openIdx === {{ $idx }} ? -1 : {{ $idx }}"
                            :aria-expanded="(openIdx === {{ $idx }}).toString()"
                            class="w-full px-6 py-5 flex items-center justify-between gap-4 text-left cursor-pointer group"
                        >
                            <span class="font-display font-bold text-lg sm:text-xl leading-snug group-hover:text-[var(--mentega)] transition-colors">
                                {{ $faq->question }}
                            </span>
                            <span
                                class="w-8 h-8 rounded-full bg-[var(--batu)] text-[var(--tinta)] inline-flex items-center justify-center shrink-0 text-base font-bold transition-transform duration-300"
                                :class="openIdx === {{ $idx }} ? 'rotate-45 bg-[var(--mentega)] text-white' : ''"
                                aria-hidden="true"
                            >
                                +
                            </span>
                        </button>
                        <div
                            x-show="openIdx === {{ $idx }}"
                            x-collapse
                            style="display: none;"
                        >
                            <p class="px-6 pb-6 pt-0 text-sm sm:text-[15px] text-[var(--tinta-70)] leading-relaxed">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
