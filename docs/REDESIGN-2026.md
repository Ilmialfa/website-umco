# UMCO — Spesifikasi Redesain Total 2026

> Dokumen kerja untuk agent pelaksana. Baca sampai habis sebelum menulis kode.
> Bahasa antarmuka: Indonesia. Stack tetap: Laravel 13 + Livewire + Alpine + Tailwind v4 + Filament v4 (admin) + GSAP + Vite.
> Status dokumen: final untuk dikerjakan. Hal yang masih perlu jawaban klien ada di bagian 16 dan **tidak boleh** menghambat pekerjaan (pakai nilai default yang tertulis).

---

## 0. Ringkasan dalam 10 baris

1. Semua halaman publik dibangun ulang dari nol. Tampilan lama (hijau gelap, "satu gedung dua suasana", denah huruf H) **dibuang total**, termasuk copy, gambar, dan komponennya.
2. Arah visual: **"kertas, tinta, dan karcis"**. Diambil dari isi `docs/umco-tmi.html` dan `docs/umco.html` (nota, karcis, LED, catatan tangan, seduh di layar, jam ramai), tapi warnanya diredam supaya tidak norak.
3. Landing page panjang, 13 section, **tidak ada dua section dengan pola tata letak yang sama**.
4. Reservasi tetap di halaman sendiri (`/reservasi`), tapi landing page punya "cicipan" interaktif yang mengisi karcis lalu melempar data ke halaman reservasi.
5. Aturan klien: minimal 2 orang, wajib pesan menu dengan minimal belanja bertingkat, bayar DP minimal 50% atau lunas, QRIS statis, upload bukti, permintaan tempat duduk lewat catatan.
6. Setelah submit: notifikasi ke WA UMCO, masuk dashboard admin dengan notifikasi lonceng yang bisa diklik, admin bisa chat pelanggan dari website (konfirmasi, ingatkan, follow up), dan ada pengingat otomatis 15 menit sebelum jam datang.
7. Admin bisa input reservasi walk-in/WA lewat form yang alurnya sama dengan website, termasuk upload bukti bayar.
8. Tiga tampilan yang dirancang terpisah: **HP, tablet, laptop/desktop**. Bukan sekadar menyusut.
9. Ringan: JS awal di bawah 60 KB gzip, animasi dimuat per section saat dibutuhkan, gambar AVIF/WebP responsif.
10. Semua konten publik bisa diubah oleh role **owner** dari Filament tanpa sentuh kode.

---

## 1. Larangan keras (baca dua kali)

Pekerjaan dianggap gagal kalau salah satu ini muncul:

| Dilarang | Ganti dengan |
|---|---|
| Emoji / emotikon di mana pun (UI, copy, seed data, notifikasi WA, admin) | Teks biasa, atau ikon garis Lucide/Heroicon ukuran kecil bila benar-benar perlu |
| Efek neon, glow, teks bercahaya, gradien ungu/biru, gradien pelangi | Warna solid dari palet di bagian 3 |
| `box-shadow` dekoratif pada kartu, tombol, navbar, gambar | Kontras warna permukaan. Satu-satunya pengecualian: "tumpukan kertas" karcis/nota memakai lapisan solid bergeser (lihat 6.3), bukan blur |
| Glassmorphism / `backdrop-blur` sebagai gaya | Permukaan solid |
| Grid 3 kartu "fitur" berikon bulat (pola template AI) | Tata letak editorial: daftar, tabel, tipografi besar, foto |
| Label kecil UPPERCASE berjarak lebar di atas setiap judul ("OUR MENU", "TENTANG KAMI") | Langsung judul, atau nomor section kecil `03` |
| Kartu bertepi + bayangan berjejer tanpa alasan | Konten langsung di atas latar, dipisah ruang kosong |
| Kata "satu gedung", "dua suasana", "Wing Barat/Timur", "huruf H", "zona indoor vs outdoor" | Hapus. UMCO dibicarakan sebagai satu tempat |
| Copy gaya AI: "secangkir kebahagiaan", "pengalaman tak terlupakan", "nikmati momen", "hadir untuk Anda", "revolusioner", "sempurna" | Lihat panduan copy bagian 4 |
| Lorem ipsum, foto stok barista senyum, ilustrasi kartun 3D | Foto menu asli dari `docs/Menu Umco - belum lengkap/`, placeholder tipografi bila belum ada |
| Three.js, WebGL, model 3D, partikel | CSS, SVG, canvas 2D ringan bila perlu |
| Progress bar tipis di atas layar saat pindah halaman | Skeleton (lihat 8.5) |

---

## 2. Teknologi dan strategi ringan

Teman klien (escalucgroup.com) memakai Vue + Inertia + GSAP + Lenis + Three.js + Lit + shadcn. Kita **ambil rasanya, bukan tumpukannya**:

| Mereka | Kita | Alasan |
|---|---|---|
| Vue + Inertia | Livewire + Alpine (sudah ada) | Tidak perlu SPA. `wire:navigate` sudah memberi perpindahan halaman tanpa reload |
| GSAP | GSAP 3 + ScrollTrigger + SplitText (sudah terpasang di `package.json`) | Mesin animasi utama |
| Lenis | Lenis (sudah terpasang) **hanya desktop pointer halus**, lerp 0.1, bisa dimatikan owner (`motion.smooth_scroll`, default **mati**) | Klien sebelumnya pernah minta smooth scroll dimatikan. Siapkan, tapi default off |
| Three.js | Tidak dipakai | Berat, tidak relevan untuk kafe |
| shadcn / Radix | Komponen Blade sendiri | Kontrol penuh atas estetika |

### 2.1 Anggaran performa (wajib, diukur di Lighthouse mobile, throttling 4G)

- LCP < 2,0 s, CLS < 0,05, INP < 200 ms, Lighthouse Performance ≥ 90 di HP.
- JS awal (`app.js`) ≤ 60 KB gzip. Modul section dimuat lewat `import()` saat elemen mendekati viewport.
- CSS ≤ 35 KB gzip.
- Font: self-host WOFF2 hasil subset Latin. Preload maksimal 2 file (display + teks). Font tangan dan LED dimuat lazy.
- Gambar: AVIF + WebP, `srcset` 480/800/1200/1600, `loading="lazy"` kecuali hero, `fetchpriority="high"` untuk hero, `width/height` selalu diisi.
- Video hero: hanya desktop dan tablet landscape, `preload="none"` + poster, maks 2,5 MB, tanpa audio. HP memakai foto.
- Tidak ada request ke Google Fonts saat runtime.

### 2.2 Pemuatan modul per section

```html
<section data-module="brew-playground" ...>
```

```js
// resources/js/app.js
const modules = {
  'hero': () => import('./sections/hero.js'),
  'menu-nota': () => import('./sections/menu-nota.js'),
  'signature-reel': () => import('./sections/signature-reel.js'),
  'brew-playground': () => import('./sections/brew-playground.js'),
  'crowd-chart': () => import('./sections/crowd-chart.js'),
  'karcis-teaser': () => import('./sections/karcis-teaser.js'),
  'scroll-meter': () => import('./sections/scroll-meter.js'),
};
const io = new IntersectionObserver((entries) => {
  entries.forEach(async (e) => {
    if (!e.isIntersecting) return;
    io.unobserve(e.target);
    const mod = await modules[e.target.dataset.module]?.();
    mod?.default?.(e.target);
  });
}, { rootMargin: '400px 0px' });
document.querySelectorAll('[data-module]').forEach((el) => io.observe(el));
```

Setiap modul wajib mengembalikan fungsi `destroy()` dan dipanggil ulang dengan benar setelah `livewire:navigated` (bersihkan ScrollTrigger lama dengan `ScrollTrigger.getAll().forEach(t => t.kill())` di `livewire:navigating`).

---

## 3. Sistem desain

### 3.1 Konsep

UMCO adalah tempat ngopi dan nongkrong mahasiswa. Dunianya: **kertas nota, karcis antrean, tinta pulpen, papan LED jam buka, coretan tangan kasir, gelas kopi susu**. Website harus terasa seperti barang cetak yang dirancang dengan teliti, bukan template aplikasi.

### 3.2 Palet warna

Warna diredam dari referensi (yang terlalu terang). Tidak ada hijau gelap.

