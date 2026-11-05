<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_wa')
                    ->label('WhatsApp')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => PhoneNumber::format($state))
                    ->copyable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Jam')
                    ->formatStateUsing(fn ($state) => "{$state} WIB")
                    ->sortable(),

                TextColumn::make('party_size')
                    ->label('Pax')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('mode')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'table_menu' ? 'Meja + Menu' : 'Meja Saja')
                    ->color(fn ($state) => $state === 'table_menu' ? 'info' : 'gray'),

                TextColumn::make('status')
                    ->label('Status Meja')
                    ->badge()
                    ->formatStateUsing(fn (Reservation $record) => $record->status_label)
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Status DP')
                    ->badge()
                    ->formatStateUsing(fn (Reservation $record) => $record->payment_status_label)
                    ->color(fn ($state) => match ($state) {
                        'none' => 'gray',
                        'waiting_proof' => 'warning',
                        'proof_uploaded' => 'info',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('dp_amount')
                    ->label('Nominal DP')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Dikonfirmasi',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran DP')
                    ->options([
                        'none' => 'Tanpa DP (Meja Saja)',
                        'waiting_proof' => 'Menunggu Bukti Transfer',
                        'proof_uploaded' => 'Bukti Telah Diunggah',
                        'verified' => 'DP Terverifikasi',
                        'rejected' => 'Bukti Ditolak',
                    ]),

                SelectFilter::make('mode')
                    ->label('Mode Reservasi')
                    ->options([
                        'table_only' => 'Hanya Meja',
                        'table_menu' => 'Meja + Menu',
                    ]),
            ])
            ->recordActions([
                // Smart WhatsApp Engine Action Group
                ActionGroup::make([
                    Action::make('confirm_wa')
                        ->label('Chat Konfirmasi WA')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->color('success')
                        ->openUrlInNewTab()
                        ->url(function (Reservation $record) {
                            $template = Settings::get('whatsapp_templates.confirmation', 'Halo Kak {nama}, kami dari Admin UM.CO House mengonfirmasi reservasi Kakak (Kode: {kode}) untuk {jumlah} orang pada {tanggal} pukul {jam} WIB. Ditunggu kehadirannya di UM.CO House ya!');
                            $msg = self::renderTemplate($template, $record);

                            return PhoneNumber::waLink($record->phone_wa, $msg);
                        }),

                    Action::make('cancel_wa')
                        ->label('Chat Pembatalan / Menu Kosong')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->openUrlInNewTab()
                        ->url(function (Reservation $record) {
                            $template = Settings::get('whatsapp_templates.cancellation', 'Halo Kak {nama}, mohon maaf dari Admin UM.CO House terkait reservasi {kode} pada {tanggal} {jam} WIB ada penyesuaian jadwal / menu habis. Silakan hubungi kami untuk opsi meja pengganti.');
                            $msg = self::renderTemplate($template, $record);

                            return PhoneNumber::waLink($record->phone_wa, $msg);
                        }),

                    Action::make('custom_wa')
                        ->label('Chat Bebas Langsung')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->openUrlInNewTab()
                        ->url(function (Reservation $record) {
                            return PhoneNumber::waLink($record->phone_wa, '');
                        }),
                ])
                ->label('WhatsApp')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('success'),

                // Quick Status Confirmation
                Action::make('mark_confirmed')
                    ->label('Konfirmasi Meja')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Reservation $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Reservasi Ini?')
                    ->modalDescription('Status reservasi akan diubah menjadi Dikonfirmasi dan kapasitas kursi diamankan.')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);

                        ActivityLog::record('status_change', Reservation::class, $record->id, [
                            'new_status' => 'confirmed',
                        ]);

                        Notification::make()
                            ->title('Reservasi Berhasil Dikonfirmasi')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Helper to render WhatsApp template placeholders.
     */
    protected static function renderTemplate(string $template, Reservation $record): string
    {
        $dateFormatted = date('d/m/Y', strtotime((string) $record->date));

        return strtr($template, [
            '{nama}' => $record->name,
            '{kode}' => $record->code,
            '{jumlah}' => (string) $record->party_size,
            '{tanggal}' => $dateFormatted,
            '{jam}' => $record->time,
            '{total}' => 'Rp ' . number_format($record->subtotal, 0, ',', '.'),
            '{dp}' => 'Rp ' . number_format($record->dp_amount, 0, ',', '.'),
            '{catatan}' => $record->note ?? '-',
        ]);
    }
}
