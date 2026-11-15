# Audit Pra-Deploy — website-umco

Tanggal audit: 15 November 2026 · Commit dasar: `38f99a3` (branch `master`)
Stack: Laravel 13 · PHP 8.4 · Filament 4 · Livewire · Vite (vite-plus) · Tailwind 4 · GSAP + Lenis · MySQL

Dokumen ini adalah **daftar kerja** untuk AI agent yang akan memperbaiki codebase sebelum diserahkan ke klien. Setiap temuan punya ID, lokasi file, bukti, dan kriteria selesai. Kerjakan berurutan dari P0 → P3.

> [!IMPORTANT]
> Status label di bawah:
> - **[Terverifikasi]** = sudah dibuktikan saat audit (perintah/isi file).
> - **[Perlu dicek]** = indikasi kuat, tapi agent wajib memverifikasi dulu sebelum mengubah.

---

## Ringkasan prioritas

| Prioritas | Arti | Jumlah |
|---|---|---|
| **P0** | Wajib sebelum deploy (bug data, keamanan, brand salah) | 8 |
| **P1** | Backoffice: tata letak, keterbacaan, editability | 9 |
| **P2** | Kode mati / spageti / performa | 10 |
| **P3** | Polish, SEO, dokumentasi | 8 |

---

## P0 — Wajib sebelum deploy

### P0-01 · Timezone aplikasi masih `UTC` [Terverifikasi]
- **Lokasi:** `config/app.php` baris 68 → `'timezone' => 'UTC'`.
- **Dampak:** Slot reservasi, "hari ini", tanggal tutup, pengingat WA (`reservations:remind`), laporan DP, dan `today()` di `HomeController` bergeser 7 jam. Sebagian kode memakai `'Asia/Jakarta'` secara manual (mis. `AdminWhatsAppRedirectController.php` baris 24) → logika campur aduk.
- **Perbaikan:** Ubah ke `env('APP_TIMEZONE', 'Asia/Jakarta')`, tambahkan `APP_TIMEZONE=Asia/Jakarta` di `.env.example`. Cari semua `'Asia/Jakarta'` hard-coded dan hapus jika sudah redundan. Jalankan test reservasi.
- **Selesai jika:** `config('app.timezone') === 'Asia/Jakarta'`, test `ReservationEngineTest` & `ReservationReminderTest` lulus.

### P0-02 · Favicon masih huruf "H" (terlihat seperti logo Herd) [Terverifikasi]
- **Lokasi:** `public/favicon.svg` (isi: path huruf H, komentar "H-building"), `public/favicon.ico`, `public/apple-touch-icon.png`; dipakai di `resources/views/components/layouts/public.blade.php` baris 54–55 dan `AdminPanelProvider.php` `->favicon('/favicon.svg')`.
- **Perbaikan:** Buat favicon dari logo klien `docs/logo UMCO.png` (emblem oval hijau, sudah ada versi transparan `public/images/umco-logo.webp`). Hasilkan: `favicon.ico` (16/32/48), `favicon-32.png`, `apple-touch-icon.png` 180×180 (latar hijau `#19523A` atau krem, emblem di tengah, padding ±12%), dan `favicon.svg` (boleh SVG yang membungkus PNG base64 atau ganti `<link>` ke PNG). Perbaiki `<link rel="apple-touch-icon">` yang sekarang menunjuk ke `.svg` (iOS tidak mendukung SVG).
- **Selesai jika:** tab browser publik & `/admin` menampilkan emblem UMCO, bukan "H".

### P0-03 · Data sosial palsu di fallback Instagram [Terverifikasi]
- **Lokasi:** `app/Services/InstagramFeedService.php` `buildCuratedFallback()` baris 224–225 & 244–245: `'likes' => 42 + ($idx * 9)`, `'comments' => 4 + ($idx % 5)`, `'timestamp_human' => ($idx + 1) . ' hari lalu'`.
- **Dampak:** Website menampilkan jumlah like/komentar dan waktu posting yang dikarang. Ini menyesatkan pengunjung.
- **Perbaikan:** Set `likes`, `comments` ke `null` dan `timestamp_human` ke `null` untuk sumber `curated`/`default`; pastikan `s13-instagram.blade.php` menyembunyikan elemen tersebut bila `null`.

### P0-04 · Instagram di-fetch sinkron saat render beranda [Terverifikasi]
- **Lokasi:** `HomeController.php` baris 92 → `app(InstagramFeedService::class)->getFeed(8)`; service memanggil HTTP dengan `timeout(4)` + `retry(1, 200)`, dua sumber berurutan → saat cache kosong/expired, TTFB beranda bisa ±8–10 detik.
- **Tambahan:** `fetchViaPublicProfile()` memakai endpoint tidak resmi `instagram.com/api/v1/users/web_profile_info` dengan header `x-ig-app-id` hard-coded — rapuh dan sering diblokir di IP server/hosting.
- **Perbaikan:** (1) Beranda hanya membaca cache (`Cache::get`) atau fallback kurasi, **tanpa** HTTP. (2) Buat command `instagram:sync` dijadwalkan tiap 15–30 menit di `routes/console.php`. (3) Section `s13-instagram` boleh tetap memanggil `/instagram-feed` via JS untuk refresh. (4) Dokumentasikan di `DEPLOY.md` bahwa sumber resmi adalah Graph API token (field `instagram_access_token` di Pengaturan).

