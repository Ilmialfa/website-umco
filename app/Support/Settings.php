<?php

namespace App\Support;

use App\Models\Setting;
use App\Observers\PublicContentObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Settings
{
    protected const CACHE_KEY = 'umco_all_settings';

    /** @var array<string, mixed>|null */
    protected static ?array $memoryCache = null;

    /**
     * Get a setting by key (e.g. 'brand.brand_name', 'contact.phone_wa', 'reservation.dp_percentage').
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        return $default;
    }

    /**
     * Get all settings as a dictionary keyed by both 'group.key' (canonical) and bare 'key'.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$memoryCache !== null) {
            return self::$memoryCache;
        }

        return self::$memoryCache = Cache::rememberForever(self::CACHE_KEY, function (): array {
            try {
                $settings = Setting::query()->orderBy('id')->get();
                $dict = [];

                foreach ($settings as $setting) {
                    $val = $setting->payload;
                    $decoded = is_string($val) ? json_decode($val, true) : null;
                    $parsed = (is_string($val) && json_last_error() === JSON_ERROR_NONE && ! is_numeric($val))
                        ? $decoded
                        : $val;

                    $dict["{$setting->group}.{$setting->key}"] = $parsed;

                    if (! array_key_exists($setting->key, $dict)) {
                        $dict[$setting->key] = $parsed;
                    }
                }

                return $dict;
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Get all settings in a specific group.
     *
     * @return array<string, mixed>
     */
    public static function group(string $group): array
    {
        $all = self::all();
        $prefix = "{$group}.";
        $results = [];

        foreach ($all as $k => $v) {
            if (str_starts_with($k, $prefix)) {
                $subKey = substr($k, strlen($prefix));
                $results[$subKey] = $v;
            }
        }

        return $results;
    }

    /**
     * Set a setting value keyed uniquely by (group, key) and flush cache.
     */
    public static function set(string $key, mixed $value, ?string $group = null): void
    {
        [$resolvedGroup, $resolvedKey, $payload] = self::prepareEntry($key, $value, $group);

        Setting::updateOrCreate(
            ['group' => $resolvedGroup, 'key' => $resolvedKey],
            ['payload' => $payload]
        );

        self::flush();
    }

    /**
     * Save many settings in one transaction and purge caches once.
     *
     * @param  array<string, mixed>  $values  Keyed by 'group.key'
     */
    public static function setMany(array $values): void
    {
        Setting::withoutEvents(function () use ($values): void {
            DB::transaction(function () use ($values): void {
                foreach ($values as $key => $value) {
                    [$resolvedGroup, $resolvedKey, $payload] = self::prepareEntry((string) $key, $value);

                    Setting::updateOrCreate(
                        ['group' => $resolvedGroup, 'key' => $resolvedKey],
                        ['payload' => $payload]
                    );
                }
            });
        });

        PublicContentObserver::clearAllCaches();
    }

    /**
     * @return array{0: string, 1: string, 2: ?string}
     */
    protected static function prepareEntry(string $key, mixed $value, ?string $group = null): array
    {
        $itemKey = $key;
        if (str_contains($key, '.')) {
            [$grp, $k] = explode('.', $key, 2);
            $group = $group ?? $grp;
            $itemKey = $k;
        }

        $group = $group ?: 'general';
        $payload = match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) || is_object($value) => (string) json_encode($value),
            $value === null => null,
            default => (string) $value,
        };

        return [$group, $itemKey, $payload];
    }

    /**
     * Flush settings cache.
     */
    public static function flush(): void
    {
        self::$memoryCache = null;
        Cache::forget(self::CACHE_KEY);
    }
}
