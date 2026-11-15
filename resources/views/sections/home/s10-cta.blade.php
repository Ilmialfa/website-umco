@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Kampus 2 UMRI · Pekanbaru';
    $heading = $section?->heading ?: 'Mampir dulu, kursimu kami siapkan.';
    $subheading = $section?->subheading ?: 'Buka setiap hari pukul 08.00 - 22.00 WIB di Kampus 2 UMRI. Pesan meja lebih dulu atau hubungi kami untuk acara dan kolaborasi.';
    $primaryLabel = $section?->cta_label ?: 'Pesan meja';
    $primaryUrl = $section?->cta_url ?: '/reservasi';
    $secondaryLabel = $section?->data['secondary_label'] ?? 'Hubungi kami';

    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = method_exists(\App\Support\PhoneNumber::class, 'normalizeBusiness')
        ? \App\Support\PhoneNumber::normalizeBusiness($rawWa)
        : preg_replace('/\D+/', '', (string) $rawWa);
    if ($waDigits && str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $collabTemplate = \App\Support\Settings::get('contact.collab_template', 'Halo UMCO, saya ingin menghubungi terkait acara atau kolaborasi.');
    $waLink = $waDigits ? ('https://wa.me/' . $waDigits . '?text=' . rawurlencode($collabTemplate)) : '#';
@endphp

<section
    id="cta-footer"
    data-nav-theme="light"
    class="umco-cta-gradient text-[var(--tinta)] pt-20 pb-24 sm:pt-28 sm:pb-32 overflow-hidden"
>
    <div class="umco-container">
        <div class="max-w-2xl mx-auto text-center space-y-5">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega-tua)]">
                {{ $eyebrow }}
            </p>

            <h2 data-reveal="rise" class="type-display font-display font-bold text-[var(--tinta)]">
                {{ $heading }}
            </h2>

            <p data-reveal="fade" class="type-body sm:type-body-l text-[var(--tinta)]/85 max-w-lg mx-auto leading-relaxed">
                {{ $subheading }}
            </p>

            <div data-reveal-stagger class="pt-3 flex flex-wrap items-center justify-center gap-3">
                <a
                    href="{{ url($primaryUrl) }}"
                    wire:navigate
                    class="btn-umco bg-[#134731] text-white hover:bg-[#0E3524]"
                >
                    <span class="btn-roll">
                        <span>{{ $primaryLabel }}</span>
                        <span aria-hidden="true">{{ $primaryLabel }}</span>
                    </span>
                </a>

                <a
                    href="{{ $waLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-umco bg-white text-[#141916] hover:bg-[#EDE9E0]"
                >
                    <span>{{ $secondaryLabel }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
