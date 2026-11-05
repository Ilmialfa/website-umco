<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Pengguna')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Kosongkan jika tidak ingin mengubah kata sandi.'),

                        Select::make('role')
                            ->label('Peran / Hak Akses')
                            ->options([
                                'owner' => 'Owner (Akses Penuh + Pengaturan + Pengguna)',
                                'admin' => 'Admin (Reservasi, Menu, Konten)',
                                'cashier' => 'Kasir (Reservasi & Status Menu)',
                            ])
                            ->default('cashier')
                            ->required(),

                        Toggle::make('must_change_password')
                            ->label('Wajib Ganti Password pada Login Pertama')
                            ->default(false),
                    ]),
            ]);
    }
}
