<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\ActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Pengguna / Staf')
                    ->placeholder('Sistem Otomatis')
                    ->searchable(),

                TextColumn::make('action')
                    ->label('Jenis Aksi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'whatsapp_click' => 'Klik Chat WhatsApp',
                        'status_change' => 'Ubah Status Reservasi',
                        'price_change' => 'Ubah Harga Menu',
                        'setting_update' => 'Ubah Pengaturan',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'whatsapp_click' => 'success',
                        'status_change' => 'info',
                        'price_change' => 'warning',
                        'setting_update' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('details')
                    ->label('Detail Perubahan / Parameter')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE) : (string) $state)
                    ->wrap(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'whatsapp_click' => 'Klik Chat WhatsApp',
                        'status_change' => 'Ubah Status Reservasi',
                        'price_change' => 'Ubah Harga Menu',
                        'setting_update' => 'Ubah Pengaturan',
                    ]),
            ]);
    }
}
