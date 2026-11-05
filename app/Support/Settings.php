<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected const CACHE_KEY = 'umco_all_settings';

    /**
     * Get a setting by key (e.g. 'brand.name', 'contact.phone', 'reservation.dp_percent').
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        // Check if key is formatted as group.key
        if (str_contains($key, '.')) {
            [$group, $itemKey] = explode('.', $key, 2);
            if (isset($all[$itemKey])) {
                return $all[$itemKey];
            }
        }

        return $default;
    }

    /**
     * Get all settings as key => value dictionary from cache.
     */
    public static function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                $settings = Setting::all();
                $dict = [];
                foreach ($settings as $setting) {
                    $val = $setting->payload;
                    $decoded = json_decode($val, true);
                    $dict[$setting->key] = (json_last_error() === JSON_ERROR_NONE && !is_numeric($val)) ? $decoded : $val;
                    $dict["{$setting->group}.{$setting->key}"] = $dict[$setting->key];
                }
                return $dict;
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Get all settings in a specific group.
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
     * Set a setting value and flush cache.
     */
    public static function set(string $key, mixed $value, ?string $group = null): void
    {
        $itemKey = $key;
        if (str_contains($key, '.')) {
            [$grp, $k] = explode('.', $key, 2);
            $group = $group ?? $grp;
            $itemKey = $k;
        }

        $group = $group ?? 'general';
        $payload = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        Setting::updateOrCreate(
            ['key' => $itemKey],
            [
                'group' => $group,
                'payload' => $payload,
            ]
        );

        self::flush();
    }

    /**
     * Flush cache.
     */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
