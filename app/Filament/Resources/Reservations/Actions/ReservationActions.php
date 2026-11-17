<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\IndonesianFormat;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Contracts\View\View;

class ReservationActions
{
    public static function viewProofAction(): Action
    {
        return Action::make('view_proof')
            ->label('Lihat bukti')
            ->icon('heroicon-o-photo')
            ->color('info')
            ->visible(fn (Reservation $record): bool => filled($record->payment_proof_path))
            ->modalHeading(fn (Reservation $record): string => 'Bukti Transfer — '.$record->code)
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn (Reservation $record): View => view('filament.components.payment-proof-viewer', [
                'proofPath' => $record->payment_proof_path,
                'code' => $record->code,
                'amountDueText' => IndonesianFormat::rupiah((int) $record->amount_due),
                'paymentStatusLabel' => $record->payment_status_label,
                'isModal' => true,
            ]));
    }

    /**
     * @return array<int, Action>
     */
    public static function whatsappActions(bool $prefixed = false): array
    {
        $prefix = $prefixed ? 'WA: ' : '';

        return [
            Action::make('wa_confirmation')
                ->label($prefix.'Konfirmasi')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'confirmation'])),

            Action::make('wa_reminder')
                ->label($prefix.'Ingatkan')
                ->icon('heroicon-o-bell-alert')
                ->color('warning')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'reminder'])),

            Action::make('wa_follow_up_proof')
                ->label($prefix.'Follow up bukti bayar')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('info')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'follow_up_proof'])),

            Action::make('wa_underpaid')
                ->label($prefix.'Kurang bayar')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'underpaid'])),

            Action::make('wa_cancellation')
                ->label($prefix.'Batal')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'cancellation'])),

            Action::make('wa_free')
                ->label($prefix.'Chat bebas')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'free'])),
        ];
    }

    public static function whatsappGroup(): ActionGroup
    {
        return ActionGroup::make(self::whatsappActions())
            ->label('WhatsApp')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->color('success');
    }

    public static function verifyPaymentAction(): Action
    {
        return Action::make('verify_payment')
            ->label('Verifikasi pembayaran')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
            ->visible(fn (Reservation $record): bool => in_array($record->status, ['pending', 'confirmed'], true) && $record->payment_status !== 'verified')
            ->form([
                Placeholder::make('bill_summary')
                    ->label('Ringkasan Tagihan')
                    ->content(fn (Reservation $record): string => 'Total Menu: '.IndonesianFormat::rupiah((int) $record->subtotal).' · Wajib DP: '.IndonesianFormat::rupiah((int) $record->dp_amount)),
                Select::make('payment_result')
                    ->label('Hasil pengecekan')
                    ->options([
                        'dp_ok' => 'DP diterima (sisa dibayar di kasir saat datang)',
                        'full_ok' => 'Lunas penuh',
                    ])
                    ->default(fn (Reservation $record): string => $record->payment_type === 'full' ? 'full_ok' : 'dp_ok')
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set, Reservation $record): void {
                        if ($state === 'full_ok') {
                            $set('amount_paid', (int) $record->subtotal);
                        } else {
                            $set('amount_paid', (int) ($record->dp_amount ?: $record->amount_due));
                        }
                    })
                    ->required(),
                TextInput::make('amount_paid')
                    ->label('Nominal transfer yang masuk (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(fn (Reservation $record): int => (int) ($record->amount_paid ?: ($record->payment_type === 'full' ? $record->subtotal : $record->amount_due)))
                    ->required(),
            ])
            ->action(function (Reservation $record, array $data, mixed $livewire = null): void {
                $paid = max(0, (int) ($data['amount_paid'] ?? 0));
                $subtotal = (int) $record->subtotal;
                $isFull = ($data['payment_result'] ?? 'dp_ok') === 'full_ok' || ($subtotal > 0 && $paid >= $subtotal);

                $record->update([
                    'status' => 'confirmed',
                    'payment_status' => 'verified',
                    'payment_type' => $isFull ? 'full' : 'dp',
                    'amount_paid' => $paid,
                    'payment_rejected_reason' => null,
                    'confirmed_at' => $record->confirmed_at ?? now(),
                ]);

                ActivityLog::record('reservation.payment_verified', Reservation::class, $record->id, [
                    'code' => $record->code,
                    'result' => $isFull ? 'full_ok' : 'dp_ok',
                    'amount_paid' => $paid,
                    'remaining' => $record->remainingBalance(),
                ]);

                if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('refresh-sidebar');
                }

                $remaining = $record->remainingBalance();
                $body = $remaining > 0
                    ? 'Masuk '.IndonesianFormat::rupiah($paid).' · Sisa pelunasan di kasir '.IndonesianFormat::rupiah($remaining)
                    : 'Lunas '.IndonesianFormat::rupiah($paid);

                Notification::make()
                    ->title('Pembayaran diverifikasi')
                    ->body($body)
                    ->success()
                    ->send();
            });
    }

    public static function settlePaymentAction(): Action
    {
        return Action::make('settle_payment')
            ->label('Pelunasan di kasir')
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
            ->visible(fn (Reservation $record): bool => in_array($record->status, ['confirmed', 'completed'], true) && $record->payment_status === 'verified' && $record->remainingBalance() > 0)
            ->form([
                Placeholder::make('settlement_summary')
                    ->label('Rincian Sebelum Pelunasan')
                    ->content(fn (Reservation $record): string => 'Total Menu Awal: '.IndonesianFormat::rupiah((int) $record->subtotal)
                        .' · Sudah Masuk (DP): '.IndonesianFormat::rupiah((int) ($record->amount_paid ?? 0))
                        .' · Sisa Awal: '.IndonesianFormat::rupiah($record->remainingBalance())),
                TextInput::make('extra_amount')
                    ->label('Tambahan pesanan / ekstra di meja (Rp, isi 0 jika tidak ada)')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, Reservation $record): void {
                        $extra = max(0, (int) ($state ?? 0));
                        $set('settlement_amount', $record->remainingBalance() + $extra);
                    }),
                TextInput::make('settlement_amount')
                    ->label('Nominal pelunasan diterima di kasir (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(fn (Reservation $record): int => $record->remainingBalance())
                    ->required(),
                Select::make('settlement_method')
                    ->label('Metode pembayaran pelunasan')
                    ->options([
                        'cash' => 'Tunai di Kasir',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer Bank',
                    ])
                    ->default('cash')
                    ->required(),
                Textarea::make('extra_note')
                    ->label('Catatan tambahan / menu ekstra (opsional)')
                    ->placeholder('Contoh: Tambah 2 Es Kopi Susu + 1 Kentang Goreng.')
                    ->rows(2),
            ])
            ->action(function (Reservation $record, array $data, mixed $livewire = null): void {
                $extra = max(0, (int) ($data['extra_amount'] ?? 0));
                $settlement = max(0, (int) ($data['settlement_amount'] ?? 0));
                $method = (string) ($data['settlement_method'] ?? 'cash');
                $extraNote = trim((string) ($data['extra_note'] ?? ''));

                $newSubtotal = (int) $record->subtotal + $extra;
                $newPaid = (int) ($record->amount_paid ?? 0) + $settlement;
                $isFull = $newPaid >= $newSubtotal;

                $methodLabel = match ($method) {
                    'qris' => 'QRIS',
                    'transfer' => 'Transfer',
                    default => 'Tunai',
                };
                $logLine = '['.now()->format('d/m H:i').'] Pelunasan '.$methodLabel.' '.IndonesianFormat::rupiah($settlement)
                    .($extra > 0 ? ' (ekstra '.IndonesianFormat::rupiah($extra).')' : '')
                    .($extraNote !== '' ? ' — '.$extraNote : '');
                $adminNote = trim((string) ($record->admin_note ?? ''));
                $adminNote = $adminNote !== '' ? $adminNote."\n".$logLine : $logLine;

                $record->update([
                    'subtotal' => $newSubtotal,
                    'amount_paid' => $newPaid,
                    'payment_type' => $isFull ? 'full' : $record->payment_type,
                    'arrived_at' => $record->arrived_at ?? now(),
                    'admin_note' => $adminNote,
                ]);

                ActivityLog::record('reservation.payment_settled', Reservation::class, $record->id, [
                    'code' => $record->code,
                    'extra_amount' => $extra,
                    'settlement_amount' => $settlement,
                    'total_paid' => $newPaid,
                    'total_bill' => $newSubtotal,
                    'method' => $method,
                ]);

                if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('refresh-sidebar');
                }

                $remainingAfter = $record->remainingBalance();
                $body = $remainingAfter === 0
                    ? 'Total masuk '.IndonesianFormat::rupiah($newPaid).' (Lunas)'
                    : 'Total masuk '.IndonesianFormat::rupiah($newPaid).' · Sisa '.IndonesianFormat::rupiah($remainingAfter);

                Notification::make()
                    ->title('Pelunasan dicatat')
                    ->body($body)
                    ->success()
                    ->send();
            });
    }

    /**
     * @return array<int, Action>
     */
    public static function statusActions(): array
    {
        return [
            self::verifyPaymentAction(),
            self::settlePaymentAction(),

            Action::make('reject_proof')
                ->label('Tolak bukti')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
                ->visible(fn (Reservation $record): bool => $record->status === 'pending' && $record->payment_status !== 'rejected')
                ->form([
                    Textarea::make('payment_rejected_reason')
                        ->label('Alasan ditolak (wajib)')
                        ->placeholder('Contoh: Nominal transfer kurang atau foto buram.')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (Reservation $record, array $data, mixed $livewire = null): void {
                    $reason = trim((string) ($data['payment_rejected_reason'] ?? ''));

                    $record->update([
                        'payment_status' => 'rejected',
                        'payment_rejected_reason' => $reason,
                    ]);

                    ActivityLog::record('reservation.proof_rejected', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'reason' => $reason,
                    ]);

                    if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                        $livewire->dispatch('refresh-sidebar');
                    }

                    Notification::make()
                        ->title('Bukti ditandai ditolak')
                        ->warning()
                        ->send();
                }),

            Action::make('mark_arrived')
                ->label('Tandai datang')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
                ->visible(fn (Reservation $record): bool => $record->status === 'confirmed' && $record->arrived_at === null)
                ->requiresConfirmation()
                ->modalDescription(function (Reservation $record): string {
                    $rem = $record->remainingBalance();
                    if ($rem > 0) {
                        return 'Catatan kasir: Tamu baru membayar DP '.IndonesianFormat::rupiah((int) ($record->amount_paid ?? 0)).'. Sisa pelunasan '.IndonesianFormat::rupiah($rem).' bisa dicatat lewat tombol "Pelunasan di kasir" atau saat "Tandai selesai".';
                    }

                    return 'Pembayaran tamu sudah lunas ('.IndonesianFormat::rupiah((int) ($record->amount_paid ?? 0)).').';
                })
                ->action(function (Reservation $record, mixed $livewire = null): void {
                    $record->update(['arrived_at' => now()]);

                    ActivityLog::record('reservation.arrived', Reservation::class, $record->id, [
                        'code' => $record->code,
                    ]);

                    if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                        $livewire->dispatch('refresh-sidebar');
                    }

                    Notification::make()
                        ->title('Tamu ditandai sudah datang')
                        ->success()
                        ->send();
                }),

            Action::make('mark_completed')
                ->label('Tandai selesai')
                ->icon('heroicon-o-flag')
                ->color('gray')
                ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
                ->visible(fn (Reservation $record): bool => $record->status === 'confirmed')
                ->requiresConfirmation(fn (Reservation $record): bool => $record->remainingBalance() === 0)
                ->modalHeading(fn (Reservation $record): string => $record->remainingBalance() > 0
                    ? 'Pelunasan & Selesaikan Reservasi'
                    : 'Selesaikan Reservasi')
                ->modalDescription(fn (Reservation $record): string => $record->remainingBalance() > 0
                    ? 'Tamu ini baru membayar DP '.IndonesianFormat::rupiah((int) ($record->amount_paid ?? 0)).' dari total '.IndonesianFormat::rupiah((int) $record->subtotal).'. Catat pelunasannya sekaligus sebelum menyelesaikan.'
                    : 'Pembayaran sudah lunas ('.IndonesianFormat::rupiah((int) ($record->amount_paid ?? 0)).'). Tandai reservasi ini selesai?')
                ->form(fn (Reservation $record): array => $record->remainingBalance() > 0 ? [
                    Toggle::make('mark_settled')
                        ->label('Catat pelunasan sisa tagihan saat ini')
                        ->default(true)
                        ->live(),
                    TextInput::make('extra_amount')
                        ->label('Tambahan pesanan di tempat (Rp, isi 0 jika tidak ada)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => (bool) $get('mark_settled'))
                        ->afterStateUpdated(function ($state, Set $set) use ($record): void {
                            $extra = max(0, (int) ($state ?? 0));
                            $set('settlement_amount', $record->remainingBalance() + $extra);
                        }),
                    TextInput::make('settlement_amount')
                        ->label('Nominal pelunasan diterima (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->default($record->remainingBalance())
                        ->visible(fn (Get $get): bool => (bool) $get('mark_settled'))
                        ->required(fn (Get $get): bool => (bool) $get('mark_settled')),
                ] : [])
                ->action(function (Reservation $record, array $data, mixed $livewire = null): void {
                    $update = [
                        'status' => 'completed',
                        'arrived_at' => $record->arrived_at ?? now(),
                    ];

                    if (! empty($data['mark_settled']) && $record->remainingBalance() > 0) {
                        $extra = max(0, (int) ($data['extra_amount'] ?? 0));
                        $settlement = max(0, (int) ($data['settlement_amount'] ?? 0));
                        $newSubtotal = (int) $record->subtotal + $extra;
                        $newPaid = (int) ($record->amount_paid ?? 0) + $settlement;

                        $update['subtotal'] = $newSubtotal;
                        $update['amount_paid'] = $newPaid;
                        if ($newPaid >= $newSubtotal) {
                            $update['payment_type'] = 'full';
                        }

                        $logLine = '['.now()->format('d/m H:i').'] Pelunasan saat selesai '.IndonesianFormat::rupiah($settlement)
                            .($extra > 0 ? ' (ekstra '.IndonesianFormat::rupiah($extra).')' : '');
                        $adminNote = trim((string) ($record->admin_note ?? ''));
                        $update['admin_note'] = $adminNote !== '' ? $adminNote."\n".$logLine : $logLine;
                    }

                    $record->update($update);

                    ActivityLog::record('reservation.completed', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'amount_paid' => $record->amount_paid,
                        'subtotal' => $record->subtotal,
                    ]);

                    if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                        $livewire->dispatch('refresh-sidebar');
                    }

                    Notification::make()
                        ->title('Reservasi selesai')
                        ->success()
                        ->send();
                }),

            Action::make('cancel_reservation')
                ->label('Batalkan')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false)
                ->visible(fn (Reservation $record): bool => in_array($record->status, ['pending', 'confirmed'], true))
                ->form([
                    Textarea::make('cancelled_reason')
                        ->label('Alasan pembatalan (wajib)')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (Reservation $record, array $data, mixed $livewire = null): void {
                    $reason = trim((string) ($data['cancelled_reason'] ?? ''));

                    $record->update([
                        'status' => 'cancelled',
                        'cancelled_reason' => $reason,
                    ]);

                    ActivityLog::record('reservation.cancelled', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'reason' => $reason,
                    ]);

                    if (is_object($livewire) && method_exists($livewire, 'dispatch')) {
                        $livewire->dispatch('refresh-sidebar');
                    }

                    Notification::make()
                        ->title('Reservasi dibatalkan')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
