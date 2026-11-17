<?php

namespace App\Models;

use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone_input',
        'phone_wa',
        'date',
        'time',
        'party_size',
        'note',
        'seating_option_id',
        'seating_note',
        'mode',
        'source',
        'status',
        'payment_status',
        'payment_type',
        'subtotal',
        'min_spend_snapshot',
        'dp_amount',
        'amount_due',
        'amount_paid',
        'payment_proof_path',
        'payment_rejected_reason',
        'admin_note',
        'cancelled_reason',
        'confirmed_at',
        'reminder_notified_at',
        'late_notified_at',
        'arrived_at',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'party_size' => 'integer',
        'subtotal' => 'integer',
        'min_spend_snapshot' => 'integer',
        'dp_amount' => 'integer',
        'amount_due' => 'integer',
        'amount_paid' => 'integer',
        'confirmed_at' => 'datetime',
        'reminder_notified_at' => 'datetime',
        'late_notified_at' => 'datetime',
        'arrived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reservation): void {
            if (empty($reservation->code)) {
                $reservation->code = self::generateUniqueCode();
            }

            if (! empty($reservation->phone_input) && empty($reservation->phone_wa)) {
                $reservation->phone_wa = PhoneNumber::normalize($reservation->phone_input);
            }
        });
    }

    /**
     * @return HasMany<ReservationItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class, 'reservation_id');
    }

    /**
     * @return BelongsTo<SeatingOption, $this>
     */
    public function seatingOption(): BelongsTo
    {
        return $this->belongsTo(SeatingOption::class, 'seating_option_id');
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
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDpAttribute(): string
    {
        return 'Rp '.number_format($this->dp_amount, 0, ',', '.');
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

    public function remainingBalance(): int
    {
        if ($this->status === 'cancelled') {
            return 0;
        }

        return max(0, (int) $this->subtotal - (int) ($this->amount_paid ?? 0));
    }

    public function isFullyPaid(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        if ($this->payment_status === 'none' && (int) $this->subtotal === 0) {
            return true;
        }

        return $this->payment_status === 'verified' && $this->remainingBalance() === 0;
    }
}
