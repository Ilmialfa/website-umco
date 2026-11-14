<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\IndonesianFormat;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ReservationActions
{
    public static function whatsappGroup(): ActionGroup
    {
        return ActionGroup::make([
            Action::make('wa_confirmation')
                ->label('Konfirmasi')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'confirmation'])),

            Action::make('wa_reminder')
                ->label('Ingatkan')
                ->icon('heroicon-o-bell-alert')
                ->color('warning')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'reminder'])),

            Action::make('wa_follow_up_proof')
                ->label('Follow up bukti bayar')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('info')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'follow_up_proof'])),

            Action::make('wa_underpaid')
                ->label('Kurang bayar')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'underpaid'])),

            Action::make('wa_cancellation')
                ->label('Batal')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'cancellation'])),

            Action::make('wa_free')
                ->label('Chat bebas')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'free'])),
        ])
            ->label('WhatsApp')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->color('success');
    }

    /**
     * @return array<int, Action>
     */
    public static function statusActions(): array
    {
        return [
            Action::make('verify_payment')
                ->label('Verifikasi pembayaran')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Reservation $record): bool => in_array($record->status, ['pending', 'confirmed'], true) && $record->payment_status !== 'verified')
                ->form([
                    Select::make('payment_result')
                        ->label('Hasil pengecekan')
                        ->options([
                            'dp_ok' => 'DP diterima',
                            'full_ok' => 'Lunas',
                        ])
                        ->default(fn (Reservation $record): string => $record->payment_type === 'full' ? 'full_ok' : 'dp_ok')
                        ->required(),
                    TextInput::make('amount_paid')
                        ->label('Nominal yang masuk (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(fn (Reservation $record): int => (int) ($record->amount_paid ?: $record->amount_due))
                        ->required(),
                ])
                ->action(function (Reservation $record, array $data): void {
                    $isFull = ($data['payment_result'] ?? 'dp_ok') === 'full_ok';
                    $paid = max(0, (int) ($data['amount_paid'] ?? 0));

                    $record->update([
                        'status' => 'confirmed',
                        'payment_status' => 'verified',
                        'payment_type' => $isFull ? 'full' : $record->payment_type,
                        'amount_paid' => $paid,
                        'payment_rejected_reason' => null,
                        'confirmed_at' => $record->confirmed_at ?? now(),
                    ]);

                    ActivityLog::record('reservation.payment_verified', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'result' => $data['payment_result'],
                        'amount_paid' => $paid,
                    ]);

                    Notification::make()
                        ->title('Pembayaran diverifikasi')
                        ->body("Nominal tercatat " . IndonesianFormat::rupiah($paid))
                        ->success()
                        ->send();
                }),

            Action::make('reject_proof')
                ->label('Tolak bukti')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->visible(fn (Reservation $record): bool => $record->status === 'pending' && $record->payment_status !== 'rejected')
                ->form([
                    Textarea::make('payment_rejected_reason')
                        ->label('Alasan ditolak (wajib)')
                        ->placeholder('Contoh: Nominal transfer kurang atau foto buram.')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (Reservation $record, array $data): void {
                    $reason = trim((string) ($data['payment_rejected_reason'] ?? ''));

                    $record->update([
                        'payment_status' => 'rejected',
                        'payment_rejected_reason' => $reason,
                    ]);

                    ActivityLog::record('reservation.proof_rejected', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'reason' => $reason,
                    ]);

                    Notification::make()
                        ->title('Bukti ditandai ditolak')
                        ->warning()
                        ->send();
                }),

            Action::make('mark_arrived')
                ->label('Tandai datang')
                ->icon('heroicon-o-user-check')
                ->color('info')
                ->visible(fn (Reservation $record): bool => $record->status === 'confirmed' && $record->arrived_at === null)
                ->requiresConfirmation()
                ->action(function (Reservation $record): void {
                    $record->update(['arrived_at' => now()]);

                    ActivityLog::record('reservation.arrived', Reservation::class, $record->id, [
                        'code' => $record->code,
                    ]);

                    Notification::make()
                        ->title('Tamu ditandai sudah datang')
                        ->success()
                        ->send();
                }),

            Action::make('mark_completed')
                ->label('Tandai selesai')
                ->icon('heroicon-o-flag')
                ->color('gray')
                ->visible(fn (Reservation $record): bool => $record->status === 'confirmed')
                ->requiresConfirmation()
                ->action(function (Reservation $record): void {
                    $record->update([
                        'status' => 'completed',
                        'arrived_at' => $record->arrived_at ?? now(),
                    ]);

                    ActivityLog::record('reservation.completed', Reservation::class, $record->id, [
                        'code' => $record->code,
                    ]);

                    Notification::make()
                        ->title('Reservasi selesai')
                        ->success()
                        ->send();
                }),

            Action::make('cancel_reservation')
                ->label('Batalkan')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (Reservation $record): bool => in_array($record->status, ['pending', 'confirmed'], true))
                ->form([
                    Textarea::make('cancelled_reason')
                        ->label('Alasan pembatalan (wajib)')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (Reservation $record, array $data): void {
                    $reason = trim((string) ($data['cancelled_reason'] ?? ''));

                    $record->update([
                        'status' => 'cancelled',
                        'cancelled_reason' => $reason,
                    ]);

                    ActivityLog::record('reservation.cancelled', Reservation::class, $record->id, [
                        'code' => $record->code,
                        'reason' => $reason,
                    ]);

                    Notification::make()
                        ->title('Reservasi dibatalkan')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
