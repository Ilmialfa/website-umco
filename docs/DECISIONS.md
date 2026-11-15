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

### Decision 14 (Fase 6): Panel Admin Filament Kertas-Tinta, Pengingat 15 Menit, Wizard Walk-in, & Matriks Izin 12.5
- **Konteks:** Panel admin perlu selaras dengan token kertas/tinta/mentega, mendukung notifikasi lonceng real-time, pengingat 15 menit sebelum tamu datang, aksi WA terlacak tanpa memperlambat tabel, wizard admin yang berbagi logika persis dengan halaman publik, serta pembatasan ketat Owner vs Admin vs Kasir.
- **Keputusan:**
  1. `AdminPanelProvider` mengaktifkan `databaseNotifications()->databaseNotificationsPolling('30s')` dan menghapus referensi font eksternal; `filament-theme.css` memakai `#F5F2EA`, `#FCFBF7`, `#22263A`, `#EFC94C` dengan zero box-shadow dan mendukung opsional nada notifikasi Web Audio saat diaktifkan Owner.
  2. `RemindUpcomingReservations` (`reservations:remind`, dijadwalkan `everyMinute()` di `routes/console.php`, didokumentasikan di `DEPLOY.md`) mengirimkan notifikasi satu kali untuk jendela `remind_before_minutes` (15 menit) dan peringatan keterlambatan menjelang habisnya `grace_minutes` beserta aksi langsung `Chat WA` & `Lihat`.
  3. Pencatatan log WhatsApp dipindahkan ke route `GET /admin/wa/{reservation}/{template}` (`AdminWhatsAppRedirectController`) dan 6 template plus aksi cepat status dikumpulkan dalam `ReservationActions` untuk tabel & halaman view.
  4. `CreateReservation` di Filament diubah menjadi Wizard 5 langkah (`HasWizard`) yang memanggil `App\Actions\Reservations\CreateReservation` dengan opsi tambahan `source` dan toggle `skip_min_rules` khusus Owner.
  5. Seluruh resource & halaman CMS baru (`PageSectionResource`, `RuleResource`, `SpendTierResource`, `SeatingOptionResource`, `BeanOriginResource`, `CrowdPatternPage`), konten lama (`Banner`, `StoryBlock`, `GalleryItem`, `StreetStall`, `Testimonial`, `Facility`), `ManageSettings`, `UserResource`, serta `ActivityLogResource` dikunci untuk `Owner`; `Kasir` hanya mengelola reservasi dan toggle `is_available` menu tanpa akses ubah harga/foto/CMS/pengaturan.

### Decision 15 (Fase 7): Audit Statis, Perbaikan Serialisasi Cache, Optimasi Gambar, & Verifikasi Lintas Layar
- **Konteks:** Uji browser langsung pada `https://website-umco.test` di tiga ukuran layar (`375px`, `820px`, `1440px`) serta audit Lighthouse menemukan serialisasi koleksi Eloquent di `Cache::remember`, 404 font saat mode dev Vite aktif, file hero JPEG berukuran 745–886 KB, kelas tipografi `.type-display`/`.type-h1`/`.type-h2` yang terlewat, serta detail aksesibilitas (`heading-order`, `aria-label` otomatis `SplitText` pada `<div>`, dan rasio kontras `--cabai` di atas latar `#F0D2C9`).
- **Keputusan:**
  1. Menghapus pembungkus `Cache::remember` untuk model/koleksi Eloquent di `HomeController`, `MenuController`, dan `AboutController` agar tidak memicu `__PHP_Incomplete_Class` antar permintaan.
  2. Memindahkan definisi `@font-face` `Anybody` & `Familjen Grotesk` langsung ke `<head>` `public.blade.php` sehingga aset font `/fonts/*.woff2` selalu dimuat dari origin aplikasi baik saat produksi maupun saat server Vite dev berjalan.
  3. Menyelaraskan `.type-display`, `.type-h1`, `.type-h2`, `.type-h3` pada `resources/css/app.css`, memperbaiki ruang kanan-bawah hero desktop terhadap karcis mini, serta merapatkan jarak katalog `/menu` agar tidak berongga di bawah bar filter sticky.
  4. Mengompresi gambar utama di `public/images/` (`hero-photo`, `hero-banner-full`, `bistro-patio-table`, `drinks-trio-banner`, `hero-iced-coffee-splash`) ke JPEG ringan + varian WebP & AVIF dan menambahkan `<source type="image/avif">` + `<source type="image/webp">` di `s01-hero.blade.php`.
  5. Memperbaiki aksesibilitas ke skor Lighthouse `100` (`Accessibility`, `Best Practices`, `SEO`): mengubah judul `<x-umco.karcis>` dari `<h3>` menjadi `<p>`, memakai `opacity` pada `reveal.js` dengan `aria: 'none'` pada `SplitText`, menandai wordmark dekoratif `aria-hidden="true"`, serta memperjelas `--cabai`/`--stempel` ke `#A3301A` dan angka aturan `text-[var(--tinta)]/55`.
  6. Hasil akhir: `app.js` `50.64 kB` gzip (`<= 60 kB`), `app.css` `16.65 kB` gzip (`<= 35 kB`), Lighthouse Desktop `99` (`A11y 100`, `BP 100`, `SEO 100`), Mobile `94` saat skrip dev toolbar Herd dikecualikan (`74` dengan skrip lokal Herd `herd-studio`), Playwright bersih (`200`, `0` overflow, `0` error/404 pada `375px`/`820px`/`1440px` + `reduced-motion`), dan `php vendor/bin/pest` hijau (`63 passed, 297 assertions`).