### P0-05 · Fitur "Pola Jam Ramai" tidak tampil di mana pun [Terverifikasi]
- **Bukti:** `CrowdPatternPage` (admin) berkata "Grafik ini tampil di beranda", `HomeController` meng-query `CrowdPattern::all()` (baris 75–78), tetapi tidak ada satu pun view publik yang memakai `$crowdPatterns` dan tidak ada key section di `$partialMap` (`pages/home.blade.php`).
- **KEPUTUSAN KLIEN (final): HAPUS fitur ini.** Jangan menghidupkan kembali grafik di beranda.
- **Perbaikan:**
  - Hapus `app/Filament/Pages/CrowdPatternPage.php` dan `resources/views/filament/pages/crowd-pattern-page.blade.php` beserta gaya `.umco-crowd-*` di tema admin.
  - Hapus query `CrowdPattern` di `HomeController` dan semua referensi lain (`grep -ri crowd app resources routes database tests`), termasuk `PublicContentObserver` registration di `AppServiceProvider`, policy mapping, seeder, factory, dan assertion test (`AdminAccessTest`, `SeedDataTest`, dll.) yang menyebut `CrowdPatternPage`/`CrowdPattern`.
  - Hapus model `app/Models/CrowdPattern.php`. Buat **migrasi baru** `drop_crowd_patterns_table` (dengan `down()` yang membuat ulang tabel) — jangan mengedit migrasi lama.
  - Hapus juga JS/CSS publik yang hanya dipakai grafik tersebut jika ada.
- **Selesai jika:** `grep -ri "crowd"` di `app/`, `resources/`, `routes/`, `tests/`, `database/seeders/` tidak menemukan apa pun (kecuali migrasi lama + migrasi drop baru), menu "Pola Jam Ramai" hilang, test lulus.

### P0-06 · Section terdaftar di DB tapi tidak dirender [Terverifikasi]
- **Bukti:** `page_sections` (page `home`) berisi key `rules` (sort 10, aktif) yang **tidak** ada di `$partialMap` → admin bisa mengeditnya tapi tidak ada efek. Page `cerita` berisi key `altimeter` — cek apakah `pages/cerita.blade.php` memakainya [Perlu dicek].
- **Perbaikan:** Sinkronkan: setiap key di `page_sections` harus punya partial, atau dihapus via migrasi data/seeder. Tambahkan label "Tidak dipakai di halaman" atau validasi di `PageSectionResource` agar admin tidak bingung.

### P0-07 · Akun owner masih `must_change_password = 1` dan kredensial seed lemah [Terverifikasi]
- **Bukti:** `users` → `owner@umco.house` `must_change_password: 1`; seeder memakai sandi `password123` untuk owner/admin/kasir.
- **Perbaikan:** (1) Tambahkan middleware/hook Filament yang **memaksa** pengguna dengan `must_change_password = true` ke halaman `/admin/profile` sampai sandi diganti (listener `User::updating` di `AppServiceProvider` sudah me-reset flag). (2) Seeder produksi tidak boleh membuat akun dengan sandi statis — gunakan command `php artisan umco:create-owner` interaktif atau sandi acak yang dicetak sekali. (3) Tulis di `DEPLOY.md`.

### P0-08 · Template `.env.example` tidak siap produksi [Terverifikasi]
- **Bukti:** `APP_NAME=Laravel`, `APP_LOCALE=en`, `APP_DEBUG=true`, `DB_CONNECTION=sqlite` (lokal memakai MySQL), `MAIL_FROM_ADDRESS="hello@example.com"`, tidak ada `APP_TIMEZONE`, tidak ada variabel WhatsApp gateway.
- **Perbaikan:** Buat `.env.example` yang mencerminkan proyek: `APP_NAME="UM.CO House"`, `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, `APP_TIMEZONE=Asia/Jakarta`, `DB_CONNECTION=mysql` (dengan contoh host/db), komentar untuk produksi (`APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `FILESYSTEM_DISK=public` bila perlu). Default `config/app.php` untuk `name` dan `locale` juga disesuaikan.

---

## P1 — Backoffice (Filament `/admin`)

Keluhan klien: latar krem/stone membuat kusam dan pusing, keterbacaan rendah, sidebar tidak bisa diciutkan, tabel reservasi terpotong (scroll horizontal, kolom `Aksi` tersembunyi).

