<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
        /** @var list<array{party_min: int, party_max: ?int, min_spend: int, per_extra_person: ?int}> $tiers */
        $tiers = Cache::rememberForever('umco_spend_tiers', static function (): array {
            return static::query()
                ->orderBy('party_min')
                ->get(['party_min', 'party_max', 'min_spend', 'per_extra_person'])
                ->map(static fn (self $tier): array => [
                    'party_min' => (int) $tier->party_min,
                    'party_max' => $tier->party_max !== null ? (int) $tier->party_max : null,
                    'min_spend' => (int) $tier->min_spend,
                    'per_extra_person' => $tier->per_extra_person !== null ? (int) $tier->per_extra_person : null,
                ])
                ->all();
        });

        if ($tiers === []) {
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
            $withinMax = $tier['party_max'] === null || $partySize <= $tier['party_max'];
            if ($partySize >= $tier['party_min'] && $withinMax) {
                if ($tier['per_extra_person'] !== null && $partySize > $tier['party_min']) {
                    return $tier['min_spend'] + (($partySize - $tier['party_min']) * $tier['per_extra_person']);
                }

                return $tier['min_spend'];
            }
        }

        $highest = $tiers[array_key_last($tiers)];
        if ($partySize > $highest['party_min'] && $highest['per_extra_person']) {
            return $highest['min_spend'] + (($partySize - $highest['party_min']) * $highest['per_extra_person']);
        }

        return $highest['min_spend'];
    }
}
