# CATATAN KEPUTUSAN ARSITEKTUR & DESAIN (DECISIONS.md)

Dokumen ini mencatat seluruh keputusan penting, alternatif yang dipertimbangkan, dan alasan teknis selama pembangunan website UM.CO House (UMCO).

---

## Decision 01: Multi-Page Architecture with Livewire 3/4 & `wire:navigate`
- **Konteks:** Website membutuhkan transisi antarhalaman yang instan layaknya SPA, namun tetap ramah SEO dan mudah di-cache di shared hosting.
- **Keputusan:** Menggunakan Livewire dengan direktif `wire:navigate` pada semua link publik (`/`, `/menu`, `/about`, `/reservasi`).
- **Penanganan Khusus:** Lifecycle JavaScript diatur ketat: inisialisasi GSAP/Alpine pada event `livewire:navigated` dan pembersihan ScrollTrigger pada `livewire:navigating` untuk mencegah kebocoran memori atau animasi ganda.

## Decision 02: Dual-Mode Reservation Workflow
- **Konteks:** Pengunjung memiliki kebutuhan beragam: ada yang hanya ingin mengamankan meja untuk nugas/rapat santai (Mode A: tanpa DP), dan ada yang ingin memastikan makanan/minuman sudah disiapkan saat tiba untuk pertemuan khusus (Mode B: wajib DP).
- **Keputusan:** Menggabungkan kedua kebutuhan dalam satu halaman cerdas (`/reservasi`). Pengunjung dapat mencentang "Pesan menu sekaligus" untuk membuka daftar menu. Perhitungan subtotal dan DP berjalan seketika di sisi klien dan divalidasi ulang di server dalam database transaction dengan row lock (`lockForUpdate`).

## Decision 03: Client-Side Menu Filtering for Instant Performance
- **Konteks:** Katalog menu UMCO berkisar 30–60 item. Melakukan request server (AJAX/Livewire round-trip) pada setiap ketukan pencarian atau filter kategori akan boros bandwidth dan terasa lambat di jaringan seluler 4G.
- **Keputusan:** Menyediakan data menu awal sebagai JSON ringan ke komponen Alpine.js di halaman `/menu`. Filter kategori dan pencarian nama/deskripsi berjalan instan 0ms tanpa request ke server.

## Decision 04: The H-Building Signature Motif
- **Konteks:** Denah gedung berbentuk huruf H adalah ciri khas fisik UMCO di Kampus UMRI yang tidak dimiliki kafe lain di Pekanbaru.
- **Keputusan:** Mengadopsi huruf H bukan sekadar sebagai huruf, melainkan elemen interaktif:
  1. Denah H interaktif yang membagi sayap Indoor (AC, nugas) dan sayap Outdoor (street food, obrolan malam).
  2. Masking visual pada galeri dan hero.
  3. Desain loader dan favicon minimalis.

## Decision 05: Cache Architecture & Auto-Purge Observers
- **Konteks:** Website ditargetkan cepat (LCP < 2.0s) pada hosting dengan spesifikasi standar, namun perubahan menu/banner oleh Admin harus langsung terlihat oleh pengunjung.
- **Keputusan:** Cache halaman publik (`/`, `/menu`, `/about`) menggunakan caching layer, dengan **pengecualian mutlak untuk `/reservasi` dan `/admin/*`** demi keamanan token CSRF. Setiap perubahan model (`MenuItem`, `MenuCategory`, `Banner`, `Setting`, `Testimonial`) otomatis memicu event observer untuk membersihkan cache publik secara tepat sasaran.

## Decision 06: Zero-Cost WhatsApp Integration (Smart WA Engine)
- **Konteks:** Klien tidak menggunakan WhatsApp Business API berbayar bulanan, namun staf membutuhkan efisiensi tinggi saat mengonfirmasi reservasi pelanggan.
- **Keputusan:** Membangun `PhoneNumber` normalizer yang kuat (mengubah semua format lokal `08...`, `+62 8...`, `8...` menjadi `628...`) dan pembuat tautan pesan instan (`wa.me`) dengan template otomatis yang bisa diatur di panel Admin.

## Decision 07: Database Driver Fallback
- **Konteks:** Dev environment menggunakan MySQL (port 3306) di Herd. Pada beberapa shared hosting, Redis mungkin tidak tersedia.
- **Keputusan:** Sistem dikonfigurasi menggunakan driver database / file sebagai fallback default yang aman dan teruji di lingkungan Laravel Herd, dengan opsi switch mudah ke Redis melalui `.env`.

