<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Support\PhoneNumber;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingReservationsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Reservasi Terdekat & Hari Ini')
            ->description('Daftar tamu yang akan datang dalam waktu dekat. Gunakan tombol aksi cepat WhatsApp untuk konfirmasi.')
            ->query(
                Reservation::query()
                    ->with(['seatingOption'])
                    ->whereDate('date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('date')
                    ->orderBy('time')
                    ->limit(8)
            )
            ->headerActions([
                Action::make('view_all')
                    ->label('Lihat Semua')
                    ->icon('heroicon-o-arrow-right')
                    ->color('gray')
                    ->url(ReservationResource::getUrl('index')),
            ])
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Tamu')
                    ->description(fn (Reservation $record) => PhoneNumber::format($record->phone_wa)),

                TextColumn::make('date')
                    ->label('Jadwal')
                    ->date('d M Y')
                    ->description(fn (Reservation $record) => str_replace(':', '.', substr((string) $record->time, 0, 5)).' WIB')
                    ->sortable(),

                TextColumn::make('party_size')
                    ->label('Pax / Tipe')
                    ->formatStateUsing(fn (Reservation $record) => $record->party_size.' org · '.($record->mode === 'table_menu' ? 'Meja + Menu' : 'Meja')),

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
                    ->label('WA')
                    ->tooltip('Kirim konfirmasi WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->openUrlInNewTab()
                    ->url(fn (Reservation $record): string => route('admin.wa.redirect', ['reservation' => $record->id, 'template' => 'confirmation'])),

                Action::make('mark_confirmed')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->authorize(fn (): bool => in_array(auth()->user()?->role, ['owner', 'admin', 'cashier'], true))
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
            ])
            ->emptyStateHeading('Belum ada jadwal mendatang')
            ->emptyStateDescription('Semua reservasi yang dikonfirmasi atau menunggu verifikasi untuk hari ini dan mendatang akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
