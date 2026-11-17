<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class ReservationStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Komposisi Status Reservasi';

    protected ?string $description = 'Distribusi seluruh status pemesanan meja saat ini.';

    protected ?string $maxHeight = '260px';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $counts = Cache::rememberForever('filament.dashboard.charts.mix', static function (): array {
            $rows = Reservation::query()
                ->toBase()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return [
                'pending' => (int) ($rows['pending'] ?? 0),
                'confirmed' => (int) ($rows['confirmed'] ?? 0),
                'completed' => (int) ($rows['completed'] ?? 0),
                'cancelled' => (int) ($rows['cancelled'] ?? 0),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Reservasi',
                    'data' => [
                        $counts['pending'],
                        $counts['confirmed'],
                        $counts['completed'],
                        $counts['cancelled'],
                    ],
                    'backgroundColor' => [
                        '#F59E0B',
                        '#1B5E42',
                        '#0284C7',
                        '#E11D48',
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                'Menunggu Konfirmasi',
                'Dikonfirmasi',
                'Selesai',
                'Dibatalkan',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'cutout' => '68%',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 8,
                        'padding' => 14,
                    ],
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
