<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpendTier extends Model
{
    protected $fillable = [
        'party_min',
        'party_max',
        'min_spend',
        'per_extra_person',
    ];

    protected $casts = [
        'party_min' => 'integer',
        'party_max' => 'integer',
        'min_spend' => 'integer',
        'per_extra_person' => 'integer',
    ];

    /**
     * Hitung minimal belanja (rupiah) berdasarkan jumlah orang.
     */
    public static function minSpendFor(int $partySize): int
    {
        $tiers = static::orderBy('party_min')->get();

        if ($tiers->isEmpty()) {
            // Default sesuai aturan 11.1 bila tabel belum diisi
            if ($partySize <= 2) {
                return 50000;
            }
            if ($partySize === 3) {
                return 100000;
            }
            if ($partySize === 4) {
                return 125000;
            }
            if ($partySize === 5) {
                return 150000;
            }

            return 150000 + (($partySize - 5) * 25000);
        }

        foreach ($tiers as $tier) {
            $withinMax = $tier->party_max === null || $partySize <= $tier->party_max;
            if ($partySize >= $tier->party_min && $withinMax) {
                if ($tier->per_extra_person !== null && $partySize > $tier->party_min) {
                    return $tier->min_spend + (($partySize - $tier->party_min) * $tier->per_extra_person);
                }

                return $tier->min_spend;
            }
        }

        $highest = $tiers->last();
        if ($partySize > $highest->party_min && $highest->per_extra_person) {
            return $highest->min_spend + (($partySize - $highest->party_min) * $highest->per_extra_person);
        }

        return $highest->min_spend;
    }
}
