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
        Settings::flush();
        Cache::forget('umco_home_data');
        Cache::forget('umco_menu_data');
        Cache::forget('umco_about_data');
        Cache::forget('umco_client_menu_json');
        Cache::forget('umco_banners');
        Cache::forget('umco_featured_testimonials');
    }
}
