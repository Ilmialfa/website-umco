@php
    $feed = $instagramFeed ?? [
        'username' => ltrim((string) \App\Support\Settings::get('contact.instagram_handle', 'um.co_house'), '@') ?: 'um.co_house',
        'profile_url' => \App\Support\Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house'),
        'source' => 'curated',
        'posts' => [],
    ];
    $igUsername = $feed['username'] ?: 'um.co_house';
    $igUrl = $section?->cta_url ?: ($feed['profile_url'] ?: 'https://www.instagram.com/um.co_house');
    $eyebrow = $section?->data['eyebrow'] ?? 'Instagram Harian';
    $heading = $section?->heading ?: 'Aktivitas Terbaru di Instagram';
    $subheading = $section?->subheading ?: 'Potret harian dari meja bar, seduhan sore, dan kabar acara di @' . $igUsername . '.';
    $ctaLabel = $section?->cta_label ?: 'Ikuti @' . $igUsername;
    $initialPosts = array_slice($feed['posts'] ?? [], 0, 8);
@endphp

<section
    id="instagram-feed"
    data-nav-theme="light"
    x-data="{
        username: @js($igUsername),
        profileUrl: @js($igUrl),
        source: @js($feed['source'] ?? 'curated'),
        posts: @js($initialPosts),
        syncing: false,
        async refreshFeed() {
            if (this.syncing) return;
            this.syncing = true;
            try {
                const res = await fetch('{{ url('/instagram-feed') }}', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    if (Array.isArray(data.posts) && data.posts.length) {
                        this.posts = data.posts.slice(0, 8);
                        this.source = data.source || this.source;
                        if (data.profile_url) this.profileUrl = data.profile_url;
                        if (data.username) this.username = data.username;
                    }
                }
            } catch (_) {
                // Tetap tampilkan feed awal dengan aman bila offline
            } finally {
                this.syncing = false;
            }
        },
        init() {
            setTimeout(() => this.refreshFeed(), 450);
        }
    }"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        {{-- Header gaya BKA UMRI: Judul tegas kiri (maks 1-2 baris) + Tombol Ikuti @um.co_house kanan --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5">
            <div class="max-w-3xl space-y-2.5">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                    {{ $eyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                    {{ $heading }}
                </h2>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl">
                    {{ $subheading }}
                </p>
            </div>

            <div data-reveal="fade" class="shrink-0">
                <a
                    :href="profileUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2.5 rounded-full bg-[var(--kertas-terang)] border border-[var(--garis-kuwat)] hover:bg-[var(--mentega)] hover:text-white hover:border-[var(--mentega)] px-5 h-11 text-sm font-semibold transition-colors"
                >
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                    </svg>
                    <span>{{ $ctaLabel }}</span>
                    <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-up-right"/></svg>
                </a>
            </div>
        </div>

        {{-- Grid 4 Kolom Feed Instagram Real-Time --}}
        <div data-reveal-stagger class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <template x-for="(post, idx) in posts" :key="'ig-' + post.id + '-' + idx">
                <a
                    :href="post.permalink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group relative rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/5] block"
                >
                    <img
                        :src="post.image"
                        :alt="post.caption"
                        loading="lazy"
                        decoding="async"
                        referrerpolicy="no-referrer"
                        class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500"
                    >

                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#101813]/88 via-[#101813]/45 to-transparent p-3.5 sm:p-4 pt-14 text-white space-y-2">
                        <div class="flex items-center gap-3 text-[11px] sm:text-xs font-semibold text-white/90">
                            <template x-if="post.likes !== null">
                                <span class="inline-flex items-center gap-1 tabular-nums">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                    <span x-text="post.likes"></span>
                                </span>
                            </template>
                            <template x-if="post.comments !== null">
                                <span class="inline-flex items-center gap-1 tabular-nums">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                    <span x-text="post.comments"></span>
                                </span>
                            </template>
                            <span class="ml-auto text-[11px] text-white/75 truncate" x-text="post.timestamp_human"></span>
                        </div>

                        <p class="text-xs sm:text-[13px] leading-snug text-white/95 line-clamp-2" x-text="post.caption"></p>

                        <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-[#D6EBDC] group-hover:translate-x-0.5 transition-transform">
                            <span>Buka di Instagram</span>
                            <svg class="w-3.5 h-3.5"><use href="#icon-arrow-up-right"/></svg>
                        </span>
                    </div>
                </a>
            </template>
        </div>
    </div>
</section>
