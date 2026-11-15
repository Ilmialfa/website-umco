# Website & Backoffice UM.CO House

Aplikasi web resmi dan sistem reservasi meja + pre-order menu untuk **UM.CO House** (Kampus 2 UMRI, Pekanbaru).

Dibangun dengan **Laravel 13**, **Filament 4**, **Livewire 4**, **Tailwind CSS 4**, **Vite**, dan **GSAP + Lenis**.

---

## Fitur Utama

- **Halaman Publik Minimalis & Cepat:** Beranda (`/`), Katalog Menu interaktif + Nota Sementara (`/menu`, `/menu/{slug}`), Galeri Suasana (`/galeri`), Cerita & Fasilitas (`/cerita`), dan Sitemap XML otomatis (`/sitemap.xml`).
- **Sistem Reservasi Meja 5 Langkah (`/reservasi`) & Karcis Status (`/reservasi/{code}`):** Cek kapasitas slot per jam dengan penguncian transaksi database (`lockForUpdate`), aturan minimal belanja per jumlah tamu, perhitungan DP 50% / Lunas, unggah bukti transfer / QRIS, ekspor kalender `.ics`, serta notifikasi Lapis 1 (tautan WhatsApp siap kirim) dan Lapis 2 (opsional Fonnte / Wablas API).
- **Backoffice Terproteksi Role (`/admin`):** Tema putih bersih berakses cepat, sidebar dapat diciutkan, dasbor statistik 7 hari, audit log aktivitas keamanan, serta seluruh teks, foto, FAQ, ulasan, dan pengaturan operasional dapat diubah langsung oleh Owner.

---

## Persyaratan & Instalasi Lokal

- PHP >= 8.3 (disarankan 8.4) dengan ekstensi `pdo_mysql`, `gd`, `mbstring`, `intl`, `exif`
- Composer 2.7+
- Node.js 20+ & NPM
- MySQL 8+ (atau MariaDB 10.6+)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --class=Redesign2026Seeder
php artisan storage:link
npm install
npm run build
```

Untuk lingkungan produksi lengkap (Nginx, cPanel, Cron Scheduler, Queue Worker, dan langkah ganti akun demo), ikuti [DEPLOY.md](DEPLOY.md).

---

## Membuat Akun Owner Produksi

Jangan memakai akun contoh `password123` di server publik. Buat akun Owner baru atau perbarui sandi via terminal:

```bash
php artisan umco:create-owner --name="Owner UMCO" --email="owner@domain.id"
```

Atau langsung menyertakan `--force-change` agar sistem memaksa pengguna mengganti sandi pada login pertama di `/admin/profile`.

---

## Matriks Hak Akses Role Staf (`/admin`)

| Fitur / Resource | Owner | Admin | Kasir |
|---|---|---|---|
| Dasbor & Statistik Reservasi | Penuh | Penuh | Penuh |
| Kelola Daftar Reservasi (Konfirmasi/Tolak DP/Selesai) | Penuh + Hapus | Baca/Ubah | Baca/Ubah |
| Ekspor Laporan & Rekap DP (`/admin/reservation-reports`) | Penuh | Penuh | Tidak ada akses |
| Jadwal Libur Khusus (`ClosedDate`) | Penuh | Penuh | Tidak ada akses |
| Katalog Menu (`MenuItem`) | Penuh | Penuh | Lihat saja |
| Kategori Menu (`MenuCategory`) | Penuh | Penuh | Tidak ada akses |
| Konten Website (Banner, Section, Galeri, Ulasan, FAQ, Cerita) | Penuh | Tidak ada akses | Tidak ada akses |
| Aturan Reservasi, Minimal Belanja & Area Duduk | Penuh | Tidak ada akses | Tidak ada akses |
| Pengaturan Website & CMS (`ManageSettings`) | Penuh | Tidak ada akses | Tidak ada akses |
| Manajemen Pengguna & Staf (`User`) | Penuh | Tidak ada akses | Tidak ada akses |
| Log Aktivitas & Keamanan (`ActivityLog`) | Baca | Tidak ada akses | Tidak ada akses |

---

## Panduan Singkat Edit Konten di Backoffice

1. **Teks, Alamat, Jam Buka, QRIS, WhatsApp & SEO:** Masuk sebagai Owner &rarr; **Pengaturan & Keamanan &rarr; Pengaturan Website & CMS** (`/admin/manage-settings`). Tersedia tab terpisah (Brand, Kontak, Jam & Rating, Aturan Reservasi, Pembayaran & QRIS, WhatsApp, dan SEO).
2. **Judul Section & Urutan Beranda / Cerita:** Buka **Konten Editorial &rarr; Section & Urutan** (`/admin/page-sections`).
3. **Menu & Foto Hidangan:** Buka **Katalog & Menu &rarr; Katalog Menu**. Unggah foto rasio `1:1` atau `4:5` (otomatis diproses menjadi WebP responsif 480 / 800 / 1200 px).
4. **Sinkronisasi Instagram Manual:**
   ```bash
   php artisan instagram:sync --force
   ```

---

## Pengujian & Verifikasi Kualitas

```bash
php vendor/bin/pest
php vendor/bin/pint --test
php vendor/bin/phpstan analyse --memory-limit=1G
```
