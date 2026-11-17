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
        $today = now()->toDateString();
        $row = Reservation::query()
            ->toBase()
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count")
            ->selectRaw("SUM(CASE WHEN date = ? AND status IN ('pending', 'confirmed') THEN 1 ELSE 0 END) as today_count", [$today])
            ->first();

        $counts = [
            'pending' => $row !== null ? (int) ($row->pending_count ?? 0) : 0,
            'confirmed' => $row !== null ? (int) ($row->confirmed_count ?? 0) : 0,
            'today' => $row !== null ? (int) ($row->today_count ?? 0) : 0,
        ];

        return [
            'semua' => Tab::make('Semua'),
            'verifikasi' => Tab::make('Menunggu Verifikasi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'pending'))
                ->badge($counts['pending'])
                ->badgeColor('warning'),
            'terkonfirmasi' => Tab::make('Terkonfirmasi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'confirmed'))
                ->badge($counts['confirmed'])
                ->badgeColor('success'),
            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('date', now()->toDateString()))
                ->badge($counts['today'])
                ->badgeColor('info'),
            'selesai_batal' => Tab::make('Selesai / Batal')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', ['completed', 'cancelled']))
                ->badgeColor('gray'),
        ];
    }
}
