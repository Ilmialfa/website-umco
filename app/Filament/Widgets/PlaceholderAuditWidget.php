<?php

namespace App\Filament\Widgets;

use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Support\Settings;
use Filament\Widgets\Widget;

class PlaceholderAuditWidget extends Widget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.widgets.placeholder-audit-widget';

    public function getViewData(): array
    {
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
                'description' => 'Rekening transfer DP belum diisi nomor rekening riil. Pelanggan Mode B akan diarahkan menghubungi WA admin.',
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

        // 4. Menu Items without photo
        $menusWithoutPhoto = MenuItem::whereNull('image_path')->where('is_active', true)->count();
        if ($menusWithoutPhoto > 0) {
            $items[] = [
                'type' => 'info',
                'title' => "{$menusWithoutPhoto} Menu Belum Memiliki Foto Produk Asli",
                'description' => 'Menu tetap tampil rapi dengan placeholder estetik, namun foto sajian asli UMCO akan meningkatkan konversi pemesanan.',
                'action_url' => url('/admin/menu-items'),
                'action_label' => 'Unggah Foto Menu',
            ];
        }

        return [
            'placeholderItems' => $items,
        ];
    }
}