| Token | Hex | Peran | Catatan |
|---|---|---|---|
| `--kertas` | `#F5F2EA` | Latar utama | Krem kertas, bukan putih |
| `--kertas-terang` | `#FCFBF7` | Permukaan nota/karcis, input | |
| `--tinta` | `#22263A` | Teks utama, tombol sekunder, footer | Hitam kebiruan seperti tinta pulpen |
| `--tinta-70` | `#4E5266` | Teks sekunder | Kontras ≥ 7:1 di atas kertas |
| `--tinta-40` | `#9396A3` | Placeholder, garis putus | Jangan untuk teks penting |
| `--garis` | `#DCD7CB` | Garis tipis di dalam konten (daftar menu, tabel) | Tidak dipakai di navbar |
| `--mentega` | `#EFC94C` | **Warna identitas UMCO**. CTA utama, sorotan, latar section nota | Kuning yang sudah diredam |
| `--mentega-tua` | `#D9AF2B` | Hover CTA | |
| `--matcha` | `#C8D5AE` | Latar satu section, chip terpilih | Pengingat identitas hijau UMCO, versi muda dan segar |
| `--susu-stroberi` | `#F0D2C9` | Kertas karcis | |
| `--stempel` | `#C4472E` | Nomor karcis, stempel "LUNAS"/"DP", pesan error | Dipakai sedikit sekali |

Aturan pakai:
- Satu section hanya boleh punya **satu** warna aksen di atas latarnya.
- Teks di atas `--mentega` dan `--matcha` selalu `--tinta`. Tidak ada teks putih di atas kuning.
- Rasio kasar seluruh halaman: 70% kertas, 15% tinta, 10% mentega, 5% sisanya.
- Footer memakai latar `--tinta` dengan teks `--kertas`.

### 3.3 Tipografi

Font dari referensi sudah disetujui klien. Self-host dari Google Fonts (unduh, subset, simpan di `public/fonts/`).

| Peran | Font | Pemakaian |
|---|---|---|
| Display | **Anybody** variable (`wdth` 50–150, `wght` 100–900) | Judul besar, nama menu, wordmark. Sumbu `wdth` dianimasikan |
| Teks | **Familjen Grotesk** variable (400–700) | Paragraf, UI, tombol, form |
| Tangan | **Reenie Beanie** | Hanya isian karcis/nota dan catatan pinggir. Maks 1–3 kemunculan per layar |
| LED | **Doto** | Hanya papan status "Hari ini di UMCO", nomor antrean, angka jam ramai |

Skala (pakai `clamp()`, nilai HP sengaja lebih kecil supaya compact):

| Token | HP (<640) | Tablet | Desktop | Pemakaian |
|---|---|---|---|---|
| `display-xl` | 56px | 96px | `clamp(120px, 13vw, 220px)` | Hero, wordmark footer. `wdth` 75, `wght` 800 |
| `display-l` | 40px | 64px | `clamp(72px, 7vw, 120px)` | Judul section |
| `display-m` | 28px | 36px | 48px | Nama menu di daftar, sub judul |
| `title` | 20px | 22px | 24px | Judul kecil |
| `body-l` | 17px | 18px | 20px | Lead paragraf |
| `body` | 15px | 16px | 17px | Teks biasa |
| `small` | 13px | 13px | 14px | Keterangan, label form |

Aturan:
- `line-height` display 0.9–0.95, teks 1.5. `letter-spacing` display `-0.01em` (jangan dirapatkan lebih; klien pernah komplain font terlalu dempet).
- Judul pakai sentence case. Tidak ada UPPERCASE kecuali wordmark "UMCO" dan nomor karcis.
- Semua harga dan angka: `font-variant-numeric: tabular-nums`. Format harga di landing: **"25,5 ribu"** (gaya referensi, lebih santai). Di reservasi, karcis, dan admin: **"Rp25.500"**.
- Paragraf maks 60 karakter per baris (`max-width: 34ch` sampai `60ch`).

### 3.4 Ruang dan grid

- Spasi kelipatan 4px. Jarak antar section: HP 72px, tablet 104px, desktop `clamp(128px, 12vw, 200px)`.
- Grid desktop 12 kolom, gutter 24px, margin luar `clamp(24px, 5vw, 80px)`, lebar konten maks 1440px. Foto boleh full-bleed.
- Grid tablet 8 kolom, gutter 20px, margin 32px.
- Grid HP 4 kolom, gutter 16px, margin 20px.

### 3.5 Bentuk

- Radius: tombol pil penuh; foto 4px (hampir kotak, terasa cetak); input 10px; karcis bergerigi (lihat 6.3).
- Garis: 1.5px `--tinta` untuk tombol outline dan bingkai karcis. Garis putus `2px dashed --tinta-40` untuk sobekan.
- Pembatas antar section tidak selalu lurus. Variasi yang dipakai (masing-masing maksimal 2 kali di landing):
  1. Tepi kertas sobek (SVG mask, path acak tapi statis).
  2. Perforasi (deret lingkaran kecil, `mask` radial-gradient).
  3. Gelombang landai sekali (satu lengkung, bukan wave berulang).
  4. Potong lurus biasa.

### 3.6 Ikon

Lucide (garis, stroke 1.5) via SVG sprite inline, hanya yang dipakai. Ukuran 18–20px. Tidak ada ikon di dalam lingkaran berwarna.

---

## 4. Panduan copywriting

Nada: **ngobrol dengan teman sekampus yang sopan**. Santai, jelas, pendek. Orang tua yang baca tetap paham.

Aturan:
- Pakai "kamu" dan "kami". Jangan "Anda", jangan "lo/gue".
- Kalimat pendek. Satu ide per kalimat. Maksimal 2 kalimat untuk deskripsi section.
- Kata sehari-hari: "pesan meja", "bayar di muka", "minimal belanja", "datang telat", "habis".
- Boleh sedikit jenaka, tidak boleh lebay. Jangan tanda seru berderet. Jangan emoji.
- Jangan pakai istilah sulit: "ekstraksi", "reservasi terkonfirmasi secara real-time", "pengalaman holistik". Kalau perlu istilah kopi, jelaskan di catatan kecil.
- Angka spesifik lebih baik dari kata sifat: "buka 07.00–22.00", "minimal 2 orang", bukan "jam operasional fleksibel".

Contoh pengganti:

| Hindari | Pakai |
|---|---|
| Nikmati pengalaman ngopi tak terlupakan bersama kami | Kopinya enak, kursinya enak diduduki lama. |
| Reservasi meja Anda sekarang juga! | Pesan meja dulu, biar datang langsung duduk. |
| Kami menyajikan kopi dengan biji pilihan terbaik | Bijinya dari petani Nusantara. Diseduh satu-satu. |
| Proses reservasi mudah dan cepat | Tiga langkah. Karcisnya terisi sendiri. |
| Silakan unggah bukti pembayaran Anda | Kirim foto bukti transfernya di sini. |
| Reservasi Anda berhasil dibuat | Karcismu sudah dicetak. Tinggal tunggu kami cek pembayarannya. |

Contoh copy siap pakai (boleh disesuaikan, jadikan default seeder):

- Hero: **"Ngopi dulu. Tugasnya nanti."** Sub: "Kopi Nusantara, makanan kenyang, dan meja yang betah ditempati sampai sore."
- Status board: "Hari ini di UMCO"
- Nota menu: **"Pilih menu, kami tulis di nota."** Sub: "Ketuk nama menunya. Totalnya dihitung sendiri."
- Signature: **"Yang paling sering dipesan."**
- Seduh: **"Coba seduh sendiri. Di layar saja."** Sub: "Geser tuasnya, lihat hasilnya. Cuma buat seru-seruan, tidak bisa dipesan."
- Jam ramai: **"Kapan sepi, kapan rame."** Sub: "Biar kamu tahu kapan enak nugas, kapan enak ngobrol."
- Karcis teaser: **"Pesan meja. Karcisnya terisi sendiri."**
- Aturan: **"Biar sama-sama enak."**
- Ulasan: **"Kata mereka yang sudah mampir."**
- Lokasi: **"Mampir ke sini."**
- Footer: "Sampai ketemu di UMCO."

---

## 5. Animasi dan interaksi

### 5.1 Prinsip

- Setiap elemen konten **fade in** saat masuk viewport dan **fade out pelan** saat keluar (scroll ke atas maupun ke bawah). Halus, tidak memantul.
- Animasi selalu melayani isi. Maksimal satu "momen besar" per section.
- Hormati `prefers-reduced-motion`: semua animasi diganti fade opacity 150 ms tanpa transform, pin dan parallax dimatikan.

### 5.2 Token gerak

