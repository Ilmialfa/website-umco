<?php

namespace App\Filament\Widgets;

use App\Models\MenuItem;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ReservationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $metrics = Cache::remember('filament.dashboard.stats', 15, function (): array {
            $today = now()->toDateString();
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();

            $resRow = Reservation::query()
                ->selectRaw(
                    "SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) as today_count,
                     SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                     SUM(CASE WHEN date BETWEEN ? AND ? AND payment_status IN ('verified', 'proof_uploaded') THEN dp_amount ELSE 0 END) as month_dp",
                    [$today, $monthStart, $monthEnd]
                )
                ->first();

            $activeMenuCount = MenuItem::where('is_active', true)->where('is_available', true)->count();

            return [
                'today_count' => (int) ($resRow?->today_count ?? 0),
                'pending_count' => (int) ($resRow?->pending_count ?? 0),
                'month_dp' => (int) ($resRow?->month_dp ?? 0),
                'active_menu' => (int) $activeMenuCount,
            ];
        });

        $todayCount = $metrics['today_count'];
        $pendingCount = $metrics['pending_count'];
        $totalDpThisMonth = $metrics['month_dp'];
        $activeMenuCount = $metrics['active_menu'];

        return [
            Stat::make('Reservasi Hari Ini', "{$todayCount} Meja")
                ->description(now()->translatedFormat('l, d F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Menunggu Konfirmasi', "{$pendingCount} Reservasi")
                ->description($pendingCount > 0 ? 'Perlu tindakan kasir' : 'Semua telah ditangani')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            Stat::make('Total DP Masuk (Bulan Ini)', 'Rp ' . number_format($totalDpThisMonth, 0, ',', '.'))
                ->description('Dari pembayaran DP/Lunas terverifikasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Menu Aktif & Tersedia', "{$activeMenuCount} Item")
                ->description('Siap dipesan pengunjung')
                ->descriptionIcon('heroicon-m-cake')
                ->color('info'),
        ];
    }
}
