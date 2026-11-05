# RENCANA KERJA & TRACKING FASE (PLAN.md): UM.CO HOUSE

Dokumen ini memetakan tahapan eksekusi dari Fase 1 hingga Fase 6 sesuai Master Prompt Bagian 14.

---

## Ringkasan Fase & Status

- [x] **Fase 0: Persiapan Lingkungan & Audit Kebutuhan**
  - [x] Baca tuntas master prompt tanpa melompat
  - [x] Cek Laravel Herd, PHP 8.4, Composer 2.9, Node 24, Git
  - [x] Cek koneksi MySQL & buat database `website_umco`
  - [x] Kloning 4 repositori panduan desain (`unslop`, `impeccable`, `taste-skill`, `hallmark`)
  - [x] Inisialisasi Git repository

- [ ] **Fase 1: Fondasi Desain & Dokumentasi Awal**
  - [x] Buat `docs/DESIGN-PRINCIPLES.md` (analisis 4 repo desain)
  - [x] Buat `docs/ASSUMPTIONS.md` (asumsi default operasional)
  - [x] Buat `docs/OPEN-QUESTIONS.md` (pertanyaan verifikasi klien)
  - [x] Buat `docs/DECISIONS.md` (keputusan arsitektur & desain)
  - [ ] Konfigurasi Design Tokens di `@theme` Tailwind v4 (`resources/css/app.css`)
  - [ ] Halaman specimen tipografi & warna (`/specimen`) untuk pengujian visual
  - [ ] Siapkan daftar 10 ide tambahan yang diperkaya
  - [ ] Commit Fase 1: `feat: design foundation and token setup`

- [ ] **Fase 2: Struktur Data, Model & Backend Core**
  - [ ] Konfigurasi `.env` ke MySQL `website_umco`
  - [ ] Migrasi database lengkap:
    - `menu_categories` & `menu_items` (dengan flag favorite, favorite_order, is_new, is_available)
    - `banners` (dengan jadwal tayang dan CTA)
    - `reservations` & `reservation_items` (mode A/B, status, payment status, subtotal, DP, kode unik)
    - `story_blocks`, `gallery_items`, `facilities`, `street_stalls`, `testimonials`, `closed_dates`
    - `settings` (Brand, Contact, Hours, Reservation, Payment, WhatsApp Templates, SEO, Footer)
  - [ ] Buat helper normalisasi nomor WhatsApp `App\Support\PhoneNumber`
  - [ ] Tulis unit & feature test Pest untuk `PhoneNumber` (semua contoh 9.3 dan kasus invalid)
  - [ ] Model, Relationships, Casts, dan Scopes
  - [ ] Seeder komprehensif data nyata UMCO + menu contoh realistis (Rp 25.000 – Rp 48.000)
  - [ ] Observers untuk pembersihan cache otomatis saat model berubah
  - [ ] Commit Fase 2: `feat: database schema, models, seeders, and phonenumber helper with tests`

- [ ] **Fase 3: Admin CMS & Smart WhatsApp Engine (Zero-Code)**
  - [ ] Setup Admin Panel (Filament / Admin CMS)
  - [ ] Dashboard Admin:
    - Statistik (Reservasi hari ini, menunggu konfirmasi, total DP, menu aktif)
    - Kartu "Perlu Diganti" (placeholder detector agar tidak bocor ke publik)
    - Reservasi terdekat
  - [ ] Resource Manajemen Reservasi:
    - Tabel dengan filter status, tanggal, mode, status bayar
    - Detail reservasi, pratinjau bukti transfer aman, ubah status
    - Smart WhatsApp Buttons di setiap baris (Konfirmasi, Pembatalan/Menu Kosong, Bebas)
  - [ ] Resource Menu & Kategori (CRUD, reordering, switch sold out, favorite order)
  - [ ] Resource Banner Serbaguna Dinamis
  - [ ] Resource Cerita, Galeri (Indoor/Outdoor), Fasilitas, Stan Street Food, Ulasan
  - [ ] Pengaturan Lengkap (Kontak, Jam Buka, Aturan Reservasi, Rekening/QRIS, Template WhatsApp, SEO, Footer)
  - [ ] Halaman Laporan Reservasi & DP (Filter rentang tanggal, ringkasan, ekspor CSV, tampilan cetak rapi)
  - [ ] Activity Log (riwayat perubahan harga, konfirmasi reservasi, pengubahan setting)
  - [ ] Commit Fase 3: `feat: admin cms, reservation management, and smart whatsapp engine`

