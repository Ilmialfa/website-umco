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
            ->components([
                Section::make('Rincian Reservasi Meja')
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
                            ->formatStateUsing(fn ($state) => "{$state} WIB"),
                        TextEntry::make('party_size')
                            ->label('Jumlah Kursi (Pax)')
                            ->formatStateUsing(fn ($state) => "{$state} Orang"),
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
                        TextEntry::make('mode')
                            ->label('Tipe Reservasi')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'table_menu' ? 'Meja + Menu' : 'Meja Saja'),
                        TextEntry::make('confirmed_at')
                            ->label('Waktu Konfirmasi')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Belum dikonfirmasi'),
                        TextEntry::make('note')
                            ->label('Catatan Tamu')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada catatan khusus.'),
                    ]),

                Section::make('Rincian Pesanan Menu & Pembayaran DP')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Estimasi Total Menu')
                            ->money('IDR'),
                        TextEntry::make('dp_amount')
                            ->label('Kewajiban Down Payment (DP)')
                            ->money('IDR'),
                        TextEntry::make('payment_status')
                            ->label('Status Pembayaran DP')
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
                        ImageEntry::make('payment_proof_path')
                            ->label('Pratinjau Bukti Transfer')
                            ->disk('public')
                            ->placeholder('Belum ada bukti transfer diunggah.'),

                        RepeatableEntry::make('items')
                            ->label('Daftar Menu yang Dipesan')
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name_snapshot')
                                    ->label('Menu'),
                                TextEntry::make('price_snapshot')
                                    ->label('Harga')
                                    ->money('IDR'),
                                TextEntry::make('qty')
                                    ->label('Qty'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Catatan Internal Staf')
                    ->schema([
                        TextEntry::make('admin_note')
                            ->label('Catatan Staf')
                            ->placeholder('Belum ada catatan internal.'),
                    ]),
            ]);
    }
}
