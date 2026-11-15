<?php

namespace App\Services;

use App\Models\GalleryItem;
use App\Support\Settings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InstagramFeedService
{
    public const CACHE_KEY = 'umco.instagram_feed.v1';
    public const STALE_CACHE_KEY = 'umco.instagram_feed.last_live.v1';

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil daftar postingan Instagram UMCO secara otomatis & berkala.
     * Prioritas:
     * 1) Cache aktif (15 menit)
     * 2) Instagram Graph API (jika token diisi oleh Owner di Pengaturan)
     * 3) Public profile web endpoint (@um.co_house)
     * 4) Cache live sebelumnya (hingga 7 hari) atau galeri kurasi @um.co_house
     */
    public function getFeed(int $limit = 8, bool $forceRefresh = false): array
    {
        $handle = ltrim((string) Settings::get('contact.instagram_handle', 'um.co_house'), '@') ?: 'um.co_house';
        $profileUrl = (string) Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house') ?: 'https://www.instagram.com/um.co_house';
        $token = trim((string) Settings::get('contact.instagram_access_token', ''));

        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && ! empty($cached['posts'])) {
            $cached['posts'] = array_slice($cached['posts'], 0, $limit);
            $cached['profile_url'] = $profileUrl;
            $cached['username'] = $handle;

            return $cached;
        }

        if (! app()->runningUnitTests()) {
            $live = null;

            if ($token !== '') {
                $live = $this->fetchViaGraphApi($token, $handle, $profileUrl, max($limit, 8));
            }

            if ($live === null) {
                $live = $this->fetchViaPublicProfile($handle, $profileUrl, max($limit, 8));
            }

            if (is_array($live) && ! empty($live['posts'])) {
                Cache::put(self::CACHE_KEY, $live, now()->addMinutes(15));
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

    private function fetchViaGraphApi(string $token, string $handle, string $profileUrl, int $limit): ?array
    {
        try {
            $response = Http::timeout(4)
                ->retry(1, 200)
                ->get('https://graph.instagram.com/me/media', [
                    'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,like_count,comments_count',
                    'limit' => $limit,
                    'access_token' => $token,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $items = $response->json('data', []);
            if (! is_array($items) || empty($items)) {
                return null;
            }

            $posts = [];
            foreach ($items as $row) {
                $img = ($row['media_type'] ?? '') === 'VIDEO'
                    ? ($row['thumbnail_url'] ?? $row['media_url'] ?? null)
                    : ($row['media_url'] ?? null);

                if (! $img) {
                    continue;
                }

                $ts = ! empty($row['timestamp']) ? Carbon::parse($row['timestamp']) : null;
                $posts[] = [
                    'id' => (string) ($row['id'] ?? uniqid('ig-')),
                    'image' => $img,
                    'caption' => trim((string) ($row['caption'] ?? 'Aktivitas terbaru dari meja bar @' . $handle)),
                    'permalink' => (string) ($row['permalink'] ?? $profileUrl),
                    'likes' => isset($row['like_count']) ? (int) $row['like_count'] : null,
                    'comments' => isset($row['comments_count']) ? (int) $row['comments_count'] : null,
                    'timestamp_human' => $ts ? $ts->locale('id')->diffForHumans() : 'Baru saja',
                    'is_video' => ($row['media_type'] ?? '') === 'VIDEO',
                ];
            }

            if (empty($posts)) {
                return null;
            }

            return [
                'username' => $handle,
                'profile_url' => $profileUrl,
                'source' => 'live',
                'synced_at' => now()->toIso8601String(),
                'posts' => $posts,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchViaPublicProfile(string $handle, string $profileUrl, int $limit): ?array
    {
        try {
            $response = Http::timeout(4)
                ->withHeaders([
                    'x-ig-app-id' => '936619743392459',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                    'Accept' => '*/*',
                    'Referer' => 'https://www.instagram.com/' . $handle . '/',
                ])
                ->get('https://www.instagram.com/api/v1/users/web_profile_info/', [
                    'username' => $handle,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $user = $response->json('data.user');
            $edges = $user['edge_owner_to_timeline_media']['edges'] ?? [];
            if (! is_array($edges) || empty($edges)) {
                return null;
            }

            $posts = [];
            foreach (array_slice($edges, 0, $limit) as $edge) {
                $node = $edge['node'] ?? [];
                $img = $node['display_url'] ?? $node['thumbnail_src'] ?? null;
                $shortcode = $node['shortcode'] ?? null;
                if (! $img || ! $shortcode) {
                    continue;
                }

                $caption = $node['edge_media_to_caption']['edges'][0]['node']['text'] ?? ('Momen harian di @' . $handle);
                $takenAt = ! empty($node['taken_at_timestamp']) ? Carbon::createFromTimestamp((int) $node['taken_at_timestamp']) : null;

                $posts[] = [
                    'id' => (string) ($node['id'] ?? $shortcode),
                    'image' => $img,
                    'caption' => trim((string) $caption),
                    'permalink' => 'https://www.instagram.com/p/' . $shortcode . '/',
                    'likes' => $node['edge_liked_by']['count'] ?? $node['edge_media_preview_like']['count'] ?? null,
                    'comments' => $node['edge_media_to_comment']['count'] ?? null,
                    'timestamp_human' => $takenAt ? $takenAt->locale('id')->diffForHumans() : 'Baru saja',
                    'is_video' => (bool) ($node['is_video'] ?? false),
                ];
            }

            if (empty($posts)) {
                return null;
            }

            return [
                'username' => $handle,
                'profile_url' => $profileUrl,
                'followers' => $user['edge_followed_by']['count'] ?? null,
                'source' => 'live',
                'synced_at' => now()->toIso8601String(),
                'posts' => $posts,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function buildCuratedFallback(string $handle, string $profileUrl, int $limit): array
    {
        $gallery = GalleryItem::active()
            ->limit($limit)
            ->get();

        $posts = $gallery->map(function ($item, $idx) use ($profileUrl) {
            $img = $item->image_path ?: '/images/bistro-patio-table.jpg';
            if (! str_starts_with($img, 'http') && ! str_starts_with($img, '/')) {
                $img = '/storage/' . ltrim($img, '/');
            }

            return [
                'id' => 'curated-' . $item->id,
                'image' => asset($img),
                'caption' => $item->caption ?: 'Suasana harian dan sajian bar UMCO House Kampus 2 UMRI.',
                'permalink' => $profileUrl,
                'likes' => 42 + ($idx * 9),
                'comments' => 4 + ($idx % 5),
                'timestamp_human' => ($idx + 1) . ' hari lalu',
                'is_video' => false,
            ];
        })->values()->all();

        if (empty($posts)) {
            $defaults = [
                ['/images/hero-iced-coffee-splash.jpg', 'Racikan susu segar & espresso dingin sebelum kelas sore di @um.co_house.'],
                ['/images/bistro-patio-table.jpg', 'Meja halaman teduh buat diskusi santai bareng teman kampus UMRI.'],
                ['/images/drinks-trio-banner.jpg', 'Aren Coffee, Matcha Oat dingin, dan cokelat sore paling dicari.'],
                ['/images/hero-banner-full.jpg', 'Pintu buka jam 08.00 - 22.00 WIB tiap hari di Jalan Tuanku Tambusai Ujung.'],
            ];
            foreach ($defaults as $idx => [$src, $cap]) {
                $posts[] = [
                    'id' => 'default-' . $idx,
                    'image' => asset($src),
                    'caption' => $cap,
                    'permalink' => $profileUrl,
                    'likes' => 48 + ($idx * 11),
                    'comments' => 5 + $idx,
                    'timestamp_human' => ($idx + 1) . ' hari lalu',
                    'is_video' => false,
                ];
            }
        }

        return [
            'username' => $handle,
            'profile_url' => $profileUrl,
            'source' => 'curated',
            'synced_at' => now()->toIso8601String(),
            'posts' => array_slice($posts, 0, $limit),
        ];
    }
}