---

## Redesain Total 2026 (Sumber Kebenaran: `docs/REDESIGN-2026.md`)

Keputusan di bawah ini menggantikan Decision 02 dan Decision 04 di atas.

### Decision 08 (Fase 1): Identitas Kertas-Tinta-Karcis & Penghapusan Motif Huruf H
- **Konteks:** Konsep lama ("satu gedung dua suasana berbeda", Wing Barat/Timur, denah huruf H, palet hijau gelap, kartu berikon, serta `backdrop-blur`) terasa kaku dan generik.
- **Keputusan:** Seluruh tampilan publik diganti ke palet kalem `--kertas` (`#F5F2EA`), `--tinta` (`#22263A`), `--mentega` (`#EFC94C`), `--zaitun` (`#C8D5AE`), `--blush` (`#F0D2C9`), `--cabai` (`#C4472E`) tanpa emoji, tanpa bayangan dekoratif, tanpa blur latar, dan rute `/about` dialihkan permanen (301) ke `/cerita`. `/specimen` dihapus.

### Decision 09 (Fase 1): Font Self-Hosted & Anggaran JS Awal <= 60 KB Gzip
- **Konteks:** Menggantungkan font ke CDN Google/Bunny memperlambat LCP seluler, dan bundler lama menyertakan JS berat di muatan awal.
- **Keputusan:**
  1. Empat font (`Anybody` variable `wdth`/`wght`, `Familjen Grotesk`, `Reenie Beanie`, `Doto`) disimpan lokal di `public/fonts/`. Hanya `anybody-latin.woff2` dan `familjen-grotesk-latin.woff2` yang di-preload; CSS font aksen dimuat asinkron.
  2. Smooth scroll (`Lenis`) dimatikan secara default (`motion.smooth_scroll = false`) dan hanya di-import dinamis bila owner mengaktifkannya dari panel.
  3. Asset gambar dekoratif footer (`public/images/footer-cutout.webp`, 34 KB WebP transparan) di-render bersih dari potongan gelas kopi tanpa kotak bayangan lantai.

### Decision 10 (Fase 2): Migrasi Aditif, Konversi Gambar Menu 3 Ukuran, & Standar Pengisian Data Awal
- **Konteks:** Sistem membutuhkan skema baru (`spend_tiers`, `seating_options`, `crowd_patterns`, `rules`, `page_sections`, `bean_origins`, `notifications`, serta kolom tambahan pada `reservations`, `reservation_items`, dan `menu_items`) tanpa mengubah migrasi historis. Foto menu asli di `docs/Menu Umco - belum lengkap/` berbentuk `.JPG` beresolusi besar (2–5 MB per file).
- **Keputusan:**
  1. Seluruh skema baru ditambahkan lewat migrasi aditif `2026_11_14_000001_add_redesign_2026_tables_and_columns.php`.
  2. `App\Support\MenuImageProcessor` otomatis memperbaiki orientasi EXIF kamera dan menghasilkan varian **WebP + AVIF** (480px, 800px, 1200px) idempoten ke `storage/app/public/menu/` (234 berkas untuk 39 menu), dengan nilai koma (misal `25,5k`) tersimpan sebagai integer rupiah (`25500`).
  3. Nilai default Bagian 16 diterapkan: jam buka `08:00–22:00`, minimal belanja bertingkat (`2 -> 50k`, `3 -> 100k`, `4 -> 125k`, `5 -> 150k`, `6+ -> 150k + 25k/orang`), kebijakan DP pindah jadwal 1x maksimal 2 jam sebelum datang, serta nomor uji bisnis `02248001571` (`622248001571`).