### Arah desain wajib (design brief)
- **Kanvas putih bersih:** `body`/main `#FFFFFF`; sidebar `#FFFFFF` dengan garis pemisah tipis `#EEF0F2` (boleh 1px, satu-satunya garis), atau abu sangat muda `#F8FAF9`.
- **Teks kontras tinggi:** utama `#0F172A`/`#111827`, sekunder `#475569`; minimal kontras WCAG AA (4.5:1). Ukuran teks tabel 14px, label form 14px semi-bold.
- **Berwarna tapi terkendali (colorful):** gunakan palet status yang konsisten di seluruh panel:
  - Hijau UMCO `primary` (`#1B5E42`) untuk aksi utama & navigasi aktif.
  - `success` emerald (Terkonfirmasi/Lunas), `warning` amber (Menunggu Konfirmasi/Bukti Diunggah), `danger` rose (Dibatalkan/Ditolak/Hangus), `info` sky (Walk-in/Info), `gray` (Selesai/Arsip).
  - Kartu statistik dasbor masing-masing punya ikon ber-latar warna lembut (mis. `bg-emerald-50 text-emerald-700`, `bg-amber-50 text-amber-700`, `bg-sky-50 text-sky-700`, `bg-violet-50 text-violet-700`).
- **Sidebar bisa diciutkan:** `->sidebarCollapsibleOnDesktop()` + `->collapsibleNavigationGroups()`; setiap item **wajib** punya ikon Heroicon yang jelas agar mode ciut tetap terbaca.
- **Lebar konten:** `->maxContentWidth(Width::Full)` (atau `ScreenTwoExtraLarge`) agar tabel tidak sempit.
- **Tanpa** `backdrop-blur`, tanpa bayangan berat; pakai `ring-1 ring-gray-950/5` bawaan Filament.

### P1-01 · Ganti tema `public/css/filament-theme.css` (807 baris) [Terverifikasi]
- **Masalah:** Override global berbasis `!important` (mis. `.fi-body { background-color: var(--umco-canvas) !important; }`, `.fi-sidebar { background-color: var(--umco-surface-alt) !important; }`) di-inject manual lewat `renderHook(HEAD_END)` dan tidak ikut build Vite → sulit dirawat, mudah rusak saat upgrade Filament.
- **Perbaikan:** Buat tema Filament resmi: `php artisan make:filament-theme admin` → `resources/css/filament/admin/theme.css`, daftarkan `->viteTheme('resources/css/filament/admin/theme.css')`, pindahkan hanya gaya yang benar-benar perlu (laporan, pola jam ramai, widget audit). Hapus render hook `<link href="/css/filament-theme.css">` dan file lamanya. Ganti `'gray' => Color::Stone` menjadi `Color::Slate` atau `Color::Zinc` agar netral putih, bukan krem.
- **Selesai jika:** tidak ada `!important` untuk background panel; kanvas putih; `npm run build` menghasilkan aset tema.

### P1-02 · Sidebar & navigasi [Terverifikasi]
- **Lokasi:** `AdminPanelProvider.php` tidak memiliki `sidebarCollapsibleOnDesktop()`.
- **Perbaikan:** Tambahkan `->sidebarCollapsibleOnDesktop()`, `->sidebarWidth('17rem')`, `->collapsedSidebarWidth('4.5rem')`, `->brandLogo(asset('images/umco-logo.webp'))` + `->brandLogoHeight('2.25rem')` + `->darkModeBrandLogo(...)` bila dark mode dipakai (atau `->darkMode(false)` untuk konsistensi). Brand teks sekarang hanya "UMCO" polos.
- **Rapikan grup** (saat ini ±20 item dalam 4 grup sangat panjang): usulan
  1. **Hari Ini** — Dasbor, Daftar Reservasi (badge jumlah menunggu), Jadwal Libur Khusus
  2. **Menu** — Katalog Menu, Kategori Menu
  3. **Konten Beranda** — Section & Urutan, Banner, Galeri, Ulasan, FAQ
  4. **Konten Cerita** — Narasi Editorial, Fasilitas, Stan Street Food
  5. **Aturan Reservasi** — Aturan Main, Tabel Minimal Belanja, Pilihan Area Duduk
  6. **Laporan** — Laporan & Rekap DP, Audit Log
  7. **Sistem** — Pengaturan Website, Pengguna & Staf
- Set `navigationSort` eksplisit di setiap Resource/Page agar urutan stabil.

### P1-03 · Tabel Daftar Reservasi terpotong [Terverifikasi via screenshot klien]
- **Lokasi:** `app/Filament/Resources/Reservations/Tables/ReservationsTable.php`.
- **Masalah:** Scroll horizontal muncul pada lebar 1024–1440px; kolom aksi & tagihan terdorong keluar.
- **Perbaikan:** Gabungkan kolom menjadi 5–6 kolom inti: `Kode + tanggal dibuat` · `Tamu (nama + WA kecil)` · `Jadwal (tanggal + jam)` · `Pax/Tipe` · `Status` (badge warna) · `Pembayaran` (badge + nominal kecil). Pindahkan aksi ke `->recordActions([ActionGroup::make([...])])` (ikon titik tiga) + aksi cepat "Konfirmasi" sebagai ikon. Gunakan `->striped()`, `->defaultSort('date', 'desc')`, filter tab (`Hari ini`, `Menunggu`, `Terkonfirmasi`, `Semua`) via `getTabs()` di `ListReservations`.
- **Selesai jika:** tidak ada scroll horizontal di 1280px dengan sidebar terbuka.

