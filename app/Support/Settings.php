<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected const CACHE_KEY = 'umco_all_settings';

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
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
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

        Setting::updateOrCreate(
            ['group' => $group, 'key' => $itemKey],
            ['payload' => $payload]
        );

        self::flush();
    }

    /**
     * Flush settings cache.
     */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
