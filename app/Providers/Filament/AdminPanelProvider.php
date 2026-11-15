<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\AdminLogin;
use App\Filament\Widgets\PlaceholderAuditWidget;
use App\Filament\Widgets\ReservationStatsWidget;
use App\Filament\Widgets\UpcomingReservationsWidget;
use App\Http\Middleware\EnforcePasswordChange;
use App\Http\Middleware\SecurityHeaders;
use App\Support\Settings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->profile()
            ->darkMode(false)
            ->brandName('UMCO')
            ->brandLogo(fn (): HtmlString => new HtmlString(
                '<span class="inline-flex items-center gap-2.5">'
                .'<img src="/images/umco-logo.webp" alt="Logo UMCO" class="h-7 w-7 shrink-0 rounded-full object-cover" />'
                .'<span class="umco-brand-label font-semibold tracking-tight text-slate-900">UMCO Backoffice</span>'
                .'</span>'
            ))
            ->brandLogoHeight('1.75rem')
            ->favicon('/favicon.svg')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                function (): HtmlString {
                    $soundEnabled = Settings::get('reservation.sound_on_notification', false) ? '1' : '0';
                    if ($soundEnabled !== '1') {
                        return new HtmlString('');
                    }

                    return new HtmlString('<script src="/js/filament-notification-sound.js" defer></script>');
                }
            )
            ->colors([
                'primary' => [
                    50 => '#ECFDF5',
                    100 => '#D1FAE5',
                    200 => '#A7F3D0',
                    300 => '#6EE7B7',
                    400 => '#34D399',
                    500 => '#1B5E42',
                    600 => '#134731',
                    700 => '#0F3B29',
                    800 => '#0C2F21',
                    900 => '#092419',
                    950 => '#04140D',
                ],
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
            ])
            ->navigationGroups([
                NavigationGroup::make('Operasional Reservasi')->collapsible(),
                NavigationGroup::make('Laporan & Keuangan')->collapsible(),
                NavigationGroup::make('Katalog & Menu')->collapsible(),
                NavigationGroup::make('Aturan & Kapasitas')->collapsible(),
                NavigationGroup::make('Konten Editorial')->collapsible(),
                NavigationGroup::make('Galeri, Testimoni & FAQ')->collapsible(),
                NavigationGroup::make('Pengaturan & Keamanan')->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                ReservationStatsWidget::class,
                PlaceholderAuditWidget::class,
                UpcomingReservationsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SecurityHeaders::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnforcePasswordChange::class,
            ]);
    }
}
