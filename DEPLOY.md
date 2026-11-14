# Panduan Deployment & Scheduler UMCO

## 1. Penyimpanan Publik
Pastikan symlink penyimpanan aktif agar foto menu, bukti bayar, QRIS, dan denah dapat diakses:
```bash
php artisan storage:link
```

## 2. Pengingat Otomatis 15 Menit Sebelum Datang (`reservations:remind`)
Aplikasi menjadwalkan command `reservations:remind` setiap menit (`routes/console.php`) untuk mengirim notifikasi lonceng ke kasir/admin/owner:
- **Lokal (development):** jalankan worker scheduler di terminal terpisah:
  ```bash
  php artisan schedule:work
  ```
- **Produksi (VPS / Shared Hosting Cron):** pasang cron job setiap menit:
  ```cron
  * * * * * cd /path/to/website-umco && php artisan schedule:run >> /dev/null 2>&1
  ```

## 3. Antrean (Queue) Notifikasi WhatsApp Gateway
Jika gateway WhatsApp pihak ketiga (`fonnte` atau `wablas`) diaktifkan di Pengaturan Owner, pastikan queue worker juga berjalan:
```bash
php artisan queue:work --tries=3
```
Bila token gateway dikosongkan, sistem otomatis memakai driver `log` tanpa error dan tombol WhatsApp Lapis 1 tetap berjalan normal.
