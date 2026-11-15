<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneReservationProofs extends Command
{
    protected $signature = 'reservations:prune-proofs';

    protected $description = 'Hapus file bukti pembayaran reservasi lama sesuai pengaturan reservation.retention_days';

    public function handle(): int
    {
        $retentionDays = max(7, (int) Settings::get('reservation.retention_days', 90));
        $cutoff = now()->subDays($retentionDays);

        $reservations = Reservation::query()
            ->whereNotNull('payment_proof_path')
            ->where('payment_proof_path', '!=', '')
            ->where(function ($query) {
                $query->whereIn('status', ['completed', 'cancelled'])
                    ->orWhere('payment_status', 'rejected');
            })
            ->where('updated_at', '<', $cutoff)
            ->get();

        $deletedFiles = 0;

        foreach ($reservations as $reservation) {
            $path = ltrim((string) $reservation->payment_proof_path, '/');

            foreach (['local', 'public'] as $disk) {
                if ($path !== '' && Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                    $deletedFiles++;
                }
            }

            $reservation->payment_proof_path = null;
            $reservation->saveQuietly();
        }

        if ($reservations->isNotEmpty()) {
            ActivityLog::record('reservation.proofs_pruned', null, null, [
                'reservations_cleaned' => $reservations->count(),
                'files_deleted' => $deletedFiles,
                'retention_days' => $retentionDays,
            ]);
        }

        $this->info("Retensi {$retentionDays} hari: {$reservations->count()} reservasi dibersihkan ({$deletedFiles} file dihapus).");

        return self::SUCCESS;
    }
}
