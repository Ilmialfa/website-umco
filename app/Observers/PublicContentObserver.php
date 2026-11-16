<?php

namespace App\Observers;

use App\Support\Settings;
use Illuminate\Support\Facades\Cache;

class PublicContentObserver
{
    /**
     * Clear caches whenever public-facing models are updated.
     */
    public function saved(): void
    {
        $this->purgePublicCache();
    }

    public function deleted(): void
    {
        $this->purgePublicCache();
    }

    protected function purgePublicCache(): void
    {
        self::clearAllCaches();
    }

    public static function clearAllCaches(): void
    {
        Settings::flush();
        Cache::forget('umco_home_data');
        Cache::forget('umco_menu_data');
        Cache::forget('umco_about_data');
        Cache::forget('umco_cerita_data');
        Cache::forget('umco_client_menu_json');
        Cache::forget('umco_banners');
        Cache::forget('umco_featured_testimonials');
        Cache::forget('umco_page_sections_home');
        Cache::forget('umco_page_sections_cerita');
        Cache::forget('umco_rules');
        Cache::forget('umco_spend_tiers');
        Cache::forget('umco_seating_options');
        Cache::forget('umco_bean_origins');
    }
}