- [ ] **Fase 4: Halaman Publik Multipage (Livewire + Tailwind v4 + GSAP)**
  - [ ] Master Layout publik (`x-layouts.app`):
    - Floating Header transparan ke solid, Logo tengah, Nav kiri, CTA kanan, Hamburger mobile
    - Footer dramatis: navigasi, info UMRI, jam buka, wordmark raksasa "UM.CO" + minuman signature bertumpuk
    - Lifecycle script GSAP/Alpine terintegrasi `wire:navigate` aman tanpa kebocoran memori
  - [ ] Halaman `/` (Beranda):
    - Hero satu layar penuh (headline display, visual H mask / jendela, stiker rating 4.9 berputar, dual CTA)
    - Banner Serbaguna Dinamis (carousel ringan Alpine)
    - Cuplikan Cerita (About snippet)
    - Sorotan Menu (Best seller & Barista's pick)
    - Denah H interaktif & Pemilih Suasana (Indoor AC vs Outdoor Street Food)
    - Seksi Ulasan 4.9 (Google review seeder bertanda sample)
    - Ajak Kolaborasi kampus (CTA WA terformat otomatis)
    - Teaser Reservasi cepat (meneruskan query date/time/pax ke `/reservasi`)
  - [ ] Halaman `/menu`:
    - Filter kategori instan & pencarian real-time via Alpine JSON
    - Label Favorite, New, Sold Out
    - Tata letak asimetris + dot leader
    - Hash navigation (`#signature`, dll.)
  - [ ] Halaman `/about`:
    - Filosofi UM.CO House & arsitektur gedung huruf H di kampus UMRI
    - Perjalanan Dari Biji ke Cangkir (1-2-3 sequence)
    - Galeri suasana Indoor & Outdoor
    - Fasilitas & Direktori Tenant Street Food
    - Peta lokasi interaktif (click to load iframe) + tombol Google Maps & Petunjuk Arah
  - [ ] Halaman `/reservasi`:
    - Form ramah HP, input besar, validasi live
    - Pilihan Mode A (Meja) vs Mode B (Meja + Menu + DP)
    - Slot waktu dinamis, kalkulasi kapasitas real-time dengan DB lock
    - Perhitungan DP otomatis & pratinjau rekening/QRIS
    - Unggah bukti transfer privat & aman
    - Halaman Sukses: Tiket Reservasi interaktif + unduh kalender `.ics` + kirim detail ke WhatsApp
  - [ ] Commit Fase 4: `feat: complete public multipage website and reservation flow`

- [ ] **Fase 5: Optimalisasi Performa, Testing & Dokumentasi Deploy**
  - [ ] Pengujian Pest menyeluruh (PhoneNumber, DP calculation, capacity lock, observer cache purge, HTTP responses)
  - [ ] Pengujian performa (Lighthouse mobile 90+, LCP < 2.0s, CLS < 0.05, zero layout shift)
  - [ ] Optimasi aset gambar (WebP/AVIF, `<x-picture>`, lazy load, fetchpriority)
  - [ ] Tulis `docs/ASSETS.md`, `docs/PERFORMANCE.md`, dan `docs/DEPLOY.md`
  - [ ] Commit Fase 5: `perf: optimizations, tests, and deployment documentation`

- [ ] **Fase 6: Verifikasi Akhir & Laporan Selesai**
  - [ ] Pengujian manual di browser `https://website-umco.test/`
  - [ ] Verifikasi semua alur pengunjung dan alur admin
  - [ ] Pembuatan laporan akhir proyek
