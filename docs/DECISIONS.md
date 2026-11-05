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
