<?php

namespace App\Models;

use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'phone_input',
        'phone_wa',
        'date',
        'time',
        'party_size',
        'note',
        'mode',
        'status',
        'payment_status',
        'subtotal',
        'dp_amount',
        'payment_proof_path',
        'admin_note',
        'confirmed_at',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'party_size' => 'integer',
        'subtotal' => 'integer',
        'dp_amount' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($reservation) {
            if (empty($reservation->code)) {
                $reservation->code = self::generateUniqueCode();
            }

            if (!empty($reservation->phone_input) && empty($reservation->phone_wa)) {
                $reservation->phone_wa = PhoneNumber::normalize($reservation->phone_input);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class, 'reservation_id');
    }

    /**
     * Generate unique reservation code: UMCO-YYMMDD-XXXX (without confusing 0/O, 1/I)
     */
    public static function generateUniqueCode(): string
    {
        $datePrefix = date('ymd');
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $random = '';
            for ($i = 0; $i < 4; $i++) {
                $random .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $code = "UMCO-{$datePrefix}-{$random}";
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDpAttribute(): string
    {
        return 'Rp ' . number_format($this->dp_amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'none' => 'Tanpa DP',
            'waiting_proof' => 'Menunggu Bukti',
            'proof_uploaded' => 'Bukti Diunggah',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => ucfirst($this->payment_status),
        };
    }
}