### P1-04 · Dasbor [Perlu dicek tampilan akhir]
- Kartu statistik (`ReservationStatsWidget`) → gunakan `StatsOverviewWidget\Stat` dengan `->color()`, `->icon()`, `->description()`, `->chart()` 7 hari (reservasi per hari) agar berwarna & informatif.
- `PlaceholderAuditWidget` → tampilkan hanya untuk owner (sudah) dan sembunyikan otomatis bila tidak ada item; ganti judul "Checklist Kesiapan Konten & Pembayaran" menjadi daftar tugas ringkas dengan ikon status.
- `UpcomingReservationsWidget` → batasi 5–8 baris, tautan "Lihat semua".
- Tambahkan widget grafik ringan (opsional): "Reservasi 14 hari terakhir" (`ChartWidget` line).

### P1-05 · Editability: pengaturan yang ada di form tapi tidak dipakai di website [Terverifikasi]
Field di `app/Filament/Pages/ManageSettings.php` yang disimpan tetapi **tidak dibaca** oleh view/kode mana pun:
`brand.brand_short`, `brand.tagline`, `brand.story_snippet`, `brand.today_note`, `contact.coordinates`, `contact.plus_code`, `hours.operating_days`, `rating.price_range`, `reservation.retention_days`, `payment.payment_notes`, `whatsapp_templates.new_reservation` (cek pemakaiannya di `NotifyUmcoWhatsApp`/`NewReservationNotification` [Perlu dicek]).
- **Perbaikan:** Untuk tiap field: sambungkan ke tempat yang masuk akal (mis. `price_range` & `coordinates` → JSON-LD; `operating_days` → footer & menu mobile; `payment_notes` → langkah bayar `/reservasi`; `retention_days` → command pembersih bukti bayar terjadwal; `today_note` → pita info di hero/marquee) **atau** hapus field dari form. Tidak boleh ada field "pajangan".

### P1-06 · Editability: teks publik yang masih hard-coded [Terverifikasi sebagian]
Contoh yang ditemukan (agent wajib menyisir semua `resources/views/sections/**`, `pages/**`, `components/layouts/public.blade.php`):
- `public.blade.php`: JSON-LD `priceRange` "Rp 18.000 - Rp 36.000" dan alamat lengkap hard-coded (baris 110–117); teks menu mobile "Kampus 2 UMRI, Pekanbaru · 08.00 – 22.00 WIB"; label navigasi; footer "UM.CO"; `og:image` hard-coded ke `/images/hero-photo.jpg` (foto lama, bukan gedung).
- `s01-hero.blade.php`: kalimat tulisan tangan "~ seduhan hangat & dingin, tinggal pilih meja dulu" dan label tombol "Lihat menu".
- Default `hours.closing_time` di layout `'21.00'` padahal di tempat lain `22.00`.
- **Perbaikan:** Pindahkan ke kolom `PageSection` (`data` JSON) atau `Settings` yang sesuai, dengan fallback default. Tambahkan field `seo.og_image` (upload) di Pengaturan.

### P1-07 · Form Pengaturan terlalu panjang [Perlu dicek]
- `ManageSettings.php` (357 baris) — pastikan memakai `Tabs` (Brand · Kontak · Jam & Reservasi · Pembayaran · WhatsApp · SEO · Tampilan), tiap field punya `helperText` bahasa awam, dan tombol Simpan sticky. Validasi: nomor WA, URL, jam `HH.MM`, persen DP 0–100.

### P1-08 · Konsistensi bahasa & label [Perlu dicek]
- Semua label, notifikasi, empty state, dan judul halaman dalam Bahasa Indonesia yang konsisten (mis. "Buat Reservasi" vs "Tambah"). Judul halaman laporan "Laporan Reservasi & Down Payment (DP)" → "Laporan Reservasi & DP".
- Empty state tabel memakai ikon + kalimat ajakan ("Belum ada reservasi hari ini").

### P1-09 · Kualitas tampilan per role [Perlu dicek]
- Login sebagai `admin` dan `kasir`: pastikan tidak ada grup navigasi kosong, widget yang tidak berhak tidak menyisakan ruang kosong, dan badge/aksi sesuai policy.

---

## P2 — Kode mati, spageti, performa