```css
--ease-out: cubic-bezier(.22, 1, .36, 1);
--ease-in-out: cubic-bezier(.65, 0, .35, 1);
--dur-1: 180ms;  /* hover, tombol */
--dur-2: 420ms;  /* fade elemen kecil */
--dur-3: 800ms;  /* judul, foto */
--dur-4: 1200ms; /* momen besar */
```

### 5.3 Sistem atribut reveal (satu file `resources/js/motion/reveal.js`)

| Atribut | Efek masuk | Efek keluar |
|---|---|---|
| `data-reveal="fade"` | opacity 0→1 | 1→0 |
| `data-reveal="rise"` | opacity + y 24px→0 | kebalikan, y 0→-12px |
| `data-reveal="lines"` | SplitText per baris, masker, y 100%→0, stagger 80 ms | fade saja |
| `data-reveal="chars-wdth"` | Anybody `wdth` 50→75 + opacity per huruf, stagger 20 ms | fade |
| `data-reveal="mask"` | foto dibuka `clip-path: inset(100% 0 0 0)`→`inset(0)`, gambar scale 1.08→1 | fade |
| `data-reveal="stamp"` | scale 1.4→1, rotate -8°→-4°, opacity, `ease: back.out(2)` (satu-satunya yang memantul, khusus stempel) | tidak keluar |
| `data-reveal-stagger` di parent | anak-anaknya muncul berurutan 60 ms | |

Implementasi: ScrollTrigger dengan `start: "top 85%"`, `end: "bottom 15%"`, `toggleActions: "play reverse play reverse"` untuk fade keluar. Di HP kurangi jarak gerak menjadi setengah.

### 5.4 Interaksi kecil (micro)

- Tombol: hover warna berubah 180 ms, isi teks naik-turun (dua salinan teks, satu menggantikan yang lain). Tidak ada scale membesar.
- Link navbar: garis bawah tumbuh dari kiri.
- Nama menu di daftar desktop: hover mengubah `wdth` 75→110 (font melebar). Ini signature interaksi UMCO.
- Kursor kustom: **tidak** dipakai.

---

## 6. Komponen kunci

### 6.1 Navbar

Tiga perilaku:

**Desktop (≥1024)**
- Posisi atas hero: transparan, teks `--kertas` (atau `--tinta` bila hero terang, atur via `data-nav-theme` per section).
- Tata letak terpisah tiga bagian, mengambang dengan jarak 16px dari tepi:
  - Kiri: pil berisi link `Menu`, `Cerita`, `Lokasi`.
  - Tengah: wordmark "UMCO" (Anybody 800, `wdth` 90).
  - Kanan: indikator buka/tutup kecil (titik + "Buka sampai 22.00") lalu tombol pil `Pesan meja` warna `--mentega`.
- Setelah scroll 80px: ketiga pil mendapat latar solid `--kertas-terang`, tinggi 44px. **Tanpa border, tanpa shadow.**
- Scroll ke bawah: navbar menyembunyikan diri (translateY -120%). Scroll ke atas: muncul lagi. Tombol `Pesan meja` tetap tidak pernah ikut hilang di halaman reservasi (navbar sederhana di sana).

**Tablet (640–1023)**
- Satu pil penuh: wordmark kiri, `Pesan meja` dan tombol menu kanan. Link disembunyikan ke panel.

**HP (<640)**
- Bar tipis 56px: wordmark kiri, tombol `Pesan` kecil + tombol "Menu" (teks, bukan ikon hamburger) kanan.
- Panel menu: layar penuh latar `--mentega`, link besar Anybody 48px muncul satu-satu (stagger), di bawahnya papan status LED mini, alamat, dan tombol WhatsApp. Tutup dengan tombol "Tutup".

### 6.2 Tombol

- Primer: pil `--mentega`, teks `--tinta`, tinggi 48 (HP 44), padding 24.
- Sekunder: pil outline 1.5px `--tinta`, latar transparan.
- Teks: garis bawah, panah `→` sebagai karakter teks.
- Disabled: opacity 0.4, kursor not-allowed, tetap ada alasan tertulis di dekatnya ("Tambah menu 12 ribu lagi").

### 6.3 Karcis (komponen bintang)

Dipakai di landing teaser, halaman reservasi, halaman status, dan dicetak/diunduh.

Anatomi:
```
┌──────────────────────────────────┐  <- tepi atas & bawah bergerigi (mask radial)
│ UMCO                     No. 0127│  <- "No." warna --stempel, font Doto
│ Karcis meja                      │  <- Anybody 800 wdth 70
│ Hari    Sabtu, 15 Nov            │  <- label Familjen 13px, isian Reenie Beanie 26px
│ Jam     19.00                    │
│ Orang   3 orang                  │
│ Tempat  dekat jendela (permintaan)│
│ Nama    Dinda                    │
│ Pesanan 2 Aren Coffee, 1 Mie Lava│
│ Total   Rp96.500                 │
│ Bayar   DP 50% Rp48.500          │
│- - - - - - - - - - - - - - - - - │  <- garis sobek
│ [QR kode booking]  UMCO-261115-XK│  <- bagian sobekan (stub)
│ Tunjukkan ke kasir. Hangus 15    │
│ menit setelah jam pesan.         │
└──────────────────────────────────┘
```

Detail:
- Kertas `--susu-stroberi`, tinta `--tinta`. Di belakangnya dua lembar solid bergeser (`--matcha` 6px, `--mentega` 12px) memberi kesan tumpukan, **tanpa blur**.
- Rotasi diam -1.5° di desktop, 0° di HP.
- Isian muncul dengan efek tulisan tangan: teks ditampilkan karakter per karakter 25 ms (bukan typing cursor).
- Saat data berubah, isian lama dicoret (garis `--stempel` melintang 200 ms) lalu isian baru ditulis. Ini detail yang membuat terasa hidup.
- Selesai submit: stempel "MENUNGGU CEK" (status) jatuh dengan `data-reveal="stamp"`. Saat admin verifikasi, halaman status menampilkan stempel "DP DITERIMA" atau "LUNAS".
- Tombol di bawah karcis: `Simpan karcis` (unduh PNG via `html-to-image` yang di-lazy-load, atau versi server-side PDF bila lebih mudah), `Tambah ke kalender` (route `.ics` yang sudah ada), `Kirim ke WhatsApp UMCO`.
- Animasi "sobek": setelah submit sukses, stub bawah bergeser turun 12px dan berotasi 3°, seperti disobek.

### 6.4 Nota

Kertas `--kertas-terang`, tabel bergaris tinta (gaya nota toko seperti referensi), kolom Banyak / Nama barang / Harga / Jumlah, isian Reenie Beanie, "Terbilang" ditulis tangan (fungsi `terbilang()` Indonesia). Dipakai di landing (section 4) dan langkah menu reservasi.

### 6.5 Placeholder menu tanpa foto

Bukan foto stok. Kotak rasio 4:5 latar warna kategori (kopi: `--mentega` 40%, susu: `--susu-stroberi`, teh/mocktail: `--matcha`, makanan: `--kertas-terang` bergaris), nama menu Anybody besar terpotong di tepi kotak, teks kecil "foto menyusul". Field `image_path` kosong otomatis memakai ini.

### 6.6 Skeleton

Untuk perpindahan halaman (`wire:navigate`) dan pemuatan Livewire: blok `--garis` 40% dengan animasi shimmer opacity (bukan gradien bergerak), bentuknya meniru tata letak halaman tujuan (judul, 2 baris teks, blok foto).

### 6.7 Penghitung gulir

Pojok kanan bawah desktop: pil kecil "195 cm" yang bertambah sesuai jarak gulir (konversi px ke cm kasar, 1 cm = 38 px). Detail usil dari referensi. Disembunyikan di HP.

---

## 7. Peta halaman

| Route | Isi | Catatan |
|---|---|---|
| `/` | Landing panjang (bagian 8) | |
| `/menu` | Menu lengkap (bagian 9) | |
| `/cerita` | Tentang UMCO (bagian 10) | Redirect 301 dari `/about` |
| `/reservasi` | Alur pesan meja (bagian 11) | Menerima query `?tanggal=&jam=&orang=&items=` dari landing |
| `/reservasi/{code}` | Halaman status karcis, bisa upload ulang bukti bila ditolak | Akses dengan kode + 4 digit terakhir nomor WA |
| `/reservasi/calendar/{code}` | `.ics` (sudah ada, pertahankan) | |
| 404 | Karcis kosong "Halaman ini sudah hangus." + tombol pulang | |

Hapus route `/specimen` dari produksi. Hapus view dan aset lama yang tidak dipakai lagi.

