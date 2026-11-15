<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        // Main Board (2 cols)
                        Group::make([
                            Section::make('Informasi Pelanggan')
                                ->description('Identitas tamu yang melakukan pemesanan meja.')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('code')
                                                ->label('Kode Booking')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->helperText('Dihasilkan otomatis oleh sistem.'),

                                            TextInput::make('name')
                                                ->label('Nama Lengkap')
                                                ->required()
                                                ->maxLength(255),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('phone_input')
                                                ->label('Nomor WhatsApp / HP Tamu')
                                                ->required()
                                                ->placeholder('081234567890'),

                                            TextInput::make('phone_wa')
                                                ->label('Format WhatsApp Sistem (628...)')
                                                ->required()
                                                ->helperText('Otomatis dinormalisasi untuk link WhatsApp kasir.'),
                                        ]),
                                ]),

                            Section::make('Jadwal Kedatangan & Meja')
                                ->description('Waktu, kapasitas kursi, dan tipe reservasi.')
                                ->icon('heroicon-o-calendar')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            DatePicker::make('date')
                                                ->label('Tanggal Reservasi')
                                                ->required(),

                                            TextInput::make('time')
                                                ->label('Jam Kedatangan')
                                                ->placeholder('14:00')
                                                ->helperText('Format HH:MM (WIB)')
                                                ->required(),

                                            TextInput::make('party_size')
                                                ->label('Jumlah Tamu (Pax)')
                                                ->numeric()
                                                ->minValue(1)
                                                ->required(),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Select::make('mode')
                                                ->label('Tipe Reservasi')
                                                ->options([
                                                    'table_only' => 'Hanya Meja (Tanpa DP)',
                                                    'table_menu' => 'Meja + Pesan Menu (Wajib DP)',
                                                ])
                                                ->required(),

                                            Select::make('status')
                                                ->label('Status Meja')
                                                ->options([
                                                    'pending' => 'Menunggu Konfirmasi',
                                                    'confirmed' => 'Dikonfirmasi',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan',
                                                ])
                                                ->required(),
                                        ]),

                                    Textarea::make('note')
                                        ->label('Permintaan Khusus dari Tamu')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->placeholder('Contoh: Mau dekat area outdoor, bawa colokan, dsb.'),
                                ]),

                            Section::make('Catatan Internal Kasir / Staf')
                                ->description('Hanya dapat dibaca oleh tim operasional UMCO.')
                                ->icon('heroicon-o-clipboard-document-check')
                                ->schema([
                                    Textarea::make('admin_note')
                                        ->label('Catatan Operasional')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('Contoh: Sudah konfirmasi via WA, minta meja disatukan jam 15.00 WIB.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        // Financial & Verification Sidebar (1 col)
                        Group::make([
                            Section::make('Keuangan & Down Payment (DP)')
                                ->description('Status verifikasi pembayaran DP.')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Select::make('payment_status')
                                        ->label('Status DP')
                                        ->options([
                                            'none' => 'Tanpa DP (Meja Saja)',
                                            'waiting_proof' => 'Menunggu Bukti Transfer',
                                            'proof_uploaded' => 'Bukti Telah Diunggah',
                                            'verified' => 'Terverifikasi (DP Sah)',
                                            'rejected' => 'Bukti Ditolak',
                                        ])
                                        ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                                        ->required(),

                                    TextInput::make('subtotal')
                                        ->label('Estimasi Total Pesanan')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                                        ->default(0),

                                    TextInput::make('dp_amount')
                                        ->label('Nominal DP Diterima')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                                        ->default(0),

                                    FileUpload::make('payment_proof_path')
                                        ->label('Bukti Transfer DP')
                                        ->disk('public')
                                        ->directory('payment_proofs')
                                        ->image()
                                        ->openable()
                                        ->downloadable()
                                        ->helperText('Klik foto untuk memperbesar.'),
                                ]),

                            Section::make('Audit Waktu')
                                ->description('Waktu verifikasi sistem.')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    DateTimePicker::make('confirmed_at')
                                        ->label('Waktu Konfirmasi Terakhir')
                                        ->helperText('Otomatis tercatat saat status diubah ke konfirmasi.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
