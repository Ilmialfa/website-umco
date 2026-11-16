@php
    $feed = $instagramFeed ?? [
        'username' => ltrim((string) \App\Support\Settings::get('contact.instagram_handle', 'um.co_house'), '@') ?: 'um.co_house',
        'profile_url' => \App\Support\Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house'),
        'bio' => null,
        'source' => 'curated',
        'hidden' => false,
        'posts' => [],
    ];
    $igUsername = $feed['bio']['username'] ?? ($feed['username'] ?: 'um.co_house');
    $igUrl = $section?->cta_url ?: ($feed['bio']['link'] ?? $feed['profile_url'] ?: 'https://www.instagram.com/um.co_house');
    $eyebrow = $section?->data['eyebrow'] ?? 'Instagram Harian';
    $heading = $section?->heading ?: 'Aktivitas Terbaru di Instagram';
    $followersCount = (int) ($feed['bio']['followers_count'] ?? 0);
    $defaultSubheading = $followersCount > 0
        ? 'Dokumentasi visual harian dan info cafe untuk ' . number_format($followersCount, 0, ',', '.') . ' pengikut @' . $igUsername . '.'
        : 'Potret harian dari meja bar, seduhan sore, dan kabar acara di @' . $igUsername . '.';
    $subheading = $section?->subheading ?: $defaultSubheading;
    $ctaLabel = $section?->cta_label ?: 'Ikuti @' . $igUsername;
    $initialPosts = array_slice($feed['posts'] ?? [], 0, 8);
    $isHidden = ($feed['hidden'] ?? false) === true || empty($initialPosts);
@endphp

@if (! $isHidden)
<section
    id="instagram-feed"
    data-nav-theme="light"
    aria-label="Feed Instagram Resmi {{ '@' . $igUsername }}"
    x-data="{
        username: @js($igUsername),
        profileUrl: @js($igUrl),
        source: @js($feed['source'] ?? 'curated'),
        bio: @js($feed['bio'] ?? null),
        posts: @js($initialPosts),
        hidden: false,
        syncing: false,
        get subheadingText() {
            const count = Number(this.bio?.followers_count ?? 0);
            if (count > 0) {
                return `Dokumentasi visual harian dan info cafe untuk ${count.toLocaleString('id-ID')} pengikut @${this.username}.`;
            }
            return @js($subheading);
        },
        async refreshFeed() {
            if (this.syncing) return;
            this.syncing = true;
            try {
                const res = await fetch('{{ url('/instagram-feed') }}', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    if (data.hidden === true || !Array.isArray(data.posts) || data.posts.length === 0) {
                        this.hidden = true;
                        this.posts = [];
                        return;
                    }
                    this.posts = data.posts.slice(0, 8);
                    this.source = data.source || this.source;
                    if (data.bio) this.bio = data.bio;
                    if (data.profile_url) this.profileUrl = data.profile_url;
                    if (data.username) this.username = data.username;
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
    x-show="!hidden && posts.length > 0"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        {{-- Header: Judul kiri + tombol Ikuti @handle kanan --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5">
            <div class="max-w-3xl space-y-2.5">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                    {{ $eyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                    {{ $heading }}
                </h2>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl" x-text="subheadingText">
                    {{ $subheading }}
                </p>
            </div>

            <div data-reveal="fade" class="shrink-0">
                <a
                    :href="profileUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2.5 rounded-full bg-[var(--kertas-terang)] hover:bg-[var(--mentega)] hover:text-white px-5 h-11 text-sm font-semibold transition-colors"
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

        {{-- Grid 2 / 3 / 4 Kolom Feed Instagram (SociableKIT Custom UI + Fallback) --}}
        <div data-reveal-stagger class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            <template x-for="(post, idx) in posts" :key="'ig-' + post.id + '-' + idx">
                <a
                    :href="post.link || post.permalink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group relative rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/5] block"
                >
                    <img
                        :src="post.image"
                        :alt="post.caption ? post.caption.slice(0, 80) : ('Postingan Instagram @' + username)"
                        loading="lazy"
                        decoding="async"
                        referrerpolicy="no-referrer"
                        class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-[1.05]"
                    >

                    <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-[#101813]/90 via-[#101813]/40 to-transparent p-3.5 sm:p-4 text-white opacity-100 md:opacity-0 transition-opacity duration-300 md:group-hover:opacity-100 space-y-2">
                        <template x-if="(post.likes !== null && post.likes > 0) || (post.comments !== null && post.comments > 0)">
                            <div class="flex items-center gap-3 text-xs font-semibold text-white">
                                <template x-if="post.likes !== null && post.likes > 0">
                                    <span class="inline-flex items-center gap-1 tabular-nums">
                                        <svg class="w-3.5 h-3.5 text-[#FBBF24]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                        <span x-text="post.likes"></span>
                                    </span>
                                </template>
                                <template x-if="post.comments !== null && post.comments > 0">
                                    <span class="inline-flex items-center gap-1 tabular-nums">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                        <span x-text="post.comments"></span>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <template x-if="post.caption">
                            <p class="text-xs sm:text-sm leading-relaxed text-white/95 line-clamp-2" x-text="post.caption"></p>
                        </template>

                        <span class="inline-flex items-center gap-1 text-xs font-bold text-[#DCEBE1] pt-0.5">
                            <span>Buka di Instagram</span>
                            <svg class="w-3.5 h-3.5"><use href="#icon-arrow-up-right"/></svg>
                        </span>
                    </div>
                </a>
            </template>
        </div>
    </div>
</section>
@endif
