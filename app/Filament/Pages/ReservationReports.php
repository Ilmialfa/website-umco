<?php

namespace App\Filament\Pages;

use App\Models\Reservation;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReservationReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan & Keuangan';

    protected static ?string $navigationLabel = 'Laporan & Rekap DP';

    protected static ?string $title = 'Laporan Reservasi & DP';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.reservation-reports';

    public string $startDate = '';

    public string $endDate = '';

    public string $statusFilter = 'all';

    public string $modeFilter = 'all';

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin() ?? false, 403);

        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    /**
     * @return Builder<Reservation>
     */
    public function getReportQuery(): Builder
    {
        return Reservation::query()
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->modeFilter !== 'all', fn ($q) => $q->where('mode', $this->modeFilter))
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');
    }

    /**
     * @return array{total_reservations: int, total_pax: int, total_subtotal: int, total_dp: int, total_remaining: int, records: Collection<int, Reservation>}
     */
    public function getReportDataProperty(): array
    {
        $reservations = $this->getReportQuery()->get();
        $active = $reservations->where('status', '!=', 'cancelled');

        return [
            'total_reservations' => $active->count(),
            'total_pax' => (int) $active->sum('party_size'),
            'total_subtotal' => (int) $active->sum('subtotal'),
            'total_dp' => (int) $active
                ->where('payment_status', 'verified')
                ->sum(static fn (Reservation $r): int => (int) ($r->amount_paid ?? $r->dp_amount)),
            'total_remaining' => (int) $active
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum(static fn (Reservation $r): int => $r->remainingBalance()),
            'records' => $reservations,
        ];
    }

    private function safeCsvCell(mixed $value): string
    {
        $str = (string) ($value ?? '');
        if ($str !== '' && preg_match('/^[=+\-@\t\r]/', $str)) {
            return "'".$str;
        }

        return $str;
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdmin() ?? false, 403);

        $records = $this->getReportQuery()->get();
        $filename = 'rekap_reservasi_umco_'.$this->startDate.'_sampai_'.$this->endDate.'.csv';

        return response()->streamDownload(function () use ($records): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }
            // Add UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Booking',
                'Nama Tamu',
                'WhatsApp',
                'Tanggal',
                'Jam',
                'Jumlah Pax',
                'Tipe Reservasi',
                'Status Meja',
                'Status DP',
                'Total Menu (Rp)',
                'Sudah Masuk (Rp)',
                'Sisa Tagihan (Rp)',
                'Catatan',
            ]);

            foreach ($records as $r) {
                $paid = $r->payment_status === 'verified' ? (int) ($r->amount_paid ?? 0) : 0;
                fputcsv($handle, [
                    $this->safeCsvCell($r->code),
                    $this->safeCsvCell($r->name),
                    $this->safeCsvCell($r->phone_wa),
                    $r->date->format('Y-m-d'),
                    $r->time.' WIB',
                    $r->party_size,
                    $r->mode === 'table_menu' ? 'Meja + Menu' : 'Meja Saja',
                    $this->safeCsvCell($r->status_label),
                    $this->safeCsvCell($r->payment_status_label),
                    $r->subtotal,
                    $paid,
                    $r->remainingBalance(),
                    $this->safeCsvCell($r->note ?? ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
