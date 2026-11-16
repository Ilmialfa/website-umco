<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Banner;
use App\Models\ClosedDate;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Reservation;
use App\Models\Rule;
use App\Models\SeatingOption;
use App\Models\Setting;
use App\Models\SpendTier;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\PublicContentObserver;
use App\Policies\ActivityLogPolicy;
use App\Policies\ClosedDatePolicy;
use App\Policies\MenuCategoryPolicy;
use App\Policies\MenuItemPolicy;
use App\Policies\OwnerOnlyPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\UserPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        $this->registerPolicies();
        $this->registerSecurityAuditListeners();
    }

    /**
     * Record authentication lifecycle events into ActivityLog for owner security visibility.
     */
    protected function registerSecurityAuditListeners(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if (! $event->user instanceof User) {
                return;
            }
            ActivityLog::record('auth.login', User::class, $event->user->id, [
                'email' => $event->user->email,
                'role' => $event->user->role,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Failed::class, function (Failed $event): void {
            ActivityLog::record('auth.login_failed', User::class, $event->user?->getAuthIdentifier(), [
                'email' => (string) ($event->credentials['email'] ?? '-'),
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Lockout::class, function (Lockout $event): void {
            ActivityLog::record('auth.lockout', User::class, null, [
                'email' => (string) $event->request->input('email', '-'),
                'ip' => $event->request->ip(),
            ]);
        });

        User::updating(function (User $user): void {
            if ($user->isDirty('password') && $user->must_change_password) {
                $user->must_change_password = false;
            }
        });
    }

    /**
     * Register model policies for server-side authorization.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(MenuItem::class, MenuItemPolicy::class);
        Gate::policy(MenuCategory::class, MenuCategoryPolicy::class);
        Gate::policy(ClosedDate::class, ClosedDatePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);

        foreach ([
            Banner::class,
            Facility::class,
            Faq::class,
            GalleryItem::class,
            PageSection::class,
            Rule::class,
            SeatingOption::class,
            Setting::class,
            SpendTier::class,
            StoryBlock::class,
            StreetStall::class,
            Testimonial::class,
        ] as $ownerOnlyModel) {
            Gate::policy($ownerOnlyModel, OwnerOnlyPolicy::class);
        }
    }

    /**
     * Register model observers for auto-purging public & admin caches.
     */
    protected function registerObservers(): void
    {
        MenuCategory::observe(PublicContentObserver::class);
        MenuItem::observe(PublicContentObserver::class);
        Banner::observe(PublicContentObserver::class);
        Setting::observe(PublicContentObserver::class);
        GalleryItem::observe(PublicContentObserver::class);
        Facility::observe(PublicContentObserver::class);
        Faq::observe(PublicContentObserver::class);
        StreetStall::observe(PublicContentObserver::class);
        Testimonial::observe(PublicContentObserver::class);
        StoryBlock::observe(PublicContentObserver::class);
        PageSection::observe(PublicContentObserver::class);
        Rule::observe(PublicContentObserver::class);
        SpendTier::observe(PublicContentObserver::class);
        SeatingOption::observe(PublicContentObserver::class);
        ClosedDate::observe(PublicContentObserver::class);

        $flushAdminStats = static function (): void {
            Cache::forget('filament.reservations.pending_count');
            Cache::forget('filament.reservations.tab_counts');
            Cache::forget('filament.dashboard.stats');
            Cache::forget('filament.dashboard.charts.14d');
            Cache::forget('filament.dashboard.charts.30d');
            Cache::forget('filament.dashboard.charts.mix');
        };

        Reservation::saved($flushAdminStats);
        Reservation::deleted($flushAdminStats);
        MenuItem::saved($flushAdminStats);
        MenuItem::deleted($flushAdminStats);
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