---

## 8. Landing page — 13 section

Untuk tiap section: tujuan, isi, tata letak per perangkat, animasi, sumber data (editable). **Setiap section punya pola berbeda.** Owner bisa menyalakan/mematikan dan mengurutkan ulang section (lihat 13.2).

### 8.1 S01 Hero — "foto penuh, judul raksasa"

- Desktop: foto/video full-bleed 100svh. Wordmark "UMCO" raksasa (`display-xl`, warna `--kertas`) menempel di bawah kiri, judul "Ngopi dulu. Tugasnya nanti." di atasnya, sub + tombol `Pesan meja` dan `Lihat menu` di kanan bawah. Pojok kanan bawah: karcis kecil miring 6° yang "mengintip" setengah keluar layar, berisi "No. —— Karcis meja", klik membawa ke S09.
- Tablet: sama, wordmark lebih kecil, karcis mengintip disembunyikan.
- HP: foto 64svh (bukan full), teks di bawah foto di atas latar `--kertas`, bukan di atas foto (menghindari teks bertumpuk). Wordmark menyeberang batas foto dan latar (separuh di foto, separuh di kertas, `mix-blend-mode` tidak dipakai, cukup dua warna lewat clip).
- Animasi: foto `mask` dari bawah 1200 ms; wordmark `chars-wdth`; judul `lines`; gulir ke bawah membuat foto bergeser lebih lambat (parallax 0.85) dan wordmark melebar `wdth` 75→120 (scrub).
- Data: `PageSection hero` — judul, sub, foto desktop, foto HP, video, teks tombol.

### 8.2 S02 Pita menu berjalan — "marquee"

- Satu baris nama menu Anybody `display-m` bergerak pelan ke kiri, dipisah tanda bulat kecil. Latar `--tinta`, teks `--kertas`, satu nama acak berwarna `--mentega`.
- Semua perangkat sama, HP ukuran 24px. Pause saat hover/sentuh. Diputar dengan CSS animation (bukan GSAP) supaya murah.
- Pembatas atas: tepi sobek.
- Data: otomatis dari `MenuItem` aktif `is_favorite`.

### 8.3 S03 Hari ini di UMCO — "papan LED"

- Desktop: papan horizontal mirip formulir/papan pengumuman: empat kolom angka besar Doto: `BUKA` atau `TUTUP`, jam sekarang, perkiraan keramaian saat ini (`Sepi` / `Sedang` / `Rame`), jumlah menu habis hari ini. Di bawahnya satu kalimat catatan tangan dari owner ("Matcha baru datang, enak-enaknya hari ini").
- Tablet: 2×2.
- HP: satu baris yang bisa digeser horizontal (scroll-snap), tiap angka satu layar sempit.
- Animasi: angka berganti seperti papan flip (per digit, translateY masker) saat masuk viewport. Jam berdetak tiap menit.
- Data: jam buka dari Settings, keramaian dari `CrowdPattern` jam sekarang, habis dari `MenuItem.is_available=false`, catatan dari Settings `today.note`.

### 8.4 S04 Menu di nota — "daftar + nota lengket"

Adopsi langsung dari `umco-tmi.html`, warna diredam.

- Latar `--mentega`.
- Desktop: kiri 7 kolom daftar menu per kategori (kategori sebagai sub judul kecil, nama menu `display-m` Anybody, garis titik-titik, harga "25,5 ribu", tombol teks "Keterangan" membuka komposisi). Kanan 5 kolom nota sticky. Ketuk nama menu: item tertulis di nota dengan tulisan tangan, jumlah dihitung, "Terbilang" ditulis ulang. Tombol `Sobek nota` → membawa isi nota ke `/reservasi?items=...` (langkah menu sudah terisi). Kalimat kecil di samping: "Isi nota ikut tercetak di karcis mejamu."
- Tablet: daftar penuh, nota menjadi strip bawah sticky yang bisa dibuka.
- HP: kategori jadi tab geser horizontal. Daftar compact (nama 22px). Nota menjadi pil bawah "3 item · 74 ribu  Lihat nota" yang membuka bottom sheet.
- Tampilkan maksimal 4 kategori dan 4 menu per kategori di landing, lalu link "Lihat semua menu →".
- Data: `MenuItem`, `MenuCategory`, flag `show_on_home`.

### 8.5 S05 Yang paling sering dipesan — "reel foto horizontal"

- Desktop: section dipin, gulir vertikal menggerakkan deretan foto horizontal (6–8 foto menu asli, rasio 4:5, tinggi 70vh). Di bawah tiap foto: nomor `01`, nama menu besar, komposisi satu baris ("Gula aren, susu segar, kopi"), harga. Foto aktif di tengah tajam, yang lain opacity 0.5.
- Tablet: tanpa pin, scroll-snap horizontal dengan drag.
- HP: scroll-snap horizontal, foto 78vw, satu per layar, indikator garis kecil.
- Pembatas: potong lurus.
- Data: `MenuItem.is_favorite` urut `favorite_order`.

### 8.6 S06 Cerita singkat — "satu kalimat raksasa"

- Satu paragraf besar (`display-l`, 3–4 baris) yang kata-katanya menyala satu per satu saat digulir (scrub opacity 0.2→1 per kata). Dua foto kecil menyelip di dalam baris teks sebagai pil gambar inline (gaya referensi Coffee Bliss), misal foto cangkir dan foto suasana.
- Contoh: "UMCO itu tempat kopi Nusantara, nasi ayam sambal matah, dan [foto] meja panjang buat kamu yang betah lama. Datang sendiri atau rame-rame, kursinya cukup."
- HP: teks 30px, foto inline tetap ada tapi lebih kecil.
- Link "Baca ceritanya →" ke `/cerita`.
- Data: `PageSection story_line` (teks dengan token `[img:1]`, `[img:2]`).

### 8.7 S07 Coba seduh sendiri — "mainan interaktif"

Adopsi "Seduh dulu di layar" dari referensi.

- Latar `--matcha`.
- Desktop: kiri tiga tuas (range slider kustom): Kopi (10–25 g), Air panas (80–100 °C), Waktu (1–5 menit). Kanan gelas besar (SVG) yang warnanya berubah dari teh pucat ke hitam pekat sesuai hasil, dengan label tulisan tangan hasil: "Kemudaan. Kayak air cucian gelas." / "Pas. Ini yang kami cari." / "Pahit. Kamu begadang ya?". Di bawah: catatan jujur "Cuma buat seru-seruan. Menu ini tidak bisa dipesan."
- Tablet: gelas di atas, tuas di bawah.
- HP: gelas kecil di atas, tuas full width, thumb besar 28px agar mudah digeser jempol.
- Rumus sederhana (rasio kopi:air dan waktu) di modul JS, tidak perlu akurat ilmiah.
- Data: teks hasil bisa diedit owner (`PageSection brew` json `verdicts`).

### 8.8 S08 Kapan sepi, kapan rame — "grafik batang"

- Desktop: tab hari (Sen–Min). Grafik batang per jam 07–22 dari `CrowdPattern`, batang `--tinta`, batang jam sekarang `--mentega`. Label di atas batang tertinggi tulisan tangan "biasanya penuh". Hover/klik batang: muncul pil "Pesan meja jam 15.00 →" yang membawa ke `/reservasi?tanggal=..&jam=15:00`.
- Tablet: sama, batang lebih rapat.
- HP: grafik horizontal (jam di sumbu Y, batang ke kanan) supaya tidak sempit. Tab hari geser.
- Animasi: batang tumbuh dari bawah stagger 30 ms.
- Data: `CrowdPattern` (day_of_week, hour, level 0–100), diedit owner dalam tabel grid di admin.

### 8.9 S09 Pesan meja — "cicipan karcis"

Jembatan ke halaman reservasi. Bukan form lengkap.

- Latar `--kertas`, pembatas atas perforasi.
- Desktop: kiri: judul "Pesan meja. Karcisnya terisi sendiri." lalu dua pilihan cepat: chip hari (Hari ini, Besok, + 5 hari, hari tutup dicoret), stepper orang (mulai 2). Di bawahnya muncul info minimal belanja sesuai jumlah orang ("Untuk 3 orang, minimal belanja Rp100.000"). Kanan: karcis (komponen 6.3) yang terisi langsung. Tombol besar `Lanjut pilih jam, tempat, dan menu →`.
- Tablet: karcis di atas, pilihan di bawah.
- HP: pilihan dulu, karcis versi mini (hanya Hari, Orang, Minimal belanja) di bawahnya, tombol sticky bawah.
- Data dilempar lewat query string, halaman reservasi membaca dan mengisi langkah 1.

