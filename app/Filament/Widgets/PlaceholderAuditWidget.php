<?php

namespace App\Filament\Widgets;

use App\Models\MenuItem;
use App\Models\Testimonial;
use App\Support\Settings;
use Filament\Widgets\Widget;

class PlaceholderAuditWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        if (! (auth()->user()?->isOwner() ?? false)) {
            return false;
        }

        return count(self::collectIssues()) > 0;
    }

    protected string $view = 'filament.widgets.placeholder-audit-widget';

    /**
     * @return list<array{type: string, title: string, description: string, action_url: string, action_label: string}>
     */
    private static function collectIssues(): array
    {
        return once(static function (): array {
            $items = [];

            // 1. Bank Accounts check
            $bankAccounts = Settings::get('payment.bank_accounts');
            $hasEmptyBank = false;
            if (is_array($bankAccounts)) {
                foreach ($bankAccounts as $acc) {
                    if (empty($acc['account_number'])) {
                        $hasEmptyBank = true;
                        break;
                    }
                }
            } else {
                $hasEmptyBank = true;
            }

            if ($hasEmptyBank) {
                $items[] = [
                    'type' => 'critical',
                    'title' => 'Nomor Rekening Bank Masih Kosong',
                    'description' => 'Rekening alternatif transfer DP/Lunas belum diisi nomor rekening riil.',
                    'action_url' => url('/admin/manage-settings'),
                    'action_label' => 'Isi Rekening Bank',
                ];
            }

            // 2. QRIS check
            $qris = Settings::get('payment.qris_image_path');
            if (empty($qris)) {
                $items[] = [
                    'type' => 'warning',
                    'title' => 'Gambar QRIS Statis Belum Diunggah',
                    'description' => 'File gambar barcode QRIS resmi UMCO belum diunggah di Pengaturan Pembayaran.',
                    'action_url' => url('/admin/manage-settings'),
                    'action_label' => 'Unggah QRIS',
                ];
            }

            // 3. Testimonial samples
            $sampleTestimonialsCount = Testimonial::where('is_sample', true)->count();
            if ($sampleTestimonialsCount > 0) {
                $items[] = [
                    'type' => 'info',
                    'title' => "{$sampleTestimonialsCount} Ulasan Masih Berstatus Contoh (Placeholder)",
                    'description' => 'Website menampilkan ulasan contoh yang ditandai sampel. Ganti dengan ulasan Google Maps riil setelah mendapat izin pelanggan.',
                    'action_url' => url('/admin/testimonials'),
                    'action_label' => 'Kelola Ulasan',
                ];
            }

            // 4. Placeholder Detector: Menu tanpa foto / berstatus placeholder
            $menusWithoutPhoto = MenuItem::where('is_active', true)
                ->where(function ($q) {
                    $q->where('is_placeholder', true)
                        ->orWhereNull('image_path');
                })
                ->count();
            if ($menusWithoutPhoto > 0) {
                $items[] = [
                    'type' => 'info',
                    'title' => "{$menusWithoutPhoto} Menu Masih Memakai Placeholder Tipografi",
                    'description' => 'Menu tetap rapi dengan placeholder jujur ("foto menyusul"), namun unggah foto sajian asli UMCO akan melengkapi halaman menu.',
                    'action_url' => url('/admin/menu-items'),
                    'action_label' => 'Kelola Foto Menu',
                ];
            }

            return $items;
        });
    }

    public function getViewData(): array
    {
        return [
            'placeholderItems' => self::collectIssues(),
        ];
    }
}
