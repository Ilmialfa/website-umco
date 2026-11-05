<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingReservationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Reservasi Terdekat & Hari Ini')
            ->description('Daftar tamu yang akan datang dalam waktu dekat. Gunakan tombol aksi cepat WhatsApp untuk konfirmasi.')
            ->query(
                Reservation::query()
                    ->whereDate('date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('date')
                    ->orderBy('time')
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Nama Tamu'),

                TextColumn::make('phone_wa')
                    ->label('WhatsApp')
                    ->formatStateUsing(fn ($state) => PhoneNumber::format($state)),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Jam')
                    ->formatStateUsing(fn ($state) => "{$state} WIB"),

                TextColumn::make('party_size')
                    ->label('Pax')
                    ->alignCenter(),

                TextColumn::make('mode')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'table_menu' ? 'Meja + Menu' : 'Meja Saja'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (Reservation $record) => $record->status_label)
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('confirm_wa')
                    ->label('WA Konfirmasi')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->openUrlInNewTab()
                    ->url(function (Reservation $record) {
                        $template = Settings::get('whatsapp_templates.confirmation', 'Halo Kak {nama}, kami dari Admin UM.CO House mengonfirmasi reservasi Kakak (Kode: {kode}) untuk {jumlah} orang pada {tanggal} pukul {jam} WIB. Ditunggu kehadirannya di UM.CO House ya!');
                        $dateFormatted = date('d/m/Y', strtotime((string) $record->date));
                        $msg = strtr($template, [
                            '{nama}' => $record->name,
                            '{kode}' => $record->code,
                            '{jumlah}' => (string) $record->party_size,
                            '{tanggal}' => $dateFormatted,
                            '{jam}' => $record->time,
                            '{total}' => 'Rp ' . number_format($record->subtotal, 0, ',', '.'),
                            '{dp}' => 'Rp ' . number_format($record->dp_amount, 0, ',', '.'),
                            '{catatan}' => $record->note ?? '-',
                        ]);

                        ActivityLog::record('whatsapp_click', Reservation::class, $record->id, [
                            'template' => 'confirmation',
                            'phone' => $record->phone_wa,
                        ]);

                        return PhoneNumber::waLink($record->phone_wa, $msg);
                    }),

                Action::make('mark_confirmed')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn (Reservation $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);

                        ActivityLog::record('status_change', Reservation::class, $record->id, [
                            'new_status' => 'confirmed',
                        ]);

                        Notification::make()
                            ->title('Reservasi Dikonfirmasi')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