### 8.10 S10 Biar sama-sama enak — "daftar aturan bernomor"

Adopsi "Yang tidak kamu tanyakan / Peraturan kedai".

- Desktop: dua kolom. Kiri judul besar sticky. Kanan daftar bernomor besar (`01`–`07`) gaya akordion tanpa kotak: angka Anybody 64px, judul aturan, buka-tutup isi. Garis tipis antar item.
- Isi default:
  1. Minimal 2 orang per reservasi.
  2. Minimal belanja: 2 orang 50 ribu, 3 orang 100 ribu, 4 orang 125 ribu, 5 orang 150 ribu.
  3. Bayar di muka: DP minimal 50% atau langsung lunas, lewat QRIS.
  4. Mau duduk di mana? Tulis di catatan. Kami usahakan, tapi bukan janji.
  5. Karcis hangus 15 menit setelah jam pesan kalau belum datang.
  6. Batal? Kabari lewat WhatsApp paling lambat 2 jam sebelum jam pesan. (Kebijakan refund lihat bagian 16.)
  7. Datang tanpa pesan juga boleh, selama ada meja kosong.
- HP: satu kolom, angka 40px.
- Data: model `Rule` (judul, isi, urutan, aktif). Angka minimal belanja diambil otomatis dari `SpendTier` agar tidak beda dengan sistem.

### 8.11 S11 Kata mereka — "satu kutipan besar bergantian"

- Satu kutipan saja di layar, besar (`display-m`, maks 3 baris), nama dan sumber kecil di bawah ("Rani, mahasiswi, ulasan Google"). Berganti otomatis tiap 6 detik dengan fade silang, plus panah kiri/kanan teks. Angka rating Google besar di samping ("4,9 dari 86 ulasan").
- HP: kutipan 22px, geser untuk ganti.
- Tidak ada foto avatar palsu, tidak ada kartu.
- Data: `Testimonial is_featured`.

### 8.12 S12 Galeri — "mosaik editorial"

- Desktop: 7 foto susunan asimetris (satu besar 2×2, sisanya campur 1×1 dan 1×2), jarak 8px, caption tulisan tangan menempel di satu-dua foto. Klik membuka lightbox sederhana (dialog native `<dialog>`).
- Tablet: 2 kolom masonry.
- HP: 2 kolom, 6 foto, lalu "Lihat lebih banyak".
- Animasi: tiap foto `mask` berurutan.
- Data: `GalleryItem` (tanpa pengelompokan indoor/outdoor; ganti field `group` jadi tag bebas opsional).

### 8.13 S13 Lokasi + Footer — "alamat dan wordmark"

- Lokasi: kiri alamat besar, jam buka per hari (tabel kecil, hari ini ditebalkan), tombol `Buka di Google Maps` dan `Chat WhatsApp`. Kanan peta statis (gambar dari owner, bukan iframe Google Maps yang berat; iframe hanya dimuat setelah klik "Tampilkan peta interaktif").
- Footer latar `--tinta`: navigasi kecil, Instagram, hak cipta, lalu wordmark "UMCO" raksasa memenuhi lebar layar dengan **foto minuman tanpa latar (PNG/WebP transparan)** berdiri di depan wordmark (permintaan klien sebelumnya, foto bukan SVG). Wordmark membesar `wdth` 60→100 saat footer masuk.
- HP: wordmark 30vw, foto minuman lebih kecil, navigasi jadi dua kolom.
- Data: Settings `contact.*`, `hours.*`, `footer.cutout_image`.

---

## 9. Halaman Menu `/menu`

- Hero pendek: judul "Menu" `display-xl` dengan jumlah item kecil di sampingnya ("39 item"), kalimat "Harga sudah termasuk senyum kasir."
- Bar kontrol sticky di bawah navbar: tab kategori (geser di HP), kolom cari, toggle tampilan **Daftar** / **Foto**.
  - Daftar: gaya nota/menu cetak (nama besar, titik-titik, harga) — default di HP karena compact.
  - Foto: grid 4 kolom desktop, 3 tablet, 2 HP. Placeholder (6.5) untuk yang belum ada foto.
- Tiap item: nama, harga, komposisi singkat, pilihan suhu (Panas / Es) bila ada, varian (contoh Americano: Biasa 24 ribu, Aren 25 ribu, Anggur 25 ribu), label "Habis" bila `is_available=false` (nama dicoret garis `--stempel`), label "Baru".
- Klik item: panel detail (side sheet desktop, bottom sheet HP) dengan foto besar, komposisi, dan tombol "Tambah ke nota" yang tersimpan di `localStorage` dan terbawa ke reservasi.
- Filter dan cari jalan di sisi klien (Alpine), data diberikan sekali sebagai JSON. Tidak ada request per ketikan.

### 9.1 Data menu awal (seed dari foto asli)

Sumber: `docs/Menu Umco - belum lengkap/`. Nama file memuat nama, harga, dan komposisi. Buat seeder yang menyalin foto ke `storage/app/public/menu/`, mengonversi ke WebP/AVIF ukuran 480/800/1200 (pakai `intervention/image` atau `spatie/image`), dan mengisi data berikut:

| Kategori | Menu | Harga | Komposisi / catatan |
|---|---|---|---|
| Kopi hitam | Americano | 24.000 | Varian: Biasa (gula/tanpa gula) 24.000, Aren 25.000, Anggur 25.000 |
| Kopi hitam | Japanese | 22.000 | Seduh dingin gaya Jepang |
| Kopi hitam | V60 | 22.000 | Seduh manual |
| Kopi hitam | Vietnam Drip | 23.000 | |
| Kopi susu (panas/es) | Aren Coffee | 25.500 | Gula aren, susu segar, kopi |
| Kopi susu | Bliss Butter Cream | 25.500 | Sirup vanila, sirup butterscotch, susu segar, foam krimer, kopi, bubuk cokelat |
| Kopi susu | Butterscotch Coffee | 25.500 | Sirup butterscotch, susu segar, kopi |
| Kopi susu | Cafe Latte | 24.000 | Gula / tanpa gula |
| Kopi susu | Cappuccino | 24.000 | Susu segar, kopi |
| Kopi susu | Caramel Latte | 25.500 | Sirup karamel, susu segar, kopi |
| Kopi susu | Classic Coffee | 25.500 | |
| Kopi susu | Moccachino | 25.500 | Cokelat, susu segar, kopi |
| Kopi susu | Vanilla Latte | 25.500 | Sirup vanila, susu segar, kopi |
| Susu (panas/es) | Candy Pop | 25.500 | Sirup cotton candy, susu segar |
| Susu | Chocolate | 25.500 | Bubuk cokelat, susu segar |
| Susu | Matcha | 25.500 | Bubuk matcha, susu segar |
| Susu | Red Velvet | 25.500 | Bubuk red velvet, susu segar |
| Teh (panas/es) | Lemon Tea | 23.000 | |
| Teh | Lychee Tea | 23.000 | |
| Mocktail | Coffee Bree | 25.500 | Kopi, soda, Jamaica |
| Mocktail | Jade Fizzy | 25.500 | Sirup blue, jungle juice, mint, soda, vanila, krimer |
| Mocktail | Mocurry | 25.500 | Sirup anggur, sirup blue, soda, krimer, susu segar |
| Mocktail | Tropical Matcha | 25.000 | Air kelapa, selasih, matcha |
| Makanan | Mie Kuning Nusantara | 24.000 | |
| Makanan | Mie Lava UMCO | 22.000 | |
| Makanan | Nasi Ayam Remuk | 24.000 | |
| Makanan | Nasi Ayam Sambal Cabe Hijau | 24.000 | |
| Makanan | Nasi Ayam Sambal Kemangi | 24.000 | |
| Makanan | Nasi Ayam Sambal Matah | 24.000 | |
| Makanan | Nasi Goreng Cabe Hijau | 24.000 | |
| Camilan | Burger | 20.000 | Keju, daging, tomat, timun, selada, saus |
| Camilan | Classic Savory Platter | 23.000 | Risoles, pastel, samosa |
| Camilan | French Fries | 16.500 | |
| Camilan | Mix Platter | 23.000 | Kentang, sosis, nugget |
| Camilan | Pisang Goreng Cokelat Keju | 24.000 | |
| Camilan | Pisang Goreng Original | 16.500 | |
| Camilan | Sandwich | 13.000 | Keju, telur, tomat, timun, selada, saus |
| Manis | Roti Bakar Cokelat | 17.500 | Selai cokelat crunchy, roti |
| Manis | Roti Bakar Sarikaya | 16.500 | |

