<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        // Main Credentials (2 cols)
                        Group::make([
                            Section::make('Kredensial Akun Pengguna')
                                ->description('Informasi login utama untuk staf dan administrator.')
                                ->icon('heroicon-o-user-circle')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Nama Lengkap')
                                                ->required()
                                                ->placeholder('Contoh: Fajar Pratama'),

                                            TextInput::make('email')
                                                ->label('Alamat Email')
                                                ->email()
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->placeholder('email@umco.house'),
                                        ]),

                                    TextInput::make('password')
                                        ->label('Kata Sandi (Password)')
                                        ->password()
                                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                        ->dehydrated(fn ($state) => filled($state))
                                        ->required(fn (string $context): bool => $context === 'create')
                                        ->columnSpanFull()
                                        ->helperText('Kosongkan jika tidak ingin mengubah kata sandi.'),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 2]),

                        // Role & Access Sidebar (1 col)
                        Group::make([
                            Section::make('Hak Akses & Keamanan')
                                ->description('Penentuan peran operasional.')
                                ->icon('heroicon-o-shield-check')
                                ->schema([
                                    Select::make('role')
                                        ->label('Peran / Role')
                                        ->options([
                                            'owner' => 'Owner (Akses Penuh)',
                                            'admin' => 'Admin (Reservasi & Konten)',
                                            'cashier' => 'Kasir (Reservasi & Stok Menu)',
                                        ])
                                        ->default('cashier')
                                        ->disabled(fn ($record): bool => $record !== null && auth()->id() === $record->id)
                                        ->helperText('Role diri sendiri tidak bisa diturunkan langsung untuk mencegah terkunci.')
                                        ->required(),

                                    Toggle::make('must_change_password')
                                        ->label('Wajib Ganti Password')
                                        ->helperText('Minta staf mengganti sandi pada login perdana.')
                                        ->default(false),
                                ]),
                        ])->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
