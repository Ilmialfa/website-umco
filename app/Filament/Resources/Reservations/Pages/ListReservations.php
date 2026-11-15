<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'verifikasi' => Tab::make('Menunggu Verifikasi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'pending'))
                ->badge(fn (): int => Reservation::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'terkonfirmasi' => Tab::make('Terkonfirmasi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'confirmed'))
                ->badge(fn (): int => Reservation::where('status', 'confirmed')->count())
                ->badgeColor('success'),
            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('date', now()->toDateString()))
                ->badge(fn (): int => Reservation::whereDate('date', now()->toDateString())->whereIn('status', ['pending', 'confirmed'])->count())
                ->badgeColor('info'),
            'selesai_batal' => Tab::make('Selesai / Batal')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', ['completed', 'cancelled']))
                ->badgeColor('gray'),
        ];
    }
}