Cocokkan ulang nama file dengan isi folder kategori saat seeding (jangan percaya tabel ini 100%, folder adalah sumber kebenaran). Menu lama yang tidak ada di folder dan tidak punya foto: tandai `is_placeholder=true`, tetap tampil dengan placeholder, dan muncul di widget "Placeholder Detector" admin.

Harga berkoma (25,5 ribu) disimpan sebagai integer rupiah `25500`.

---

## 10. Halaman Cerita `/cerita`

Tanpa narasi gedung/arsitektur. Isi:

1. Pembuka: foto lebar + judul "Dari kampus, buat yang lagi butuh tempat."
2. Asal biji (adopsi "Asal biji kopi" referensi): gulir vertikal menaikkan "altimeter" angka Doto (0 → 1.650 mdpl) sementara nama daerah asal biji muncul di ketinggian masing-masing (Gayo, Kerinci, Toraja, dst., diedit owner). HP: daftar vertikal sederhana dengan angka ketinggian.
3. Tiga hal yang kami pegang (bukan kartu): tiga paragraf bernomor besar dalam satu kolom lebar, contoh "Kopi diseduh satu-satu", "Harga masuk akal buat kantong mahasiswa", "Boleh duduk lama".
4. Teman jajan (data `StreetStall`, bila aktif): daftar nama stan + menu jagoan + jam, gaya tabel nota.
5. Galeri tambahan + ajakan kolaborasi (komunitas kampus, acara kecil) dengan tombol WhatsApp memakai template `contact.collab_template`.

---

## 11. Reservasi `/reservasi` — alur lengkap

### 11.1 Aturan bisnis (wajib, diuji dengan Pest)

| Aturan | Nilai default | Editable owner |
|---|---|---|
| Minimal orang | 2 | Ya (`reservation.min_party`) |
| Maksimal orang online | 12 | Ya |
| Minimal belanja | Tabel `SpendTier`: 2 → 50.000; 3 → 100.000; 4 → 125.000; 5 → 150.000; 6+ → 150.000 + 25.000 per orang tambahan | Ya, tabel di admin |
| Pesan menu | **Wajib** untuk reservasi online | Ya (`reservation.menu_required`, default true) |
| Pembayaran | Pilih **DP** atau **Lunas** | |
| DP minimal | 50% dari subtotal, dibulatkan ke atas ke ribuan | Persentase editable, sistem menolak nilai < 50 |
| Metode bayar | QRIS statis (gambar dari owner) + rekening bank opsional | Ya |
| Bukti bayar | Wajib diunggah sebelum submit, JPG/PNG/WebP/HEIC ≤ 5 MB, dikompres di browser ke ≤ 1600px | |
| Permintaan tempat | Pilihan cepat + catatan bebas, sifatnya permintaan | Pilihan area editable (`SeatingOption`) |
| Interval slot | 30 menit, slot terakhir 60 menit sebelum tutup | Ya (sudah ada) |
| Kapasitas per slot | 40 kursi | Ya (sudah ada) |
| Pemesanan paling cepat | 60 menit dari sekarang | Ya (sudah ada) |
| Paling jauh | 14 hari | Ya (sudah ada) |
| Hangus | 15 menit setelah jam pesan | Ya (`reservation.grace_minutes`) |
| Hari tutup | Dari `ClosedDate` | Ya (sudah ada) |

Hitung minimal belanja dan DP **di server** (`app/Actions/Reservations/QuoteReservation.php`). Angka di browser hanya tampilan.

### 11.2 Langkah dan tata letak

Halaman berbentuk **langkah bergeser horizontal** (bukan scroll panjang), dengan karcis yang terus terisi.

Langkah:

1. **Kapan dan berapa orang** — chip tanggal (14 hari, hari tutup dicoret dengan alasan), grid jam (slot penuh dicoret, sisa kursi kecil di bawah jam bila < 8), stepper orang mulai 2. Info minimal belanja langsung muncul.
2. **Mau duduk di mana** — chip pilihan dari `SeatingOption` (contoh default: Dekat jendela, Dekat colokan, Sofa, Area luar, Bebas di mana saja) boleh pilih satu, plus kotak catatan "Ada permintaan lain? Ulang tahun, butuh colokan banyak, dsb." Kalimat jujur di bawah: "Kami usahakan sesuai permintaan, tapi tergantung meja yang kosong."
   - Opsional (bila owner mengunggah denah di Settings): gambar denah sederhana sebagai panduan visual, bukan pemilihan meja pasti.
3. **Pilih menu** — daftar menu ringkas dengan tab kategori dan cari, tombol `+`/`−`, pilihan suhu dan varian. Nota di samping (desktop) atau pil bawah (HP). Bar progres minimal belanja: "Rp74.000 dari minimal Rp100.000. Kurang Rp26.000." Tombol lanjut disabled sampai terpenuhi. Item dari landing/menu (`localStorage`/query) otomatis masuk.
4. **Data kamu** — nama, nomor WhatsApp (input `inputmode="tel"`, normalisasi `PhoneNumber::normalize` yang sudah ada **dipertahankan**: 0822, 62822, +62 822, 822, 620822 semua jadi 62822...), catatan tambahan. Tampilkan hasil normalisasi kecil di bawah input: "Kami hubungi ke +62 822-xxxx".
5. **Bayar** — pilih kartu teks besar `DP 50% · Rp48.500` atau `Lunas · Rp96.500`. Lalu QRIS besar dengan tombol `Simpan QRIS` (unduh gambar), nominal yang harus dibayar besar dan bisa disalin, rekening bank sebagai alternatif (bila diisi). Kotak unggah bukti (drag-drop desktop, tombol kamera/galeri HP) dengan pratinjau. Centang "Saya sudah baca aturan" yang link ke aturan.
6. **Selesai** — karcis lengkap tercetak, stempel "MENUNGGU CEK", kode booking, tombol `Kirim ke WhatsApp UMCO`, `Simpan karcis`, `Tambah ke kalender`, link halaman status.

Tata letak:
- **Desktop**: kiri 7 kolom area langkah (satu langkah per layar, geser horizontal 420 ms), kanan 5 kolom karcis sticky. Navigasi langkah di atas: "1 Kapan  2 Tempat  3 Menu  4 Data  5 Bayar", langkah yang sudah lewat bisa diklik.
- **Tablet**: satu kolom. Karcis jadi kartu ringkas yang menempel di atas area langkah dan bisa dilipat.
- **HP**: satu kolom compact, langkah memenuhi layar. Karcis menjadi **strip bawah sticky** (tinggi 64px: "Sab 15 Nov · 19.00 · 3 orang · Rp96.500  Lihat karcis") yang membuka bottom sheet karcis penuh. Tombol `Lanjut` menempel di bawah di atas strip. Input font ≥ 16px (mencegah zoom iOS).

Validasi pakai pesan manusiawi di bawah field, warna `--stempel`, tanpa ikon segitiga.

### 11.3 Setelah submit (server)

Satu transaksi DB lewat `app/Actions/Reservations/CreateReservation.php` (dipakai juga oleh admin):

1. Validasi ulang semua aturan, kunci slot (`lockForUpdate` hitung kapasitas).
2. Simpan `Reservation` + `ReservationItem` (snapshot nama dan harga saat itu).
3. Simpan bukti bayar di disk `public` folder `payment_proofs/` dengan nama acak.
4. Status: `status=pending`, `payment_status=proof_uploaded`, `payment_type=dp|full`, `source=web`.
5. Catat `ActivityLog`.
6. Kirim notifikasi database Filament ke semua user owner/admin/cashier (lihat 12.1).
7. Dispatch job `NotifyUmcoWhatsApp` (lihat 11.4).
8. Redirect ke langkah selesai.

### 11.4 Notifikasi ke WhatsApp UMCO

Nomor uji: **022 4800 1571** → simpan di Settings `contact.business_wa` sebagai `622248001571`.

> Penting: `PhoneNumber::normalize()` saat ini hanya menerima awalan 628 (seluler). Nomor UMCO adalah nomor tetap (022). Buat method terpisah `PhoneNumber::normalizeBusiness()` yang menerima nomor tetap (0xx → 62xx), dan pakai itu khusus untuk nomor bisnis. Jangan mengubah validasi nomor pelanggan.

Dua lapis, dibangun keduanya:

1. **Lapis 1 (pasti jalan, tanpa biaya)**: di langkah selesai, tombol `Kirim ke WhatsApp UMCO` membuka `https://wa.me/622248001571?text=...` berisi ringkasan reservasi (kode, nama, tanggal, jam, orang, tempat, pesanan, total, DP/lunas, link halaman admin). Setelah submit, tombol ini juga dibuka otomatis satu kali (dengan fallback tombol bila diblok browser).
2. **Lapis 2 (otomatis penuh, perlu layanan pihak ketiga)**: interface `App\Contracts\WhatsAppGateway` dengan driver `log` (default, menulis ke log), `fonnte`, dan `wablas`. Job `NotifyUmcoWhatsApp` mengirim pesan ke nomor bisnis lewat driver aktif. Owner mengisi token dan memilih driver di Settings. Bila token kosong, driver `log` dipakai dan tidak ada error.

Template pesan (tanpa emoji), editable owner di Settings `whatsapp_templates.new_reservation`:

```
Reservasi baru {kode}
Nama: {nama} ({wa})
Waktu: {hari}, {tanggal} jam {jam}
Orang: {jumlah}
Tempat: {tempat}
Pesanan: {pesanan}
Total: {total} | Bayar: {jenis_bayar} {nominal_bayar}
Cek bukti: {link_admin}
```

### 11.5 Halaman status `/reservasi/{code}`

- Verifikasi ringan: input 4 digit terakhir nomor WA (disimpan di session setelah benar).
- Menampilkan karcis dengan stempel status terbaru: `MENUNGGU CEK`, `DP DITERIMA`, `LUNAS`, `BUKTI DITOLAK`, `DIBATALKAN`, `SELESAI`.
- Bila ditolak: alasan dari admin + form unggah ulang bukti.
- Polling Livewire 30 detik agar stempel berubah sendiri saat admin verifikasi (dengan animasi stempel baru).

---

## 12. Admin (Filament) — tambahan dan perubahan

Tema admin yang sudah ada (krem, kuning, tanpa shadow, layout 2/3 + 1/3) dipertahankan. Sesuaikan warnanya ke token baru: latar `#F5F2EA`, aksen `#EFC94C`, sidebar aktif `--tinta` (bukan hijau).

### 12.1 Notifikasi lonceng

- Aktifkan `->databaseNotifications()` dan `->databaseNotificationsPolling('30s')` di `AdminPanelProvider`.
- Reservasi baru → notifikasi "Reservasi baru UMCO-261115-XK · 3 orang · Sab 19.00" dengan action `Lihat` ke halaman view reservasi, dan `Tandai dibaca`.
- Badge angka di menu "Daftar Reservasi" = jumlah `pending` (sudah ada, pertahankan).
- Opsional: bunyi pendek saat notifikasi baru (owner bisa matikan).

### 12.2 Pengingat 15 menit sebelum datang

- Command `reservations:remind` dijadwalkan `everyMinute()` di `routes/console.php`.
- Ambil reservasi `status in (pending, confirmed)`, `reminder_notified_at is null`, waktu datang antara sekarang dan sekarang + `reservation.remind_before_minutes` (default 15).
- Kirim notifikasi database ke staf: "15 menit lagi: Dinda, 3 orang, jam 19.00. Follow up?" dengan action `Chat WA` (link wa.me pelanggan berisi template pengingat) dan `Lihat`.
- Set `reminder_notified_at`.
- Juga tandai otomatis "terlambat" setelah lewat `grace_minutes` tanpa ditandai datang (notifikasi kedua: "Dinda belum datang, karcis hangus 5 menit lagi").
- Catat di `DEPLOY.md`: lokal jalankan `php artisan schedule:work`, produksi pasang cron `* * * * * php artisan schedule:run`.

### 12.3 Hubungi pelanggan dari admin

Di tabel dan halaman view reservasi, grup aksi WhatsApp (sudah ada, perluas) memakai nomor pelanggan yang sudah dinormalisasi:

| Aksi | Template default (editable) |
|---|---|
| Konfirmasi | "Halo {nama}, reservasi {kode} untuk {jumlah} orang hari {hari} {tanggal} jam {jam} sudah kami terima dan pembayarannya sudah kami cek. Sampai ketemu di UMCO." |
| Ingatkan | "Halo {nama}, mengingatkan reservasi {kode} jam {jam} hari ini. Mejamu kami tahan sampai {jam_hangus}. Kalau ada perubahan, kabari kami di sini ya." |
| Follow up bukti bayar | "Halo {nama}, bukti pembayaran untuk {kode} belum terbaca jelas. Boleh kirim ulang lewat link ini: {link_status}" |
| Kurang bayar | "Halo {nama}, nominal yang masuk {nominal_masuk}, kurang {kekurangan} dari DP {kode}." |
| Batal | "Halo {nama}, mohon maaf reservasi {kode} terpaksa kami batalkan karena {alasan}. Kami bantu atur ulang kalau kamu mau." |
| Chat bebas | (kosong) |

Aksi status cepat: `Verifikasi pembayaran` (pilih DP diterima / Lunas, isi nominal masuk), `Tolak bukti` (wajib alasan), `Tandai datang`, `Tandai selesai`, `Batalkan` (wajib alasan). Semua tercatat di `ActivityLog`.

Jangan mencatat ActivityLog di dalam closure `->url()` tabel (sudah pernah menyebabkan lambat). Catat lewat route redirect kecil `GET /admin/wa/{reservation}/{template}` yang mencatat lalu redirect ke wa.me.

### 12.4 Input reservasi dari admin (walk-in / WhatsApp)

- Halaman Filament `CreateReservation` diganti **Wizard** dengan langkah yang sama persis dengan website: Kapan & orang → Tempat → Menu (repeater item dengan pilih menu, varian, suhu, jumlah; subtotal hidup; validasi minimal belanja) → Data pelanggan (nomor dinormalisasi) → Pembayaran (DP/Lunas, nominal dihitung, unggah bukti, atau pilihan "Bayar tunai di kasir" khusus admin).
- Field tambahan admin: `source` (web / walk_in / whatsapp / telepon), dan toggle "Lewati aturan minimal" yang hanya boleh dipakai owner (tercatat di log).
- Memanggil `CreateReservation` action yang sama dengan website. Tidak boleh ada duplikasi logika.

### 12.5 Hak akses

| Kemampuan | Owner | Admin | Kasir |
|---|---|---|---|
| Reservasi: lihat, input, verifikasi, WA | Ya | Ya | Ya |
| Status menu habis/tersedia | Ya | Ya | Ya |
| Menu: tambah, ubah harga, foto | Ya | Ya | Tidak |
| Semua konten landing, halaman, section, aturan, jam ramai | Ya | Tidak | Tidak |
| Pengaturan pembayaran (QRIS, rekening, DP, minimal belanja) | Ya | Tidak | Tidak |
| Template WA, gateway WA | Ya | Tidak | Tidak |
| Pengguna & audit log | Ya | Tidak | Tidak |

Implementasikan dengan `canAccess()` / Policy per resource. Tambah test akses per role.

---

## 13. Model data

### 13.1 Perubahan tabel (migration baru, jangan edit migration lama)

`reservations` tambah:
- `payment_type` enum `dp|full|cash` (cash hanya dari admin)
- `amount_due` int (nominal yang harus dibayar sesuai pilihan)
- `amount_paid` int nullable (diisi admin saat verifikasi)
- `seating_option_id` nullable FK, `seating_note` text nullable
- `source` enum `web|walk_in|whatsapp|phone` default `web`
- `payment_proof_path` (sudah ada? pastikan), `payment_rejected_reason` nullable
- `reminder_notified_at`, `late_notified_at`, `arrived_at`, `cancelled_reason` nullable
- `min_spend_snapshot` int
- Index `(date, time, status)` (sudah ada), tambah index `reminder_notified_at`

`reservation_items` tambah: `variant_label`, `temperature` (`hot|ice|null`), `unit_price`, `name_snapshot`.

`menu_items` tambah: `ingredients` text, `has_temperature` bool, `variants` json (`[{label, price}]`), `show_on_home` bool, `is_placeholder` bool, `image_variants` json (path per ukuran).

