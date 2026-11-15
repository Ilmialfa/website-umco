<?php

namespace App\Providers\Filament;

use App\Support\Settings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->login()
            ->brandName('UMCO')
            ->favicon('/favicon.svg')
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('<link rel="stylesheet" href="/css/filament-theme.css">')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                function (): HtmlString {
                    $soundEnabled = Settings::get('reservation.sound_on_notification', false) ? '1' : '0';
                    if ($soundEnabled !== '1') {
                        return new HtmlString('');
                    }
                    return new HtmlString("<script>
(function(){
    let lastCount = null;
    const checkBell = () => {
        if (document.visibilityState !== 'visible') return;
        const badge = document.querySelector('[aria-label*=\"Notifikasi\"], .fi-topbar-database-notifications-btn .fi-badge');
        const txt = badge ? badge.textContent.trim() : '';
        const count = txt ? parseInt(txt, 10) || 0 : 0;
        if (lastCount !== null && count > lastCount) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
                osc.connect(gain); gain.connect(ctx.destination);
                osc.start(); osc.stop(ctx.currentTime + 0.25);
            } catch (e) {}
        }
        lastCount = count;
    };
    setInterval(checkBell, 30000);
})();
</script>");
                }
            )
            ->colors([
                'primary' => [
                    50 => '#f2f7f4',
                    100 => '#dfece4',
                    200 => '#c0d9ca',
                    300 => '#94bea6',
                    400 => '#619a7c',
                    500 => '#2f6b4f',
                    600 => '#23523c',
                    700 => '#1d4332',
                    800 => '#183629',
                    900 => '#142c22',
                    950 => '#171c19',
                ],
                'gray' => Color::Stone,
            ])
            ->navigationGroups([
                'Operasional Reservasi',
                'Katalog & Menu',
                'Konten Website',
                'Pengaturan & Audit',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Widgets\ReservationStatsWidget::class,
                \App\Filament\Widgets\PlaceholderAuditWidget::class,
                \App\Filament\Widgets\UpcomingReservationsWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