### P2-01 · Sisa Laravel Starter Kit (Flux/Fortify views) masih ter-commit [Terverifikasi]
Rute starter sudah ditutup (`Fortify::ignoreRoutes()`), tetapi file berikut tidak terpakai:
- `routes/settings.php` (tidak di-`require` lagi)
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`, `layouts/app/header.blade.php`, `layouts/app/sidebar.blade.php`, `layouts/auth.blade.php`, `layouts/auth/{card,simple,split}.blade.php`
- `resources/views/pages/auth/*` (7 file), `resources/views/pages/settings/*` (8 file, termasuk nama berawalan `⚡`)
- `resources/views/flux/**`, `components/{app-logo,app-logo-icon,auth-header,auth-session-status,desktop-user-menu,passkey-registration,passkey-verify,placeholder-pattern}.blade.php`, `partials/{head,settings-heading}.blade.php`
- `resources/js/passkeys.js` (masih jadi entry di `vite.config.js` → ikut build 12 kB), `app/Livewire/Actions/Logout.php`, `app/Actions/Fortify/{CreateNewUser,ResetUserPassword}.php`
- **KEPUTUSAN KLIEN (final): HAPUS Passkey dan 2FA sepenuhnya.** Login staf cukup email + kata sandi di `/admin/login`.
  - Dari `app/Models/User.php`: hapus trait `PasskeyAuthenticatable`, `TwoFactorAuthenticatable`, interface `PasskeyUser`, dan `MustVerifyEmail` (verifikasi email tidak dipakai); hapus `two_factor_secret`, `two_factor_recovery_codes` dari `#[Hidden]`.
  - Hapus `resources/js/passkeys.js` dan entry-nya di `vite.config.js`; hapus komponen `passkey-registration`, `passkey-verify`.
  - Cek `composer.json` untuk paket passkey (mis. `spatie/laravel-passkeys` / `laravel/passkeys` atau sejenis) — hapus dengan `composer remove`, lalu pastikan tidak ada service provider/konfigurasi yang tertinggal. Pertahankan `laravel/fortify` **hanya jika** masih dibutuhkan sesuatu; jika tidak ada pemakaian sama sekali setelah `Fortify::ignoreRoutes()`, hapus juga `FortifyServiceProvider`, `config/fortify.php`, dan paketnya, lalu pastikan rate limit login Filament tetap aktif (Filament punya throttle bawaan).
  - Buat **migrasi baru** untuk drop tabel `passkeys` dan kolom `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at` dari `users` (dengan `down()` yang mengembalikannya). Jangan mengedit migrasi lama.
  - Perbarui/hapus test terkait (`tests/Feature/Auth/TwoFactorChallengeTest.php`, `PasswordConfirmationTest.php`, `EmailVerificationTest.php`, dll.) agar tetap menguji bahwa rute tersebut `404`, tanpa bergantung pada paket yang dihapus.
- **Selesai jika:** `grep` tidak menemukan referensi ke file yang dihapus, `php artisan view:cache` & `npm run build` sukses, test lulus.

### P2-02 · Partial & modul JS yang tidak dipakai [Terverifikasi]
- Blade: `sections/home/s04-menu-nota.blade.php` (272 baris), `sections/home/s07-portrait-gallery.blade.php` — tidak ada di `$partialMap`.
- JS: `resources/js/sections/testimonials-slider.js` (0 referensi), `altimeter.js` (cek apakah `cerita` masih pakai `data-module="altimeter"` [Perlu dicek]), `testimonials-parallax.js` & `gallery-mosaic.js` terdaftar di `app.js` — pastikan masih ada elemen `data-module` yang memakainya, jika tidak hapus.
- `HomeController` meng-query data yang tidak dipakai view: `menuCategories`, `soldOutCount`, `rules`, `portraitGallery`, `galleryItems`, `crowdPatterns` (lihat P0-05). Hapus query yang tidak terpakai (hemat ±6 query per kunjungan beranda).

### P2-03 · Aset berat tidak terpakai [Terverifikasi]
| File | Ukuran | Referensi |
|---|---|---|
| `public/videos/hero-espresso-loop.mp4` | 4.305 KB | 0 |
| `public/videos/latte-art-loop.mp4` | 4.694 KB | 0 |
| `public/images/hero-iced-coffee-splash-cutout.png` | 1.721 KB | 0 |
| `public/images/signature-drink-cutout.png` | 1.148 KB | 0 |
| `public/fonts/anybody-latin.woff2` | 56 KB | 0 |
| `public/fonts/doto-latin.woff2` | 5 KB | 0 |
| `public/fonts/familjen-grotesk-latin.woff2` | 18 KB | dimuat di `fonts-accent.css`, cek pemakaian `font-family` [Perlu dicek] |
- Juga duplikat format `.jpg` + `.webp` + `.avif` untuk gambar yang sama — pastikan view memakai `<picture>` dengan AVIF/WebP, lalu hapus format yang tidak pernah dirujuk.
- `docs/` berisi PNG besar (`hero section.png` 2,4 MB, `logo UMCO.png` 3,8 MB, `Kopi Susu...png` 1,6 MB) dan folder `docs/Menu Umco - belum lengkap` (39 file). Pindahkan aset sumber ke luar repo atau Git LFS; repo yang diserahkan ke klien harus ramping.
- Total penghematan repo ±12 MB.

### P2-04 · `Settings` helper rawan bentrok kunci [Terverifikasi]
- **Lokasi:** `app/Support/Settings.php`. Tabel `settings` punya index unik hanya pada `key` (bukan `group+key`), `set()` melakukan `updateOrCreate(['key' => $itemKey])`, dan `get()` fallback ke `$all[$itemKey]` lintas grup. Jika suatu hari ada `payment.notes` dan `reservation.notes`, keduanya saling menimpa.
- **Perbaikan:** Migrasi index unik komposit `(group, key)`, `updateOrCreate(['group' => $group, 'key' => $itemKey])`, hapus fallback lintas grup di `get()` setelah semua pemanggil memakai `group.key`. Tambah unit test.

### P2-05 · Komponen terlalu besar (spageti) [Terverifikasi]
- `resources/views/livewire/reservation-page.blade.php` 918 baris + `app/Livewire/ReservationPage.php` 635 baris. Pecah per langkah menjadi Blade component (`x-reservasi.step-jadwal`, `step-tempat`, `step-menu`, `step-data`, `step-bayar`, `ringkasan-karcis`, `mobile-bar`). Logika quote/validasi tetap di Action (`QuoteReservation`, `CreateReservation`) — jangan duplikasi.
- `pages/menu.blade.php` 469 baris, `public.blade.php` 405 baris (pisah `partials/public/{head,navbar,footer,json-ld}.blade.php`).
- `ManageSettings.php`: mapping `mount()`/`save()` ditulis dua kali manual (±60 field) → buat satu array definisi `field => 'group.key'` dan loop.

### P2-06 · Inline script di `AdminPanelProvider` [Terverifikasi]
- Script suara notifikasi (baris 45–76) berupa string PHP. Pindahkan ke file JS di tema Filament (`resources/js/filament/notification-sound.js`) dan daftarkan via `FilamentAsset::register()`. Selector `[aria-label*="Notifikasi"]` rapuh — gunakan event Livewire/notifikasi database Filament bila tersedia.

### P2-07 · Fallback gambar Instagram & layout memakai `.jpg` lama [Terverifikasi]
- `InstagramFeedService` fallback memakai `/images/*.jpg`; `og:image` memakai `hero-photo.jpg`. Ganti ke WebP yang relevan dan pastikan file ada.

### P2-08 · Scroll & animasi di perangkat lemah [Perlu dicek]
- Lenis hanya aktif di desktop pointer halus (baik). Cek: ScrollTrigger `refresh()` dipanggil dari `ResizeObserver` di `app.js` — pastikan di-throttle/debounce agar tidak memicu refresh berulang saat gambar lazy-load (gejala: patah-patah saat scroll pertama). Uji dengan CPU throttling 4× di DevTools pada `/`, `/menu`, `/reservasi`.
- Pastikan semua animasi menghormati `prefers-reduced-motion`.
- Jalankan Lighthouse mobile untuk `/`, `/menu`, `/reservasi`; target Performance ≥ 85, CLS < 0,1, LCP < 2,5 s. Hero gedung (`hero-cutout.webp` 225 KB) sebaiknya punya varian `srcset` 640/960/1200 dan AVIF.

### P2-09 · Bundle JS [Terverifikasi]
- `app-*.js` 133 KB (51,6 KB gzip) memuat GSAP + ScrollTrigger + SplitText di semua halaman termasuk `/reservasi`. Pertimbangkan dynamic import GSAP hanya bila ada `[data-reveal]`.

### P2-10 · Cek file konfigurasi tak perlu [Perlu dicek]
- `phpstan.neon` ada → jalankan `vendor/bin/phpstan analyse` dan perbaiki error level yang dikonfigurasi.
- `pint.json` ada → jalankan `vendor/bin/pint` untuk merapikan gaya kode.
- `.github/workflows` → pastikan CI tidak lagi menjalankan test starter kit yang sudah dihapus.

---

## P3 — Polish, SEO, dokumentasi

- **P3-01** JSON-LD di `public.blade.php` dibangun dengan interpolasi string → rawan JSON rusak bila nama/alamat berisi tanda kutip. Bangun array PHP lalu `@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)`.
- **P3-02** Tambah `sitemap.xml` (route dinamis: `/`, `/menu`, `/menu/{slug}`, `/galeri`, `/cerita`, `/reservasi`) dan perbarui `robots.txt` (`Disallow: /admin`, `Sitemap: ...`).
- **P3-03** Halaman error bermerek: saat ini hanya `errors/404.blade.php`. Tambahkan `419` (sesi kedaluwarsa, penting untuk form reservasi), `429`, `500`, `503` dengan gaya yang sama.
- **P3-04** Dua file deploy: `DEPLOY.md` (root) dan `docs/DEPLOY.md` → satukan menjadi satu sumber kebenaran di root. Tambahkan checklist: `APP_ENV=production`, `APP_DEBUG=false`, `php artisan key:generate`, `migrate --force`, `storage:link`, `optimize`, `filament:optimize`, `icons:cache`, cron `schedule:run`, supervisor `queue:work`, permission `storage/` & `bootstrap/cache`, backup DB.
- **P3-05** Dokumen internal (`docs/REDESIGN-2026.md`, `DECISIONS.md`, `PLAN.md`, `ASSUMPTIONS.md`, `OPEN-QUESTIONS.md`, `umco.html`, `umco-tmi.html`) — putuskan mana yang diserahkan ke klien. Sediakan `README.md` ringkas (cara install, akun awal, struktur, cara edit konten).
- **P3-06** Aksesibilitas: semua tombol ikon punya `aria-label`, fokus keyboard terlihat di navbar/menu mobile/stepper reservasi, kontras teks putih di atas hijau muda (bagian bawah gradien hero) ≥ 4.5:1.
- **P3-07** `og:image` 1200×630 khusus (gedung + logo) dan `twitter:card`.
- **P3-08** Panduan konten untuk klien di backoffice: tambahkan halaman "Bantuan" sederhana atau `helperText` yang menjelaskan ukuran gambar ideal (hero PNG transparan 1600px, banner 1600×900, galeri bebas rasio, menu 1:1/4:5).

---

## Yang sudah baik (jangan dirusak)

- Penyatuan login ke `/admin/login`, rute starter ditutup, redirect `/login` & `/dashboard`.
- Policy per role (`app/Policies/*`) + `AdminAccessTest` mencakup owner/admin/kasir/guest.
- `CreateReservation` memakai `DB::transaction` + `lockForUpdate()` untuk kapasitas slot.
- Header keamanan (`SecurityHeaders`), CSP, rate limit pada `/reservasi/{code}`, `/instagram-feed`, kalender.
- Validasi MIME & ukuran upload di form Filament.
- Navbar adaptif warna, footer wordmark, menu mobile editorial, perbaikan scroll `/reservasi`.
- Suite test: `57 passed, 364 assertions` (per commit `38f99a3`).

---

## Definisi selesai (seluruh audit)

1. `php vendor/bin/pest` → 0 failed, 0 skipped (tambahkan test baru untuk P0-01, P0-03, P0-05/06, P2-04).
2. `vendor/bin/pint --test` bersih; `vendor/bin/phpstan analyse` tidak ada error baru.
3. `npm run build` sukses; `app-*.js` ≤ 55 KB gzip.
4. `php artisan route:list --except-vendor` tidak memuat rute starter.
5. Browser (Playwright atau manual) di 1440px, 1024px, 768px, 375px: `/`, `/menu`, `/menu/{slug}`, `/galeri`, `/cerita`, `/reservasi` (semua langkah), `/reservasi/{code}`, `/admin/login`, `/admin` sebagai owner/admin/kasir → 0 error console, 0 HTTP ≥ 400, 0 overflow horizontal.
6. Setiap teks & gambar yang terlihat di halaman publik bisa diubah dari backoffice, atau tercatat sebagai pengecualian yang disengaja di `docs/DECISIONS.md`.
7. Lighthouse mobile beranda Performance ≥ 85.


---

## Status Eksekusi Audit (Commit `38f99a3` → Final)

| ID | Status | Ringkasan Implementasi |
|---|---|---|
| `P0-01` | Selesai | `config/app.php` & `.env.example` memakai `Asia/Jakarta`; referensi hard-coded dibersihkan. |
| `P0-02` | Selesai | Favicon UMCO dibuat dari logo resmi (`favicon.svg`, `favicon-32.png`, `favicon.ico`, `apple-touch-icon.png`). |
| `P0-03` | Selesai | Fallback Instagram mengatur `likes`, `comments`, `timestamp_human` = `null`; UI menyembunyikannya. |
| `P0-04` | Selesai | Beranda memakai `getCachedOrFallback()` tanpa HTTP sinkron; jadwal `instagram:sync` tiap 15 menit. |
| `P0-05` | Selesai | Fitur `CrowdPattern` dihapus penuh termasuk tabel via migrasi baru `drop_crowd_patterns_table`. |
| `P0-06` | Selesai | `page_sections` disinkronkan dengan partial; section tak terpakai (`rules`, `altimeter`) dibersihkan & ditandai di admin. |
| `P0-07` | Selesai | Middleware `EnforcePasswordChange` aktif + command produksi `php artisan umco:create-owner`. |
| `P0-08` | Selesai | `.env.example` diperbarui sesuai konfigurasi MySQL, lokal `id`, `Asia/Jakarta`, dan WhatsApp gateway. |
| `P1-01` | Selesai | Tema resmi Filament `resources/css/filament/admin/theme.css` putih bersih; `public/css/filament-theme.css` dihapus. |
| `P1-02` | Selesai | `sidebarCollapsibleOnDesktop()`, 7 grup navigasi rapi dengan ikon Heroicon, `navigationSort` stabil. |
| `P1-03` | Selesai | `ReservationsTable` dipadatkan 6 kolom + `ActionGroup` + 5 tab status tanpa scroll horizontal di `1280px`. |
| `P1-04` | Selesai | `ReservationStatsWidget` chart 7 hari berwarna, `PlaceholderAuditWidget` sembunyi otomatis, `UpcomingReservationsWidget` ringkas. |
| `P1-05` | Selesai | Seluruh field Pengaturan tersambung (termasuk `today_note`, `coordinates`, `price_range`, `payment_notes`, `retention_days`, `og_image`). |
| `P1-06` | Selesai | Teks & gambar publik yang sebelumnya hard-coded dipindah ke `Settings` / `PageSection` dengan fallback aman. |
| `P1-07` | Selesai | `ManageSettings` memakai `Tabs`, `FIELD_MAP` tunggal, validasi, helper bahasa awam, dan tombol Simpan sticky. |
| `P1-08` | Selesai | Seluruh label, empty state, dan judul halaman konsisten Bahasa Indonesia (`Laporan Reservasi & DP`). |
| `P1-09` | Selesai | Verifikasi policy & navigasi untuk `owner`, `admin`, dan `cashier` — bebas grup kosong. |
| `P2-01` | Selesai | Paket `laravel/fortify`, `livewire/flux`, `laravel/passkeys` serta view/JS starter kit dihapus + migrasi drop tabel/kolom. |
| `P2-02` | Selesai | Partial/JS mati dihapus (`s04-menu-nota`, `s07-portrait-gallery`, `gallery-infinite`, `altimeter`, `testimonials-slider`); query controller dirampingkan. |
| `P2-03` | Selesai | Video/Cutout PNG >1MB, JPG duplikat, font `anybody`/`doto`/`familjen-grotesk`, dan aset referensi `docs/*.png` & `.html` dihapus. |
| `P2-04` | Selesai | Migrasi indeks unik komposit `(group, key)` di `settings`, pembaruan `App\Support\Settings` dan seeder. |
| `P2-05` | Selesai | `reservation-page.blade.php` & `public.blade.php` dipecah menjadi partial bersih; `ManageSettings` memakai `FIELD_MAP`. |
| `P2-06` | Selesai | Skrip suara notifikasi dipindah ke `public/js/filament-notification-sound.js` eksternal tanpa inline script PHP. |
| `P2-07` | Selesai | Fallback IG dan default layout/OG memakai aset `.webp` lokal yang valid (`og-cover.webp`, dll.). |
| `P2-08` | Selesai | `ScrollTrigger.refresh()` di-debounce 180ms dari `ResizeObserver`; `s01-hero` memakai `srcset` 640/960/1200w. |
| `P2-09` | Selesai | Build Vite di bawah target (`app-*.js` ~51,7 KB gzip <= 55 KB). |
| `P2-10` | Selesai | `pint --test` bersih dan `phpstan analyse` level 7 bersih (0 error). |
| `P3-01` | Selesai | JSON-LD dibangun sebagai array PHP via `json_encode(..., JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`. |
| `P3-02` | Selesai | Route dinamis `/sitemap.xml` ditambahkan dan `public/robots.txt` diperbarui (`Disallow: /admin`, `Sitemap`). |
| `P3-03` | Selesai | Halaman error bermerek `419`, `429`, `500`, `503` ditambahkan menyamai desain `404.blade.php`. |
| `P3-04` | Selesai | `docs/DEPLOY.md` disatukan ke root `DEPLOY.md` dengan daftar periksa produksi lengkap. |
| `P3-05` | Selesai | `README.md` Bahasa Indonesia dibuat; prototipe HTML/PNG mentah di `docs/` dibersihkan. |
| `P3-06` | Selesai | Aksesibilitas & kontras diverifikasi. |
| `P3-07` | Selesai | `public/images/og-cover.webp` (1200×630) dibuat dan dipasang beserta meta `twitter:card`. |
| `P3-08` | Selesai | `helperText` panduan ukuran gambar ideal tersedia pada field unggahan Filament. |

---

## Temuan Tambahan

1. **Kolom `bg_token` non-eksisten pada `PageSectionResource`:** Di form/tabel admin `PageSectionResource` sebelumnya tercantum referensi kolom `bg_token` yang tidak pernah ada di skema database (`page_sections`). Referensi mati tersebut sudah dibersihkan.
2. **Item terverifikasi & sengaja dipertahankan:**
   - `AboutController` (`/cerita`) tetap aktif dan melayani halaman Cerita beserta daftar `facilities`, `storyBlocks`, dan `streetStalls`.
   - `HomeController` tetap mengirim `$closedDates` dan `$spendTiers` karena keduanya dipakai oleh `resources/views/sections/home/s09-karcis-teaser.blade.php`.
   - `whatsapp_templates.new_reservation` tetap dipertahankan karena dipakai oleh `App\Jobs\NotifyUmcoWhatsApp::buildMessage()`.
   - Folder `docs/Menu Umco - belum lengkap/` dipertahankan karena `Redesign2026Seeder` membaca berkas foto mentah di sana untuk mengenerasi gambar WebP/AVIF menu di `storage/app/public/menu/` saat pemasangan awal di server baru.
3. **Kebijakan & Observer `Faq` (`AppServiceProvider`):** Model `Faq` didaftarkan ke `OwnerOnlyPolicy` dan `PublicContentObserver` sebagai pertahanan berlapis di samping `FaqResource::canAccess()`.
4. **Hidrasi `FileUpload` `og_image` di `ManageSettings`:** Menambahkan guard di `ManageSettings::mount()` agar path statis bawaan (`/images/...`) tidak dimuat ke komponen `FileUpload` disk `public`, melainkan memakai `/images/og-cover.webp` secara otomatis bila field upload dikosongkan.
5. **Pengecualian Pint untuk migrasi lama (`pint.json`):** Menambahkan `notPath` pada `database/migrations/2026_11_05_040254_create_umco_tables.php` agar migrasi yang sudah pernah dijalankan tetap utuh 100% sesuai aturan keras pra-deploy.