### Decision 16 (Revisi Estetika, Keamanan, & Backoffice 2026-11-15): Palet 3 Warna Arsitektural, Navbar 3-Zona, Penghapusan Biji/Dot-Matrix, & Filament Tanpa Bayangan/Tepi
- **Konteks:** Uji visual dan review lanjutan menuntut penghapusan kesan brutalism/AI (font tebal berlebihan, font titik-titik `Doto`, warna terlalu ramai seperti kuning neon/matcha/pink, label nomor `Section 01/02`, stiker karcis miring di hero, serta konsep Biji), perbaikan rasio media/foto-video di landing, penyederhanaan navbar, percepatan serta perapian `/admin` tanpa `box-shadow` dan garis tepi, serta penguatan keamanan backend.
- **Keputusan:**
  1. **Palet 3 warna ketat & tipografi editorial:** Kanvas hangat `#F7F5F0` / `#FFFFFF` / `#EFECE5` (Primer), tinta arang `#161615` / `#52514D` (Sekunder), dan kuningan hangat `#C4933F` / `#A87A2C` (Aksen); mengganti `Anybody` dan `Doto` dengan variable font self-hosted `Plus Jakarta Sans` + `Instrument Sans` (`Reenie Beanie` dipertahankan khusus tulisan tangan nota/karcis).
  2. **Navbar 3-zona & penghapusan Biji/Section-label:** Navbar di desktop, tablet, dan mobile disamakan (Kiri: `Menu`, `Cerita` | Tengah: `UMCO` | Kanan: `Reservasi`); `#biji`, altimeter `/cerita`, dan navigasi `BeanOriginResource` dihapus; label `"Section 01.."` ditiadakan; `footer-cutout.webp` dibersihkan dengan masker elips proporsional (`295x629`) serta animasi pelebaran wordmark `UMCO` diperlambat (`scrub: 2.4`).
  3. **Perbaikan root-cause inisialisasi JS & seksi kaya media:** `initSectionModules()` di `resources/js/app.js` diperbaiki agar mendukung `mod?.default || mod?.init`; jam WIB awal S03 dirender langsung dari server (`H.i`) bersama foto pendukung; S07 mengganti ilustrasi SVG gelas hijau dengan video `/videos/latte-art-loop.mp4`; S01 menampilkan video/foto hero bersih tanpa stiker miring; S06 dan S10 dipadukan dengan fotografi.
  4. **Keamanan & RBAC menyeluruh:** Menambahkan kebijakan model (`ReservationPolicy`, `MenuItemPolicy`, `MenuCategoryPolicy`, `ClosedDatePolicy`, `UserPolicy`, `OwnerOnlyPolicy`, `ActivityLogPolicy`), `SecurityHeaders` middleware, otorisasi eksplisit di halaman Filament (`ManageSettings`, `CrowdPatternPage`, `ReservationReports`) beserta `safeCsvCell()`, penguncian kolom sensitif & edit role diri sendiri di formulir Filament, validasi biner `@getimagesize` pada bukti bayar, rate limiting `RateLimiter` publik, serta penyamaran nama tamu di `/kalendar` publik menjadi `'Tamu UMCO'` bagi pengguna non-terverifikasi.
  5. **Filament `/admin` cepat & bersih tanpa shadow/border:** `filament-theme.css` ditulis ulang dengan permukaan tonal bebas `box-shadow` dan bebas border/ring; widget `PlaceholderAuditWidget` memakai kelas `.umco-audit-*` mandiri; `ReservationStatsWidget` digabung menjadi satu kueri agregat ber-cache 15 detik; `UpcomingReservationsWidget` memakai 5 kolom ringkas agar aksi WhatsApp tidak terpotong; polling notifikasi dilonggarkan ke `60s`.


