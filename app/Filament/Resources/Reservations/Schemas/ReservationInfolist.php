<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use App\Support\PhoneNumber;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Reservasi Meja')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode Booking')
                            ->weight('bold')
                            ->copyable(),
                        TextEntry::make('name')
                            ->label('Nama Tamu'),
                        TextEntry::make('phone_wa')
                            ->label('Nomor WhatsApp')
                            ->formatStateUsing(fn ($state) => PhoneNumber::format($state)),
                        TextEntry::make('date')
                            ->label('Tanggal')
                            ->date('l, d F Y'),
                        TextEntry::make('time')
                            ->label('Jam Kedatangan')
                            ->formatStateUsing(fn ($state) => str_replace(':', '.', substr((string) $state, 0, 5)).' WIB'),
                        TextEntry::make('party_size')
                            ->label('Jumlah Orang')
                            ->formatStateUsing(fn ($state) => "{$state} Orang"),
                        TextEntry::make('seatingOption.label')
                            ->label('Pilihan Area')
                            ->placeholder('Bebas di mana saja'),
                        TextEntry::make('source')
                            ->label('Sumber Reservasi')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'walk_in' => 'Datang langsung',
                                'whatsapp' => 'WhatsApp',
                                'phone' => 'Telepon',
                                default => 'Website',
                            }),
                        TextEntry::make('status')
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
                        TextEntry::make('confirmed_at')
                            ->label('Waktu Konfirmasi')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Belum dikonfirmasi'),
                        TextEntry::make('arrived_at')
                            ->label('Waktu Datang')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Belum datang'),
                        TextEntry::make('seating_note')
                            ->label('Catatan Tempat')
                            ->columnSpanFull()
                            ->placeholder('-'),
                        TextEntry::make('note')
                            ->label('Catatan Tamu')
                            ->columnSpanFull()
                            ->placeholder('-'),
                        TextEntry::make('cancelled_reason')
                            ->label('Alasan Pembatalan')
                            ->columnSpanFull()
                            ->visible(fn (Reservation $record): bool => filled($record->cancelled_reason)),
                    ]),

                Section::make('Rincian Pesanan Menu & Pembayaran')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Total Pesanan')
                            ->money('IDR'),
                        TextEntry::make('min_spend_snapshot')
                            ->label('Minimal Belanja Saat Pesan')
                            ->money('IDR'),
                        TextEntry::make('payment_type')
                            ->label('Skema Bayar')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'full' => 'Lunas',
                                'cash' => 'Tunai kasir',
                                default => 'DP',
                            }),
                        TextEntry::make('amount_due')
                            ->label('Tagihan Awal')
                            ->money('IDR'),
                        TextEntry::make('amount_paid')
                            ->label('Nominal Masuk')
                            ->money('IDR')
                            ->placeholder('Belum dicek'),
                        TextEntry::make('payment_status')
                            ->label('Status Pembayaran')
                            ->badge()
                            ->formatStateUsing(fn (Reservation $record) => $record->payment_status_label)
                            ->color(fn ($state) => match ($state) {
                                'waiting_proof' => 'warning',
                                'proof_uploaded' => 'info',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('payment_rejected_reason')
                            ->label('Alasan Bukti Ditolak')
                            ->columnSpanFull()
                            ->visible(fn (Reservation $record): bool => filled($record->payment_rejected_reason)),
                        ImageEntry::make('payment_proof_path')
                            ->label('Bukti Transfer Pelanggan')
                            ->disk('public')
                            ->columnSpanFull()
                            ->placeholder('Belum ada bukti yang diunggah.'),
                    ]),

                Section::make('Daftar Menu yang Dipesan')
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->columns(5)
                            ->schema([
                                TextEntry::make('name_snapshot')->label('Menu'),
                                TextEntry::make('variant_label')->label('Varian')->placeholder('-'),
                                TextEntry::make('temperature')->label('Suhu')->placeholder('-'),
                                TextEntry::make('qty')->label('Jumlah'),
                                TextEntry::make('price_snapshot')->label('Harga')->money('IDR'),
                            ]),
                    ]),
            ]);
    }
}
