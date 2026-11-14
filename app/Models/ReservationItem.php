<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'menu_item_id',
        'name_snapshot',
        'variant_label',
        'temperature',
        'price_snapshot',
        'unit_price',
        'qty',
    ];

    protected $casts = [
        'price_snapshot' => 'integer',
        'unit_price' => 'integer',
        'qty' => 'integer',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function getSubtotalAttribute(): int
    {
        return $this->price_snapshot * $this->qty;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_snapshot, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