### Decision 17 (Revisi Identitas Hijau UMCO, Navbar Full-Width, Reveal Stabil, & Bento Galeri 2026-11-15): Palet Hijau Botani, Navbar Tepi-ke-Tepi, Scroll Reveal Bebas Hilang, & Penghapusan Pin Tengah Halaman
- **Konteks:** Evaluasi lanjutan meminta pengembalian identitas warna **hijau UMCO** yang lebih elegan dan berpadu rapi di landing page maupun panel admin, perbaikan navbar agar bersih dari tepi ke tepi (bukan kapsul melayang), animasi gulir yang benar-benar mulus tanpa judul menghilang saat masuk layar atau footer terdorong ke luar viewport, serta penataan ulang grid S03, tab S04, dan bento S12 yang sebelumnya menyisakan 3 sel kosong dengan foto berulang.
- **Keputusan:**
  1. **Palet 3 warna hijau UMCO (Botanical Pine & Sage):** Primer batu hangat `#F6F5F0` / `#FFFFFF` / `#EAECE6`, sekunder arang botani `#171C19` / `#525B55`, dan aksen hijau UMCO `#2F6B4F` (`#23523C` hover, `#E4ECE6` tinta lembut, teks tombol putih `#FFFFFF`) diterapkan seragam pada `resources/css/app.css`, seluruh view/JS grafik publik, `filament-theme.css`, dan `AdminPanelProvider`.
  2. **Navbar arsitektural tepi-ke-tepi (`.nav-bar`):** Mengganti kapsul mengambang `.nav-pill` dengan bilah penuh atas (`fixed top-0 inset-x-0`) dengan gradien gelap terukur di atas hero dan transisi solid putih bersih saat digulir, tetap mempertahankan 3 zona (`Menu` & `Cerita` di kiri, `UMCO` di tengah tanpa kelas `uppercase`, tombol `Reservasi` hijau di kanan).
  3. **Perbaikan kestabilan animasi gulir & pengembalian Footer:** Memperbaiki `resources/js/app.js` membaca `document.body.dataset.smoothScroll` (bukan atribut di `<html>`) agar `Lenis` berjalan normal; merombak `reveal.js` memakai `start: 'top 93%'`, `end: 'bottom -5%'`, pengecekan awal elemen dalam viewport (`inViewport`), dan refresh pasca `document.fonts.ready` sehingga tidak ada teks `<h2>` atau footer yang hilang saat digulir; menghapus `pin: true` pada `signature-reel.js` (+2200px spacer penyebab lonjakan tata letak & ScrollTrigger meleset) dan menggantinya dengan horizontal reel interaktif + tombol panah & bilah progres hijau; melambatkan pelebaran wordmark `UMCO` di footer (`scaleX: 0.94 -> 1.14`, `scrub: 1.8`).
  4. **Kerapian seksi & pembersihan pelanggaran gaya:** S03 diubah ke grid hairline arsitektural tanpa `uppercase tracking-` di atas judul; S04 Nota mengaktifkan tab kategori lintas desktop/tablet/mobile agar tidak menumpuk ke bawah; S01 hero menaikkan video loop ke `opacity-100` dengan foto awal `opacity-95` dan scrim terfokus `h-3/5`; S12 Galeri dirapikan menjadi bento 6-tile penuh 12 sel tanpa baris yatim dan tanpa duplikasi foto.
  5. **Verifikasi akhir pasca-revisi:** `audit.php` bersih (`AUDIT_CLEAN`), produksi `npm run build` (`app.js` `50.65 kB` gzip, `app.css` `15.57 kB` gzip), Playwright penuh di `1440px`, `820px`, `375px`, serta `/menu`, `/cerita`, `/reservasi`, dan `/admin` lolos (`0` horizontal overflow, `0` error konsol, `0` elemen tersembunyi di viewport/footer, `boxShadow: none` & `borderTopWidth: 0px` di admin), serta `php vendor/bin/pest` lulus penuh (`67 passed, 328 assertions`).

