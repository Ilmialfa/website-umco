<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Models\CrowdPattern;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CrowdPatternPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $navigationLabel = 'Pola Jam Ramai (7x16)';

    protected static ?string $title = 'Pola Jam Ramai Kedai (Senin–Minggu)';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.crowd-pattern-page';

    /** @var array<int, array<int, int>> */
    public array $grid = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public function mount(): void
    {
        $rows = CrowdPattern::all();
        for ($d = 1; $d <= 7; $d++) {
            for ($h = 7; $h <= 22; $h++) {
                $this->grid[$d][$h] = 40;
            }
        }
        foreach ($rows as $row) {
            if ($row->day_of_week >= 1 && $row->day_of_week <= 7 && $row->hour >= 7 && $row->hour <= 22) {
                $this->grid[$row->day_of_week][$row->hour] = (int) $row->level;
            }
        }
    }

    public function fillExample(): void
    {
        $weekdayCurve = [
            7 => 12, 8 => 28, 9 => 46, 10 => 62, 11 => 58,
            12 => 72, 13 => 68, 14 => 74, 15 => 86, 16 => 92,
            17 => 88, 18 => 64, 19 => 78, 20 => 84, 21 => 70, 22 => 36,
        ];
        $weekendCurve = [
            7 => 10, 8 => 22, 9 => 38, 10 => 50, 11 => 56,
            12 => 64, 13 => 68, 14 => 72, 15 => 80, 16 => 88,
            17 => 90, 18 => 75, 19 => 94, 20 => 96, 21 => 82, 22 => 45,
        ];

        for ($d = 1; $d <= 7; $d++) {
            $curve = $d >= 6 ? $weekendCurve : $weekdayCurve;
            foreach ($curve as $h => $lvl) {
                $jitter = ($d % 3 - 1) * 4;
                $this->grid[$d][$h] = max(5, min(100, $lvl + $jitter));
            }
        }

        Notification::make()
            ->title('Pola contoh diisikan')
            ->body('Tekan Simpan untuk menerapkan ke website.')
            ->info()
            ->send();
    }

    public function save(): void
    {
        for ($d = 1; $d <= 7; $d++) {
            for ($h = 7; $h <= 22; $h++) {
                $val = max(0, min(100, (int) ($this->grid[$d][$h] ?? 0)));
                CrowdPattern::updateOrCreate(
                    ['day_of_week' => $d, 'hour' => $h],
                    ['level' => $val]
                );
            }
        }

        ActivityLog::record('crowd_pattern.updated', null, null, [
            'updated_by' => auth()->user()?->name,
        ]);

        Notification::make()
            ->title('Pola jam ramai disimpan')
            ->success()
            ->send();
    }

    /**
     * @return array<int, string>
     */
    public static function dayLabels(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }
}
