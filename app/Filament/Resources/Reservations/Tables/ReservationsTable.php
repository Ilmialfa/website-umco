<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Filament\Resources\Reservations\Actions\ReservationActions;
use App\Models\Reservation;
use App\Support\PhoneNumber;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->label('Nama Tamu')
                    ->description(fn (Reservation $record): string => PhoneNumber::format($record->phone_wa))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_wa')
                    ->label('WhatsApp')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => PhoneNumber::format($state))
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date')
                    ->label('Jadwal')
                    ->date('d M Y')
                    ->description(fn (Reservation $record): string => str_replace(':', '.', substr((string) $record->time, 0, 5)) . ' WIB')
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Jam')
                    ->formatStateUsing(fn ($state) => str_replace(':', '.', substr((string) $state, 0, 5)) . ' WIB')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('party_size')
                    ->label('Pax')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('seatingOption.label')
                    ->label('Tempat')
                    ->placeholder('Bebas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source')
                    ->label('Asal')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'walk_in' => 'Datang langsung',
                        'whatsapp' => 'WhatsApp',
                        'phone' => 'Telepon',
                        default => 'Website',
                    })
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
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
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn (Reservation $record) => $record->payment_status_label)
                    ->color(fn ($state) => match ($state) {
                        'waiting_proof' => 'warning',
                        'proof_uploaded' => 'info',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('amount_due')
                    ->label('Tagihan')
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
                    ->label('Status Pembayaran')
                    ->options([
                        'waiting_proof' => 'Menunggu Bukti',
                        'proof_uploaded' => 'Bukti Diunggah',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ]),

                SelectFilter::make('source')
                    ->label('Sumber')
                    ->options([
                        'web' => 'Website',
                        'walk_in' => 'Datang langsung',
                        'whatsapp' => 'WhatsApp',
                        'phone' => 'Telepon',
                    ]),
            ])
            ->recordActions([
                ReservationActions::whatsappGroup(),
                ...ReservationActions::statusActions(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