### Decision 18 (Redesain Arsitektural Terinspirasi TARAQ 2026-11-15): Palet Hijau Zamrud Deep Forest, Hero & Footer Cutout Transparan Bergradasi, Header UM.CO, & 10 Seksi Modular Berbasis CMS
- **Konteks:** Revisi lanjutan meminta pendekatan tata letak editorial ala TARAQ yang bersih dan modern namun tetap berkarakter hijau UMCO (bukan hijau pastel atau hijau tua gelap kusam), penghapusan seksi pasif yang kurang fungsional (papan LED statis, garis cerita, simulator seduh, grafik jam ramai, peta bawah), pembaruan navbar, galeri portrait tak terbatas serta galeri diagonal bebas rasio, testimoni paralaks 3 kolom berlawanan, dan footer bergradasi dengan efek uap dingin serta animasi kata `UMCO` yang mengembang hanya saat digulir menuju bagian bawah.
- **Keputusan:**
  1. **Palet Deep Forest Jade & Oat Ivory:** Warna utama diubah ke Oat Ivory `#F6F4ED` / `#FFFFFF` / `#EBE7DB`, tinta Espresso Charcoal `#141916` / `#4D5751`, serta aksen hijau UMCO `#1B5E42` (`#134731` hover, `#DCEBE1` soft) dipadukan gradasi hero `#0D3526 -> #17533B -> #2C7254`, pre-footer CTA `#F6F4ED -> #95C5AB -> #226649`, dan footer `#226649 -> #19523A -> #1D5A40` (disinkronkan pula pada Filament `#1B5E42` tanpa shadow/border).
  2. **Tipografi Display `Barlow Condensed`:** Menambahkan self-hosted `barlow-condensed-{500,600,700}.woff2` pada `public/fonts/` untuk tajuk seksi terpusat yang tinggi dan bersih (`Plus Jakarta Sans` untuk isi, `Reenie Beanie` khusus Nota & Karcis).
  3. **Navbar `UM.CO` 3-Zona:** Kiri (`Menu`, `Galeri`, `Cerita`), Tengah (`UM.CO`), Kanan (`Hubungi Kami` ke WhatsApp kolaborasi + `Reservasi` ke `/reservasi`), lengkap dengan drawer ringkas pada layar `<640px`.
  4. **10 Seksi Beranda Fungsional Berbasis CMS & Cutout Transparan:**
     - `hero` (`.umco-hero-gradient` + cutout transparan `public/images/hero-cutout.webp` yang dapat diganti via `ManageSettings`).
     - `marquee` (`s02-collage-marquee`: kolase 5 foto paralaks + marquee editable admin).
     - `promo_banner` (`s03-feature-banner`: slider/banner lanskap dari resource `Banner` + tautan bebas admin + pita fitur).
     - `signature_reel` (`s04-signature-cards`: 5 kartu menu favorit portrait).
     - `menu_nota` (`s04-menu-nota`) & `karcis_teaser` (`s09-karcis-teaser` + akordeon ringkas `Ketentuan Singkat Kedai`).
     - `gallery_portrait` (`s07-portrait-gallery` + `gallery-infinite.js`: gulir otomatis tak terbatas, seret/geser, panah, lightbox).
     - `gallery_diagonal` (`s08-diagonal-gallery` + `gallery-diagonal.js`: dua jalur miring `-3deg` paralaks untuk semua rasio foto tanpa memotong aspek).
     - `testimonials` (`s11-testimonials` + `testimonials-parallax.js`: 3 kolom vertikal bergulir berlawanan dari model `Testimonial`).
     - `cta_footer` (`s10-cta`) & Footer gradasi dengan `public/images/footer-cutout.webp` (dibersihkan dari aset `docs/Kopi Susu Dingin di Atas Tatakan Batu.png`), lapisan uap dingin `.umco-vapor-wisp`, serta animasi scrub `UMCO` (`scaleX: 0.70 -> 1.12`) yang aktif saat footer masuk viewport; seluruh gambar fallback `s07`/`s08` dikonversi via `->toBase()` agar aman pada koleksi kosong maupun terisi.
  5. **Verifikasi Akhir:** `audit.php` (`AUDIT_CLEAN`), `npm run build` (`app.js` `50.59 kB` gzip <= 60 kB, `app.css` `16.32 kB` gzip), Playwright penuh lintas `1440px`, `820px`, `375px` serta `/admin` (`0` console/403 error, `0` horizontal overflow, `wordmarkBefore scaleX(0.7)` -> `wordmarkAfter scaleX(1.12)`, `portraitDragOk: true`), dan `php vendor/bin/pest` lulus seluruhnya (`67 passed, 334 assertions`).