Tabel baru:
- `spend_tiers`: `party_min`, `party_max` nullable, `min_spend`, `per_extra_person` nullable.
- `seating_options`: `label`, `sort`, `is_active`.
- `crowd_patterns`: `day_of_week` (1–7), `hour` (0–23), `level` (0–100). Unique (day, hour).
- `rules`: `title`, `body`, `sort`, `is_active`.
- `page_sections`: `page` (home/cerita/menu), `key`, `is_active`, `sort`, `heading`, `subheading`, `body` (text), `cta_label`, `cta_url`, `media` (json: desktop, mobile, video, poster), `data` (json bebas untuk isi khusus section seperti verdicts, origins, inline images).
- `bean_origins`: `name`, `region`, `altitude`, `note`, `sort`.
- `notifications` (tabel standar Laravel untuk database notifications).

`gallery_items.group`: ubah artinya menjadi tag bebas, hapus opsi indoor/outdoor dari form dan seeder.

### 13.2 Editor konten untuk owner

- Resource **Halaman & Section**: tabel `page_sections` per halaman dengan drag urut (`->reorderable('sort')`), toggle aktif, dan form yang berubah sesuai `key` (misal `hero` menampilkan upload foto desktop/HP/video; `brew` menampilkan repeater verdict).
- Resource **Aturan**, **Minimal Belanja**, **Pilihan Tempat Duduk**, **Asal Biji**: CRUD sederhana layout 2/3 + 1/3.
- Halaman **Jam Ramai**: grid 7 hari × 16 jam berisi input angka 0–100 (atau slider), tombol "Isi contoh".
- **Pengaturan**: tambahkan tab Pembayaran (QRIS upload, rekening repeater, persen DP min 50, toggle lunas), WhatsApp (nomor bisnis, gateway, token, semua template), Reservasi (min orang, jeda pengingat, hangus), Tampilan (smooth scroll on/off, gambar footer cutout, catatan hari ini).
- Semua field punya helper text bahasa sehari-hari.
- Setiap simpan konten: `Cache::forget` kunci terkait dan `php artisan view:clear` tidak perlu (pakai cache tag per section).

---

## 14. Tiga tampilan: aturan responsif

Breakpoint:
- **HP**: < 640px (rancang di 375 × 812 dan cek 360 × 740).
- **Tablet**: 640–1023px (rancang di 820 × 1180 portrait dan 1180 × 820 landscape; landscape memakai beberapa aturan desktop).
- **Desktop**: ≥ 1024px (rancang di 1440 × 900, cek 1280 × 720 dan 1920 × 1080).

Aturan umum:
- HP itu **compact**: ukuran font sesuai tabel 3.3, padding section 72px, gambar tidak lebih tinggi dari 64svh, tidak ada pin/scrub berat, maksimal satu animasi aktif per layar.
- Gunakan `svh/dvh` bukan `vh` untuk tinggi layar.
- Target sentuh ≥ 44px. Tombol utama di HP selalu bisa dijangkau jempol (bawah layar).
- Tidak ada teks yang bertumpuk dengan foto di HP kecuali judul hero yang sudah diatur kontrasnya.
- Uji orientasi tablet berputar tanpa layout rusak.
- Tabel ringkas per section:

| Section | HP | Tablet | Desktop |
|---|---|---|---|
| Hero | Foto 64svh, teks di bawah | Foto penuh, teks di atas | Foto/video penuh, karcis mengintip |
| Marquee | 24px | 32px | 48px |
| Papan LED | Geser horizontal | 2×2 | 4 kolom |
| Menu nota | Tab + bottom sheet nota | Daftar + strip nota | Daftar + nota sticky |
| Signature reel | Snap 78vw | Snap + drag | Pin horizontal |
| Kalimat raksasa | 30px | 48px | display-l |
| Seduh | Gelas atas, tuas bawah | Sama, lebih lega | Dua kolom |
| Jam ramai | Grafik horizontal | Batang vertikal rapat | Batang vertikal |
| Karcis teaser | Pilihan + karcis mini + tombol sticky | Karcis atas | Dua kolom |
| Aturan | Satu kolom | Satu kolom lebar | Judul sticky + daftar |
| Kutipan | Geser | Fade | Fade + rating besar |
| Galeri | 2 kolom 6 foto | 2 kolom masonry | Mosaik 7 foto |
| Lokasi + footer | Bertumpuk | 2 kolom | 2 kolom + wordmark penuh |

---

## 15. Rencana kerja bertahap

Kerjakan berurutan. Commit tiap fase. Jalankan `php vendor/bin/pest` sebelum commit.

**Fase 1 — Fondasi**
- Token CSS (bagian 3) di `resources/css/app.css` dengan `@theme` Tailwind v4, font self-host, layout publik baru, navbar 3 perilaku, footer, skeleton, sistem reveal, loader modul.
- Hapus komponen, view, gambar, dan copy lama yang tidak dipakai (termasuk semua yang menyebut wing/huruf H/dua suasana).
- Selesai bila: halaman kosong dengan navbar dan footer tampil benar di tiga ukuran, Lighthouse ≥ 95.

**Fase 2 — Data**
- Migration dan model bagian 13, seeder menu dari folder foto + konversi gambar, seeder section default dengan copy bagian 4, SpendTier, Rule, SeatingOption, CrowdPattern contoh.
- Selesai bila: `php artisan migrate:fresh --seed` jalan bersih, 39 menu dari folder terisi dengan foto.

**Fase 3 — Landing page**
- 13 section sesuai bagian 8, satu per satu, masing-masing diuji di tiga ukuran.

**Fase 4 — Menu & Cerita**

**Fase 5 — Reservasi publik**
- Action `QuoteReservation` & `CreateReservation`, Livewire baru, karcis, halaman status, WA lapis 1 dan 2.

**Fase 6 — Admin**
- Notifikasi, pengingat terjadwal, aksi WA via route log, wizard input admin, editor konten owner, hak akses.

**Fase 7 — Poles & audit**
- Cek daftar larangan bagian 1 (grep emoji: `[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]`, grep `shadow-`, `backdrop-blur`, `uppercase tracking`).
- Lighthouse HP dan desktop, uji `prefers-reduced-motion`, uji keyboard (fokus terlihat, tombol bisa Enter), alt teks foto, kontras.

### 15.1 Test wajib (Pest)

- Minimal belanja per jumlah orang sesuai tabel, termasuk 6+.
- DP dibulatkan ke atas ke ribuan, tidak bisa < 50%.
- Reservasi ditolak bila orang < 2, menu kosong, belanja kurang, tanpa bukti, slot penuh, hari tutup.
- Normalisasi nomor pelanggan (semua varian yang sudah ada tetap lulus) dan `normalizeBusiness('02248001571') === '622248001571'`.
- Submit membuat notifikasi database untuk staf dan men-dispatch job WA.
- Command pengingat hanya mengirim sekali per reservasi dan hanya dalam jendela 15 menit.
- Admin wizard memakai action yang sama dan bisa menyimpan dengan bukti.
- Akses per role (tabel 12.5).
- Semua halaman publik HTTP 200 dan tidak mengandung karakter emoji.

---

## 16. Pertanyaan terbuka untuk klien (jangan menghambat, pakai default)

1. Minimal belanja "3–5 orang 100–150 ribu" ditafsirkan per orang: 3 → 100k, 4 → 125k, 5 → 150k. Untuk 6 orang ke atas default +25k per orang. Perlu konfirmasi.
2. Kebijakan refund DP bila batal. Default tertulis: "DP tidak bisa dikembalikan, tapi bisa dipindah ke jadwal lain satu kali bila kabar paling lambat 2 jam sebelumnya."
3. Jam buka sebenarnya (referensi menulis 07.00–22.00, data lama 08.00–21.00). Default 08.00–22.00, editable.
4. Gateway WA otomatis (Fonnte/Wablas) berbayar. Default pakai lapis 1 (wa.me) sampai klien memberi token.
5. Nomor 022 4800 1571 adalah nomor uji. Ganti di Settings saat produksi.
6. Daftar asal biji kopi (daerah dan ketinggian) untuk halaman Cerita.
7. Apakah ada denah meja yang mau ditampilkan sebagai panduan (opsional langkah 2).

---

## 17. Definisi selesai

- Semua item bagian 1 bersih.
- Landing 13 section, tiap section beda pola, tampil rapi di 375px, 820px, 1440px.
- Alur reservasi lengkap sesuai aturan klien, karcis hidup, WA ke UMCO, notifikasi admin bisa diklik, pengingat 15 menit jalan, admin bisa input walk-in dengan bukti.
- Semua konten publik bisa diubah owner.
- Lighthouse HP ≥ 90, desktop ≥ 95. Semua test Pest hijau.
- `docs/DECISIONS.md` diperbarui dengan keputusan yang diambil selama pengerjaan.
