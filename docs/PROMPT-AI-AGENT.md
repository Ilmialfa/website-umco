# Prompt untuk AI Agent — Perbaikan Pra-Deploy website-umco

Salin seluruh blok di bawah ini ke AI agent Anda.

---

```text
Kamu adalah senior full-stack engineer (Laravel 13, Filament 4, Livewire, Tailwind 4, Vite, GSAP) dan UI/UX designer.
Tugasmu: menyiapkan codebase `website-umco` (kedai kopi UM.CO House, Kampus 2 UMRI Pekanbaru) agar siap diserahkan dan di-deploy klien.

SUMBER KEBENARAN
- Baca penuh `docs/AUDIT-PRA-DEPLOY.md` sebelum mengubah apa pun. Itu daftar kerjamu (ID P0-xx, P1-xx, P2-xx, P3-xx).
- Baca juga `docs/DECISIONS.md` (bagian terakhir) dan `DEPLOY.md` untuk konteks keputusan sebelumnya.
- Temuan berlabel [Perlu dicek] WAJIB kamu verifikasi dulu (grep / baca file / jalankan perintah). Jika ternyata tidak terbukti, catat "tidak terbukti" dan lewati.

URUTAN KERJA
1. P0 seluruhnya (timezone, favicon logo UMCO, data Instagram palsu, fetch Instagram sinkron, fitur Pola Jam Ramai yatim, section DB yatim, paksa ganti sandi owner, .env.example).
2. P1 backoffice (tema putih bersih & berwarna, sidebar bisa diciutkan, tabel reservasi tanpa scroll horizontal, dasbor informatif, semua field Pengaturan terpakai, teks publik hard-coded dipindah ke backoffice).
3. P2 pembersihan kode mati, aset berat, refactor komponen besar, perbaikan helper Settings, performa scroll.
4. P3 polish SEO, halaman error, dokumentasi deploy & README.
Commit per kelompok kecil dengan pesan jelas (mis. `fix(P0-01): set app timezone to Asia/Jakarta`).

ATURAN KERAS
- Jangan menghapus atau mengubah migrasi lama yang sudah dijalankan; buat migrasi baru bila skema perlu berubah.
- Jangan mengubah perilaku bisnis reservasi (kapasitas slot, DP, minimal belanja, aturan) kecuali yang disebut di audit. Logika tetap di `app/Actions/Reservations/*`, jangan diduplikasi ke view/Livewire.
- Jangan merusak tampilan publik yang sudah disetujui klien: hero gradien hijau + gedung UM.CO, navbar adaptif warna (transparan di hero & footer), logo `UM [emblem] CO` di tengah navbar, menu mobile editorial, marquee, banner 3 kartu, signature reel, karcis, testimoni rolling, FAQ, Instagram, CTA, footer wordmark UMCO besar dengan gelas kopi.
- Gaya visual publik: tanpa emoji, tanpa backdrop-blur, tanpa box-shadow dekoratif, tanpa border tipis di setiap elemen. Font: Barlow Condensed (display), Plus Jakarta Sans (teks), Reenie Beanie (tulisan tangan).
- Backoffice: kanvas putih bersih, teks kontras tinggi (WCAG AA), aksen berwarna konsisten untuk status (success/warning/danger/info/gray), sidebar bisa diciutkan dengan ikon jelas, tidak ada override CSS `!important` untuk latar panel. Gunakan tema Filament resmi via `->viteTheme()`.
- Semua teks dan gambar yang tampil di website publik harus bisa diedit dari backoffice (Settings atau PageSection.data), dengan fallback default. Field di backoffice yang tidak dipakai di website harus disambungkan atau dihapus.
- Bahasa UI: Bahasa Indonesia yang konsisten dan ramah awam.
- Jangan pernah menampilkan data palsu (like, komentar, rating, ulasan) sebagai data nyata.
- Keamanan: pertahankan SecurityHeaders/CSP, policy per role, rate limit, validasi upload. Jangan membuka kembali rute starter kit (register, forgot-password, settings).
- Jika menemukan masalah baru di luar audit, catat di bagian "Temuan Tambahan" pada `docs/AUDIT-PRA-DEPLOY.md` beserta perbaikannya.
- Bila ada keputusan produk yang ambigu (contoh P0-05 opsi A vs B), pilih opsi yang disarankan di audit dan catat alasannya di `docs/DECISIONS.md`.

VERIFIKASI WAJIB SEBELUM MENYATAKAN SELESAI
1. `php vendor/bin/pest` → 0 failed, 0 skipped. Tambahkan test untuk: timezone, fallback Instagram tanpa angka palsu, sinkronisasi page_sections ↔ partial, Settings unik (group,key), paksa ganti sandi.
2. `vendor/bin/pint --test` dan `vendor/bin/phpstan analyse` bersih (tidak ada error baru).
3. Hapus `public/hot` lalu `npm run build` sukses; laporkan ukuran gzip `app-*.js` dan CSS.
4. `php artisan route:list --except-vendor` tanpa rute starter.
5. Uji browser (Playwright jika tersedia) pada 1440, 1024, 768, 375 px:
   publik `/`, `/menu`, `/menu/{slug}`, `/galeri`, `/cerita`, `/reservasi` (langkah 1–5), `/reservasi/{code}`;
   admin `/admin/login`, `/admin` sebagai owner, admin, kasir (akun seed: owner@umco.house, admin@umco.house, kasir@umco.house).
   Syarat: 0 error console, 0 respons HTTP ≥ 400, 0 overflow horizontal, tabel reservasi tanpa scroll horizontal di 1280px, sidebar ciut berfungsi, favicon menampilkan emblem UMCO.
   Simpan screenshot dan periksa secara visual.
6. Lighthouse mobile beranda: Performance ≥ 85, CLS < 0,1.

LAPORAN AKHIR (Bahasa Indonesia)
- Tabel per ID audit: status (Selesai / Tidak terbukti / Ditunda + alasan), file yang diubah.
- Hasil persis setiap perintah verifikasi (angka test, ukuran bundle, skor Lighthouse).
- Daftar hal yang masih membutuhkan keputusan atau data dari klien (token Instagram Graph API, foto menu yang belum ada, QRIS resmi, ulasan Google asli, sandi owner produksi).
- Jangan menyatakan "semua lulus" kecuali output perintah menunjukkannya.
```

---

## Catatan untuk Anda (bukan untuk agent)

- Siapkan sebelum menjalankan agent: logo final (sudah di `docs/logo UMCO.png`), keputusan apakah grafik "Kapan sepi, kapan penuh" ingin ditampilkan lagi di beranda (rekomendasi: ya), dan apakah fitur passkey/2FA staf ingin dipertahankan (rekomendasi: hapus agar sederhana, kecuali klien minta).
- Data yang harus dari klien: token Instagram Graph API, gambar QRIS resmi, rekening bank, foto menu yang belum lengkap (`docs/Menu Umco - belum lengkap`), ulasan Google asli beserta izin.
