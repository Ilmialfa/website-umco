<?php

namespace App\Services;

use App\Models\GalleryItem;
use App\Support\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InstagramFeedService
{
    public const CACHE_KEY = 'instagram_feed_data';

    public const STALE_CACHE_KEY = 'umco.instagram_feed.last_live.v1';

    public const CACHE_TTL_SECONDS = 3600;

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil feed Instagram hanya dari cache atau galeri kurasi tanpa HTTP sinkron.
     * Dipakai saat merender beranda agar TTFB tetap cepat dan stabil.
     *
     * @return array<string, mixed>
     */
    public function getCachedOrFallback(int $limit = 8): array
    {
        $handle = $this->resolveHandle();
        $profileUrl = $this->resolveProfileUrl($handle);
        $limit = min(max($limit, 1), 8);

        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            if (($cached['hidden'] ?? false) === true) {
                return [
                    'username' => $handle,
                    'profile_url' => $profileUrl,
                    'bio' => null,
                    'source' => 'hidden',
                    'hidden' => true,
                    'posts' => [],
                ];
            }

            if (! empty($cached['posts'])) {
                $cached['posts'] = array_slice($cached['posts'], 0, $limit);
                $cached['profile_url'] = $cached['bio']['link'] ?? $profileUrl;
                $cached['username'] = $cached['bio']['username'] ?? $handle;

                return $cached;
            }
        }

        $stale = Cache::get(self::STALE_CACHE_KEY);
        if (is_array($stale) && ! empty($stale['posts'])) {
            $stale['source'] = 'cache';
            $stale['posts'] = array_slice($stale['posts'], 0, $limit);
            $stale['profile_url'] = $stale['bio']['link'] ?? $profileUrl;
            $stale['username'] = $stale['bio']['username'] ?? $handle;

            return $stale;
        }

        return $this->buildCuratedFallback($handle, $profileUrl, $limit);
    }

    /**
     * Ambil feed Instagram via SociableKIT (AccentAPI JSON) dengan cache 1 jam (3600 detik).
     * Mengembalikan struktur ter-normalisasi sesuai kontrak Custom UI Instagram.
     *
     * @return array<string, mixed>
     */
    public function getFeed(int $limit = 8, bool $forceRefresh = false): array
    {
        $handle = $this->resolveHandle();
        $profileUrl = $this->resolveProfileUrl($handle);
        $limit = min(max($limit, 1), 8);
        $widgetId = $this->resolveWidgetId();

        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            if (($cached['hidden'] ?? false) === true) {
                return [
                    'username' => $handle,
                    'profile_url' => $profileUrl,
                    'bio' => null,
                    'source' => 'hidden',
                    'hidden' => true,
                    'posts' => [],
                ];
            }

            if (! empty($cached['posts'])) {
                $cached['posts'] = array_slice($cached['posts'], 0, $limit);
                $cached['profile_url'] = $cached['bio']['link'] ?? $profileUrl;
                $cached['username'] = $cached['bio']['username'] ?? $handle;

                return $cached;
            }
        }

        if ($widgetId !== '' && (! app()->runningUnitTests() || $forceRefresh)) {
            $live = $this->fetchViaSociableKit($widgetId, $handle, $profileUrl);

            if ($live === false) {
                // Kuota free tier habis / user_info.show_feed = false / feed kosong -> sembunyikan section sesuai panduan
                $hiddenState = [
                    'username' => $handle,
                    'profile_url' => $profileUrl,
                    'bio' => null,
                    'source' => 'hidden',
                    'hidden' => true,
                    'posts' => [],
                ];
                Cache::put(self::CACHE_KEY, $hiddenState, now()->addMinutes(15));

                return $hiddenState;
            }

            if (is_array($live) && ! empty($live['posts'])) {
                Cache::put(self::CACHE_KEY, $live, now()->addSeconds(self::CACHE_TTL_SECONDS));
                Cache::put(self::STALE_CACHE_KEY, $live, now()->addDays(7));
                $live['posts'] = array_slice($live['posts'], 0, $limit);

                return $live;
            }
        }

        $stale = Cache::get(self::STALE_CACHE_KEY);
        if (is_array($stale) && ! empty($stale['posts'])) {
            $stale['source'] = 'cache';
            $stale['posts'] = array_slice($stale['posts'], 0, $limit);
            Cache::put(self::CACHE_KEY, $stale, now()->addMinutes(5));

            return $stale;
        }

        $curated = $this->buildCuratedFallback($handle, $profileUrl, $limit);
        Cache::put(self::CACHE_KEY, $curated, now()->addMinutes(5));

        return $curated;
    }

    /**
     * Ambil feed JSON dari endpoint SociableKIT AccentAPI.
     * Mengembalikan array ternormalisasi, `false` jika show_feed dimatikan / feed kosong, atau `null` jika jaringan gagal.
     *
     * @return array<string, mixed>|false|null
     */
    private function fetchViaSociableKit(string $widgetId, string $handle, string $profileUrl): array|false|null
    {
        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get("https://data.accentapi.com/feed/{$widgetId}.json");

            if (! $response->successful()) {
                return null;
            }

            $json = $response->json();
            if (! is_array($json)) {
                return null;
            }

            $showFeed = (bool) ($json['user_info']['show_feed'] ?? true);
            $rawPosts = $json['posts'] ?? [];

            if (! $showFeed || ! is_array($rawPosts) || empty($rawPosts)) {
                return false;
            }

            $rawBio = is_array($json['bio'] ?? null) ? $json['bio'] : [];
            $bioUsername = ltrim((string) ($rawBio['username'] ?? $handle), '@') ?: $handle;
            $bioLink = (string) ($rawBio['link'] ?? $profileUrl) ?: $profileUrl;

            $bio = [
                'username' => $bioUsername,
                'full_name' => (string) ($rawBio['full_name'] ?? 'UMCO House'),
                'followers_count' => (int) ($rawBio['followers_count'] ?? 0),
                'posts_count' => (int) ($rawBio['posts_count'] ?? 0),
                'profile_pic_url' => (string) ($rawBio['profile_pic_url'] ?? ''),
                'link' => $bioLink,
            ];

            $posts = [];
            foreach ($rawPosts as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $image = (string) ($row['image_url'] ?? $row['thumbnail_url'] ?? $row['pic_src'] ?? '');
                if ($image === '') {
                    continue;
                }

                $likes = (int) ($row['pic_like_count_formatted'] ?? 0);
                $comments = (int) ($row['pic_comment_count_formatted'] ?? 0);
                $type = (string) ($row['pic_type'] ?? 'Image');
                $postLink = (string) ($row['link'] ?? $bioLink) ?: $bioLink;

                $posts[] = [
                    'id' => (string) ($row['id'] ?? uniqid('ig-')),
                    'link' => $postLink,
                    'permalink' => $postLink,
                    'image' => $image,
                    'caption' => trim((string) ($row['pic_text'] ?? '')),
                    'likes' => $likes > 0 ? $likes : null,
                    'comments' => $comments > 0 ? $comments : null,
                    'type' => $type,
                    'timestamp_human' => null,
                    'is_video' => strcasecmp($type, 'Video') === 0,
                ];

                if (count($posts) >= 8) {
                    break;
                }
            }

            if ($posts === []) {
                return false;
            }

            return [
                'username' => $bioUsername,
                'profile_url' => $bioLink,
                'bio' => $bio,
                'source' => 'sociablekit',
                'hidden' => false,
                'synced_at' => now()->toIso8601String(),
                'posts' => $posts,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveHandle(): string
    {
        return ltrim((string) Settings::get('contact.instagram_handle', 'um.co_house'), '@') ?: 'um.co_house';
    }

    private function resolveProfileUrl(string $handle): string
    {
        return (string) Settings::get('contact.instagram_url', 'https://www.instagram.com/'.$handle)
            ?: 'https://www.instagram.com/'.$handle;
    }

    private function resolveWidgetId(): string
    {
        $fromSetting = trim((string) Settings::get('contact.instagram_widget_id', ''));
        $raw = $fromSetting !== '' ? $fromSetting : trim((string) config('services.instagram.sociablekit_feed_id', ''));

        return (string) preg_replace('/[^A-Za-z0-9_-]/', '', $raw);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCuratedFallback(string $handle, string $profileUrl, int $limit): array
    {
        $gallery = GalleryItem::active()
            ->limit($limit)
            ->get();

        $posts = $gallery->map(function ($item) use ($profileUrl) {
            $img = $item->image_path ?: '/images/bistro-patio-table.webp';
            if (! str_starts_with($img, 'http') && ! str_starts_with($img, '/')) {
                $img = '/storage/'.ltrim($img, '/');
            }

            return [
                'id' => 'curated-'.$item->id,
                'link' => $profileUrl,
                'permalink' => $profileUrl,
                'image' => asset($img),
                'caption' => $item->caption ?: 'Suasana harian dan sajian bar UMCO House Kampus 2 UMRI.',
                'likes' => null,
                'comments' => null,
                'type' => 'Image',
                'timestamp_human' => null,
                'is_video' => false,
            ];
        })->values()->all();

        if (empty($posts)) {
            $defaults = [
                ['/images/hero-iced-coffee-splash.webp', 'Racikan susu segar & espresso dingin sebelum kelas sore di @um.co_house.'],
                ['/images/bistro-patio-table.webp', 'Meja halaman teduh buat diskusi santai bareng teman kampus UMRI.'],
                ['/images/drinks-trio-banner.webp', 'Aren Coffee, Matcha Oat dingin, dan cokelat sore paling dicari.'],
                ['/images/hero-banner-full.webp', 'Pintu buka jam 08.00 - 22.00 WIB tiap hari di Jalan Tuanku Tambusai Ujung.'],
                ['/images/hero-photo.webp', 'Sudut duduk nyaman dengan colokan dan Wi-Fi di Kampus 2 UMRI.'],
            ];
            foreach ($defaults as $idx => [$src, $cap]) {
                $posts[] = [
                    'id' => 'default-'.$idx,
                    'link' => $profileUrl,
                    'permalink' => $profileUrl,
                    'image' => asset($src),
                    'caption' => $cap,
                    'likes' => null,
                    'comments' => null,
                    'type' => 'Image',
                    'timestamp_human' => null,
                    'is_video' => false,
                ];
            }
        }

        return [
            'username' => $handle,
            'profile_url' => $profileUrl,
            'bio' => null,
            'source' => 'curated',
            'hidden' => false,
            'synced_at' => now()->toIso8601String(),
            'posts' => array_slice($posts, 0, $limit),
        ];
    }
}
