<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Reservasi')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('phone_input')
                            ->label('Nomor Telepon / WA (Input)')
                            ->required(),
                        TextInput::make('phone_wa')
                            ->label('Nomor WhatsApp Normal')
                            ->required()
                            ->helperText('Otomatis dinormalisasi berawalan 628...'),
                    ]),

                Section::make('Jadwal & Tempat')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal Kedatangan')
                            ->required(),
                        TextInput::make('time')
                            ->label('Jam Kedatangan')
                            ->placeholder('14:00')
                            ->helperText('Contoh: 14:00 (WIB)')
                            ->required(),
                        TextInput::make('party_size')
                            ->label('Jumlah Orang (Pax)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Select::make('mode')
                            ->label('Tipe Reservasi')
                            ->options([
                                'table_only' => 'Hanya Meja (Tanpa DP)',
                                'table_menu' => 'Meja + Pesan Menu (Wajib DP)',
                            ])
                            ->required(),
                        Select::make('status')
                            ->label('Status Reservasi')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'confirmed' => 'Dikonfirmasi',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan (Lepas Kursi)',
                            ])
                            ->required(),
                        DateTimePicker::make('confirmed_at')
                            ->label('Waktu Konfirmasi'),
                        Textarea::make('note')
                            ->label('Catatan dari Pelanggan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Keuangan & Down Payment (DP)')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Estimasi Total Pesanan')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0),
                        TextInput::make('dp_amount')
                            ->label('Nominal DP')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0),
                        Select::make('payment_status')
                            ->label('Status Pembayaran DP')
                            ->options([
                                'none' => 'Tanpa DP',
                                'waiting_proof' => 'Menunggu Bukti Transfer',
                                'proof_uploaded' => 'Bukti Telah Diunggah',
                                'verified' => 'Terverifikasi (DP Sah)',
                                'rejected' => 'Ditolak',
                            ])
                            ->required(),
                        FileUpload::make('payment_proof_path')
                            ->label('File Bukti Transfer')
                            ->disk('public')
                            ->directory('payment_proofs')
                            ->image()
                            ->openable()
                            ->downloadable(),
                    ]),

                Section::make('Catatan Internal Kasir / Staf')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Catatan Tambahan')
                            ->placeholder('Contoh: Pelanggan minta meja disatukan di pojok indoor dekat colokan.')
                            ->rows(3),
                    ]),
            ]);
    }
}
