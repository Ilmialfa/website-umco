<?php

namespace App\Filament\Widgets;

use App\Models\MenuItem;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $todayCount = Reservation::whereDate('date', $today)->count();
        $pendingCount = Reservation::where('status', 'pending')->count();
        $totalDpThisMonth = Reservation::whereMonth('date', now()->month)
            ->whereIn('payment_status', ['verified', 'proof_uploaded'])
            ->sum('dp_amount');
        $activeMenuCount = MenuItem::where('is_active', true)->where('is_available', true)->count();

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
                ->description('Dari reservasi Mode B terverifikasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Menu Aktif & Tersedia', "{$activeMenuCount} Item")
                ->description('Siap dipesan pengunjung')
                ->descriptionIcon('heroicon-m-cake')
                ->color('info'),
        ];
    }
}
