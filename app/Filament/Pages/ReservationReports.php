<?php

namespace App\Filament\Pages;

use App\Models\Reservation;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
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

    public function getReportQuery()
    {
        return Reservation::query()
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->modeFilter !== 'all', fn ($q) => $q->where('mode', $this->modeFilter))
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');
    }

    public function getReportDataProperty(): array
    {
        $reservations = $this->getReportQuery()->get();

        return [
            'total_reservations' => $reservations->count(),
            'total_pax' => $reservations->sum('party_size'),
            'total_subtotal' => $reservations->sum('subtotal'),
            'total_dp' => $reservations->whereIn('payment_status', ['verified', 'proof_uploaded'])->sum('dp_amount'),
            'records' => $reservations,
        ];
    }

    private function safeCsvCell(mixed $value): string
    {
        $str = (string) ($value ?? '');
        if ($str !== '' && preg_match('/^[=+\-@\t\r]/', $str)) {
            return "'" . $str;
        }

        return $str;
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdmin() ?? false, 403);

        $records = $this->getReportQuery()->get();
        $filename = 'rekap_reservasi_umco_' . $this->startDate . '_sampai_' . $this->endDate . '.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fputs($handle, "\xEF\xBB\xBF");

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
                'DP Amount (Rp)',
                'Catatan',
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $this->safeCsvCell($r->code),
                    $this->safeCsvCell($r->name),
                    $this->safeCsvCell($r->phone_wa),
                    $r->date->format('Y-m-d'),
                    $r->time . ' WIB',
                    $r->party_size,
                    $r->mode === 'table_menu' ? 'Meja + Menu' : 'Meja Saja',
                    $this->safeCsvCell($r->status_label),
                    $this->safeCsvCell($r->payment_status_label),
                    $r->subtotal,
                    $r->dp_amount,
                    $this->safeCsvCell($r->note ?? ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
