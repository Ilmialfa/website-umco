# Katalog Aset Visual UM.CO House

Dokumen ini memuat inventaris lengkap seluruh aset visual, logo, grafis vektor, icon, dan foto placeholder yang digunakan pada platform website **UM.CO House**. Dilengkapi dengan panduan dimensi ideal, format yang disarankan, lisensi, serta instruksi penggantian mandiri bagi staf/klien melalui Panel Admin Filament.

---

## 1. Inventaris Logo & Identitas Brand

| Nama Aset | Lokasi File | Format | Resolusi / Aspek | Keterangan & Penggunaan |
|---|---|---|---|---|
| **Logo Primer (Dark)** | `public/images/logo.svg` | SVG (Vektor) | Vektor fluid (4:1) | Logo resmi kata "UM.CO" dengan aksen titik crema dan dot sub-label. Digunakan pada navbar desktop/mobile dan header cetak. |
| **Logo Sekunder (Light)** | `public/images/logo-light.svg` | SVG (Vektor) | Vektor fluid (4:1) | Versi putih/crema untuk background gelap (misal footer, kartu promosi malam hari). |
| **Favicon Browser** | `public/favicon.svg` | SVG (Vektor) | 64 × 64 px | Ikon cangkir geometri kopi dan huruf U yang tajam pada tab browser. |
| **Apple Touch Icon** | `public/favicon.svg` | SVG / PNG | 180 × 180 px | Ikon bookmark perangkat mobile iOS / Android. |

---

## 2. Ilustrasi Vektor & Header/Footer Highlights

| Nama Aset | Lokasi File | Format | Lisensi | Keterangan & Penggunaan |
|---|---|---|---|---|
| **Signature Drink Hero/Footer** | `public/images/signature-drink.svg` | SVG (Vektor) | Desain Internal UM.CO | Ilustrasi vektor gelas kaca signature coffee layered latte dengan cream topping dan piring saji metalik. Digunakan pada section dramatis footer di atas giant wordmark "UM.CO" (`z-index: 10`, parallax effect). |
| **Hero Architectural Artwork** | `public/images/hero-art.svg` | SVG (Vektor) | Desain Internal UM.CO | Ilustrasi denah artistik arsitektur huruf H UM.CO House di lingkungan kampus hijau UMRI. Digunakan pada hero banner dan halaman `/about`. |

---

## 3. Placeholder Menu & Media

| Kategori Menu | Rekomendasi Resolusi | Format Disarankan | Penggantian via Admin |
|---|---|---|---|
| **Signature Coffee & Espresso** | 800 × 800 px (1:1 Square) | WebP / JPG (< 200 KB) | `/admin/menu-items` &rarr; Pilih Menu &rarr; Unggah Gambar |
| **Non-Coffee & Artisan Tea** | 800 × 800 px (1:1 Square) | WebP / JPG (< 200 KB) | `/admin/menu-items` &rarr; Pilih Menu &rarr; Unggah Gambar |
| **Makanan Berat & Rice Bowl** | 800 × 800 px (1:1 Square) | WebP / JPG (< 200 KB) | `/admin/menu-items` &rarr; Pilih Menu &rarr; Unggah Gambar |
| **Pastry & Camilan** | 800 × 800 px (1:1 Square) | WebP / JPG (< 200 KB) | `/admin/menu-items` &rarr; Pilih Menu &rarr; Unggah Gambar |
| **Banner Hero Beranda** | 1920 × 800 px (21:9 Widescreen) | WebP / JPG (< 400 KB) | `/admin/banners` &rarr; Tambah/Ubah Banner |
| **Galeri Foto Suasana** | 1200 × 800 px (3:2 Landscape) | WebP / JPG (< 300 KB) | `/admin/gallery-items` &rarr; Tambah Foto (Pilih Grup: Indoor AC / Outdoor) |
| **QRIS Pembayaran DP** | 800 × 1000 px (Potret) | PNG / WebP (< 250 KB) | `/admin/manage-settings` &rarr; Tab "Metode Pembayaran" &rarr; Unggah QRIS |

---

## 4. Tipografi Resmi (Bunny Fonts Local Bundle)

Website ini menggunakan dua font tipografi modern berkinerja tinggi yang dibundel secara lokal (tanpa request eksternal pihak ketiga) untuk menjamin privacy & kecepatan 100/100:

1. **Heading / Display:** `Fraunces` (Serif modern dengan karakter berani, bobot 500, 600, 700, 800).
   - Digunakan untuk: Judul halaman, wordmark footer, nama menu unggulan, dan kartu harga.
2. **Body / Interface:** `Instrument Sans` (Sans-serif neo-grotesque bersih, bobot 400, 500, 600, 700).
   - Digunakan untuk: Paragraf deskripsi, navigasi header, form reservasi, badge, dan tabel admin.

---

## 5. Audit Placeholder ("Perlu Diganti") di Panel Admin

Untuk memastikan pemilik/staf kafe tidak melupakan data placeholder sementara:
- Di Dashboard Admin (`/admin`), terdapat widget khusus **"Audit Data Placeholder & Kesiapan Go-Live"**.
- Widget ini secara otomatis mendeteksi:
  1. Apakah QRIS pembayaran masih menggunakan placeholder default.
  2. Apakah nomor rekening bank masih berstatus placeholder.
  3. Apakah terdapat ulasan bertanda `is_sample = true` yang belum digantikan oleh ulasan asli pengunjung.
  4. Apakah ada menu aktif yang belum memiliki foto asli.