### Decision 11 (Fase 3): 13 Section Landing Modular, Lazy Chunk GSAP, & Penyaluran Nota ke Reservasi
- **Konteks:** Halaman utama memerlukan 13 ritme visual yang berbeda tanpa melebihi anggaran muatan awal `app.js` <= 60 KB gzip serta harus berbeda penataannya pada layar HP (`<640px`), Tablet (`640–1023px`), dan Desktop (`>=1024px`).
- **Keputusan:**
  1. Setiap interaksi section (`hero`, `led-board`, `story-line`, `signature-reel`, `brew-toy`, `crowd-chart`, `testimonials-slider`, `gallery-mosaic`) dipisah menjadi modul Vite dinamis (`0.3–1.5 KB` gzip) yang dimuat lewat `IntersectionObserver` `[data-module]`, sehingga `app.js` awal tetap ~50,6 KB gzip.
  2. Urutan dan visibilitas 13 section dikendalikan lewat `PageSection` di basis data; ketika tabel kosong saat uji unit/fitur, fallback urutan default tetap menampilkan ketigabelas section.
  3. S04 Nota menyimpan item pilihan pengunjung di `localStorage` (`umco_nota_v1`) sekaligus mengirimkan parameter query (`?items=...`) maupun `?tanggal=&orang=` dari S09 Karcis Teaser menuju `/reservasi`.

### Decision 12 (Fase 4): Halaman Menu Daftar/Foto Client-Side & Halaman Cerita Altimeter
- **Konteks:** Halaman `/menu` harus tetap ringan saat memfilter 39 item (termasuk varian Americano dan suhu Panas/Es), menyediakan tampilan ringkas di HP, serta menampilkan placeholder tipografis jujur untuk menu tanpa foto. Halaman `/cerita` harus membuang seluruh narasi arsitektur lama.
- **Keputusan:**
  1. `MenuController` menyajikan data terstruktur (varian, suhu, URL AVIF/WebP 480/800/1200, flag placeholder) sekali ke Alpine agar pencarian dan tab kategori jalan 0 ms di sisi klien; default di HP `<640px` adalah tampilan Daftar bergaya nota agar compact, sedangkan Tablet/Desktop default ke grid Foto.
  2. Sheet detail item di `/menu` mengizinkan pemilihan varian & suhu lalu menyinkronkan ke `localStorage` (`umco_nota_v1`) yang terhubung ke `/reservasi`.
  3. `/cerita` menampilkan pembuka, daftar ketinggian biji kopi (`BeanOrigin`) dengan modul lazy `altimeter.js` (`0,35 KB` gzip), tiga hal yang kami pegang, daftar teman jajan gaya tabel nota, serta ajakan kolaborasi WhatsApp.

### Decision 13 (Fase 5): Mesin Reservasi Server, Halaman Status Karcis, & WA Dual-Lapis
- **Konteks:** Aturan reservasi (minimal 2 orang, minimal belanja bertingkat dari `SpendTier`, DP minimal 50% dibulatkan ke atas ke ribuan terdekat, QRIS statis + unggah bukti wajib) harus berjalan identik untuk publik maupun wizard admin, sementara notifikasi WhatsApp UMCO harus tetap berfungsi baik tanpa biaya pihak ketiga maupun dengan gateway opsional.
- **Keputusan:**
  1. Perhitungan kuota dan harga dipusatkan pada `QuoteReservation`, dan seluruh validasi aturan bisnis, penguncian kapasitas slot (`lockForUpdate` untuk MySQL/MariaDB), penyimpanan bukti ke `payment_proofs/`, logging aktivitas, notifikasi database Filament (`NewReservationNotification`), serta job `NotifyUmcoWhatsApp` dipusatkan pada `CreateReservation`.
  2. `PhoneNumber::normalizeBusiness()` ditambahkan khusus untuk nomor tetap bisnis UMCO (`02248001571 -> 622248001571`) tanpa mengubah ketatnya validasi seluler pelanggan (`PhoneNumber::normalize()`).
  3. `/reservasi` menggunakan 5 langkah geser horizontal (`ReservationPage`) + 3 tata letak terpisah (Desktop 7+5 sticky karcis, Tablet kartu karcis lipat atas, HP strip bawah 64px + bottom sheet) dengan kompresi gambar <= 1600px dan unduh karcis PNG berbasis `<canvas>` di modul lazy `reservasi-tools.js` (`1,65 KB` gzip).
  4. `/reservasi/{code}` (`ReservationStatusPage`) mengamankan karcis di balik 4 digit terakhir WA yang tersimpan di session, memakai `wire:poll.30s` untuk pembaruan stempel otomatis, serta mendukung unggah ulang bukti ketika `payment_status === 'rejected'`.
  5. Placeholder vektor statis `public/images/qris-placeholder.svg` dipakai sebagai fallback bila `payment.qris_image_path` di Settings masih kosong.
