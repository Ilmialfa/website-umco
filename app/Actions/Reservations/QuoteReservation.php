<?php

namespace App\Actions\Reservations;

use App\Models\MenuItem;
use App\Models\SpendTier;
use App\Support\Settings;

class QuoteReservation
{
    /**
     * Hitung ringkasan harga reservasi sepenuhnya di server.
     *
     * @param  array<int, array<string, mixed>>  $rawItems
     * @return array{
     *     party_size: int,
     *     subtotal: int,
     *     min_spend: int,
     *     shortfall: int,
     *     meets_min_spend: bool,
     *     dp_percentage: int,
     *     dp_amount: int,
     *     amount_due: int,
     *     payment_type: string,
     *     items: array<int, array<string, mixed>>
     * }
     */
    public static function calculate(int $partySize, array $rawItems, string $paymentType = 'dp'): array
    {
        $partySize = max(1, $partySize);

        $menuIds = collect($rawItems)
            ->pluck('menu_item_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $menus = $menuIds->isNotEmpty()
            ? MenuItem::query()
                ->whereIn('id', $menuIds)
                ->where('is_active', true)
                ->where('is_available', true)
                ->get()
                ->keyBy('id')
            : collect();

        $normalizedItems = [];
        $subtotal = 0;

        foreach ($rawItems as $raw) {
            $menuId = (int) ($raw['menu_item_id'] ?? 0);
            $qty = max(0, (int) ($raw['qty'] ?? 0));
            if ($menuId <= 0 || $qty <= 0) {
                continue;
            }

            /** @var MenuItem|null $menu */
            $menu = $menus->get($menuId);
            if (! $menu) {
                continue;
            }

            $variantLabel = isset($raw['variant_label']) ? trim((string) $raw['variant_label']) : null;
            if ($variantLabel === '') {
                $variantLabel = null;
            }

            $temperature = isset($raw['temperature']) ? trim((string) $raw['temperature']) : null;
            if ($temperature !== 'Panas' && $temperature !== 'Es') {
                $temperature = null;
            }

            $unitPrice = (int) $menu->price;
            $variants = is_array($menu->variants) ? $menu->variants : [];

            if ($variantLabel !== null && $variants !== []) {
                foreach ($variants as $variant) {
                    if (
                        is_array($variant)
                        && isset($variant['label'])
                        && mb_strtolower(trim((string) $variant['label'])) === mb_strtolower($variantLabel)
                    ) {
                        $unitPrice = (int) ($variant['price'] ?? $unitPrice);
                        $variantLabel = (string) $variant['label'];
                        break;
                    }
                }
            }

            $subtotal += $unitPrice * $qty;

            $normalizedItems[] = [
                'menu_item_id' => $menu->id,
                'name_snapshot' => $menu->name,
                'variant_label' => $variantLabel,
                'temperature' => $temperature,
                'price_snapshot' => $unitPrice,
                'unit_price' => $unitPrice,
                'qty' => $qty,
            ];
        }

        $minSpend = SpendTier::minSpendFor($partySize);
        $shortfall = max(0, $minSpend - $subtotal);
        $meetsMinSpend = $subtotal >= $minSpend;

        // Aturan 11.1: DP minimal 50%, sistem menolak nilai < 50
        $dpPercentage = max(50, (int) Settings::get('reservation.dp_percentage', 50));

        // Dibulatkan ke atas ke ribuan terdekat
        $dpAmount = $subtotal > 0
            ? (int) (ceil(($subtotal * ($dpPercentage / 100)) / 1000) * 1000)
            : 0;

        $normalizedPaymentType = in_array($paymentType, ['dp', 'full', 'cash'], true) ? $paymentType : 'dp';

        $amountDue = match ($normalizedPaymentType) {
            'full' => $subtotal,
            'cash' => 0,
            default => $dpAmount,
        };

        return [
            'party_size' => $partySize,
            'subtotal' => $subtotal,
            'min_spend' => $minSpend,
            'shortfall' => $shortfall,
            'meets_min_spend' => $meetsMinSpend,
            'dp_percentage' => $dpPercentage,
            'dp_amount' => $dpAmount,
            'amount_due' => $amountDue,
            'payment_type' => $normalizedPaymentType,
            'items' => $normalizedItems,
        ];
    }
}
