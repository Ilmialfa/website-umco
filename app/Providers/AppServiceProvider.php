<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use App\Models\Testimonial;
use App\Observers\PublicContentObserver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerObservers();
    }

    /**
     * Register model observers for auto-purging public cache.
     */
    protected function registerObservers(): void
    {
        MenuCategory::observe(PublicContentObserver::class);
        MenuItem::observe(PublicContentObserver::class);
        Banner::observe(PublicContentObserver::class);
        Setting::observe(PublicContentObserver::class);
        GalleryItem::observe(PublicContentObserver::class);
        Facility::observe(PublicContentObserver::class);
        StreetStall::observe(PublicContentObserver::class);
        Testimonial::observe(PublicContentObserver::class);
        StoryBlock::observe(PublicContentObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
