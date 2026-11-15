<footer id="public-footer" data-nav-theme="dark" data-nav-transparent="footer" class="relative umco-footer-gradient text-[var(--kertas)] pt-14 sm:pt-20 pb-0 overflow-hidden">
    <div class="umco-container">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-8 sm:gap-10 pb-10 sm:pb-12">
            <div class="sm:col-span-5 space-y-3">
                <p class="font-display font-bold text-2xl tracking-[0.06em] text-white">{{ $brandShort === 'UMCO' ? 'UM.CO' : $brandShort }}</p>
                <p class="text-sm sm:text-[15px] text-white/85 leading-relaxed max-w-sm">
                    {{ $address }}. {{ $operatingDays }}.
                </p>
                @if ($storySnippet !== '')
                    <p class="text-xs text-white/70 leading-relaxed max-w-sm">{{ $storySnippet }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-4 pt-1 text-sm font-medium text-white">
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[var(--aksen-soft)] transition-colors">
                        Buka di Google Maps
                    </a>
                    <a href="{{ $collabWaLink }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[var(--aksen-soft)] transition-colors">
                        Hubungi kami (WhatsApp)
                    </a>
                </div>
            </div>

            <div class="sm:col-span-4 grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2.5">
                    <p class="font-semibold text-white">Halaman</p>
                    <ul class="space-y-2 text-white/80">
                        <li><a href="{{ url('/') }}" wire:navigate class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/menu') }}" wire:navigate class="hover:text-white transition-colors">Menu</a></li>
                        <li><a href="{{ url('/galeri') }}" wire:navigate class="hover:text-white transition-colors">Galeri</a></li>
                        <li><a href="{{ url('/cerita') }}" wire:navigate class="hover:text-white transition-colors">Cerita kami</a></li>
                        <li><a href="{{ url('/reservasi') }}" wire:navigate class="hover:text-white transition-colors">Pesan meja</a></li>
                    </ul>
                </div>
                <div class="space-y-2.5">
                    <p class="font-semibold text-white">Kontak</p>
                    <ul class="space-y-2 text-white/80">
                        <li><a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">Instagram</a></li>
                        <li><a href="{{ $collabWaLink }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                        <li><a href="{{ url('/admin') }}" class="hover:text-white transition-colors">Staf</a></li>
                    </ul>
                </div>
            </div>

            <div class="sm:col-span-3 sm:text-right space-y-2 text-sm text-white/80">
                <p>&copy; {{ date('Y') }} {{ $brandName }}</p>
                <p>{{ $tagline }}</p>
                @if ($plusCode !== '')
                    <p class="text-xs text-white/65">Plus Code: {{ $plusCode }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Wordmark raksasa di belakang gelas + meja cream menyatu penuh ke ujung layar (sepenuhnya responsif) --}}
    <div class="relative w-full overflow-hidden select-none -mt-4 sm:-mt-8 lg:-mt-12">
        <div
            data-footer-wordmark
            class="absolute inset-x-0 top-[4.5vw] sm:top-[5vw] lg:top-[5.5vw] z-0 px-2 sm:px-4 font-display font-bold text-[38vw] sm:text-[36.5vw] lg:text-[35vw] leading-[0.84] tracking-[-0.03em] bg-gradient-to-b from-white via-[#F1F7F3] to-white/0 bg-clip-text text-transparent text-center whitespace-nowrap will-change-transform pointer-events-none"
        >
            {{ $footerWordmark }}
        </div>

        <img
            data-footer-cutout
            src="{{ asset($footerCutout) }}"
            alt="Segelas kopi susu dingin UMCO di atas tatakan batu dan meja batu"
            width="1024"
            height="682"
            loading="lazy"
            decoding="async"
            class="relative z-10 w-full h-auto block pointer-events-none"
        >
    </div>
</footer>
