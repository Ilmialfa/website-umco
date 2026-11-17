<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ReservationTrendChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Reservasi & Tamu';

    protected ?string $description = 'Grafik jumlah reservasi dan total tamu per hari.';

    protected ?string $maxHeight = '260px';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    public ?string $filter = '14d';

    protected function getFilters(): ?array
    {
        return [
            '14d' => '14 Hari',
            '30d' => '30 Hari',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $filter = $this->filter === '30d' ? '30d' : '14d';
        $days = $filter === '30d' ? 30 : 14;
        $today = now()->toDateString();

        $payload = Cache::rememberForever("filament.dashboard.charts.{$filter}.{$today}", static function () use ($days): array {
            $start = now()->subDays($days - 1)->startOfDay();
            $end = now()->endOfDay();

            $rows = Reservation::query()
                ->toBase()
                ->selectRaw('date, COUNT(*) as booking_count, COALESCE(SUM(party_size), 0) as guest_count')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where('status', '!=', 'cancelled')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            $labels = [];
            $bookings = [];
            $guests = [];

            for ($i = 0; $i < $days; $i++) {
                $d = $start->copy()->addDays($i);
                $iso = $d->toDateString();
                $row = $rows->get($iso);
                $labels[] = Carbon::parse($iso)->translatedFormat('d M');
                $bookings[] = (int) ($row->booking_count ?? 0);
                $guests[] = (int) ($row->guest_count ?? 0);
            }

            return [
                'labels' => $labels,
                'bookings' => $bookings,
                'guests' => $guests,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Reservasi',
                    'data' => $payload['bookings'],
                    'borderColor' => '#1B5E42',
                    'backgroundColor' => 'rgba(27, 94, 66, 0.10)',
                    'borderWidth' => 2.5,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Total Tamu (Pax)',
                    'data' => $payload['guests'],
                    'borderColor' => '#D97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.08)',
                    'borderWidth' => 2,
                    'pointRadius' => 2.5,
                    'pointHoverRadius' => 4,
                    'fill' => false,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $payload['labels'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 8,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'grid' => [
                        'color' => 'rgba(15, 23, 42, 0.05)',
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
