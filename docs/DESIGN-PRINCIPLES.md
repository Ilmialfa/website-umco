# PANDUAN PRINSIP DESAIN: UM.CO HOUSE (UMCO)

Dokumen ini merangkum sintesis 4 repositori desain standar industri (`unslop`, `taste-skill`, `hallmark`, dan `impeccable`) dan penerapannya secara spesifik pada website **UM.CO House (UMCO)**, Kampus UMRI, Pekanbaru.

---

## 1. unslop (Anti-Vibecoding & Anti-AI Slop)

> *"Slop spreads color and effects over the chrome... Craft keeps the chrome quiet and puts the richness into one or two moments of content that people remember."*

### Prinsip Utama & Penerapan pada UMCO:
1. **One Bright Element & Quiet Chrome**  
   - *Prinsip:* Navigasi, header, dan surface tetap tenang dengan warna netral bertint (oat cream & forest deep). Jangan ada tombol glowing, border neon, atau gradient ungu/biru.  
   - *Penerapan UMCO:* Chrome (header dan footer bar) tetap hening dan elegan. Kekayaan visual difokuskan pada foto sinematik kopi signature dan siluet denah gedung H.
2. **Spacing on a 4px Grid System**  
   - *Prinsip:* Seluruh margin, padding, dan gap harus kelipatan 4px (16, 20, 24, 32, 48, 64px). Dilarang arbitrary class aneh seperti `mt-[13px]`.  
   - *Penerapan UMCO:* Menggunakan skala spasi Tailwind v4 yang konsisten dengan padding kartu 20–24px dan jarak antar seksi fluid `clamp(5rem, 10vw, 8rem)`.
3. **Color Derived from the Product's World**  
   - *Prinsip:* Warna harus berasal dari dunia produknya, bukan tren template.  
   - *Penerapan UMCO:* Dunia UMCO adalah kopi kampus, kayu, dedaunan rindang UMRI, dan kertas catatan: palet Forest Deep (`#0F2A1D`), Espresso Green (`#1D3B2A`), Moss (`#3E5C45`), Oat Cream (`#F1ECDF`), Alabaster (`#FAF7F0`), dan aksen krema kopi (`#D3B98C`).
4. **Copy without AI-Hype (No Slop Copy)**  
   - *Prinsip:* Hindari kata-kata basi seperti "secangkir kebahagiaan", "supercharge", "revolutionary", "seamless". Tulis copy spesifik tentang apa yang dijual dan untuk siapa.  
   - *Penerapan UMCO:* Copy bernada santai ala obrolan kampus Pekanbaru yang hangat, menonjolkan indoor AC untuk nugas/skripsi dan outdoor street food untuk santai malam.
5. **Nested Radius & Structural Rhythm**  
   - *Prinsip:* Radius elemen dalam harus lebih kecil dari radius luar (`radius_inner = radius_outer - padding`). Hindari membuat semua elemen berbentuk pil identik.  
   - *Penerapan UMCO:* Tombol aksi menggunakan pil halus, kartu menu sedikit melengkung (radius 12px), dan kontainer foto memiliki lengkung asimetris yang serasi.
6. **Sentence Case & Tabular Numbers**  
   - *Prinsip:* Hindari label uppercase dengan tracking lebar berlebihan di atas setiap judul (`NAVIGATION`, `OUR FEATURES`). Gunakan sentence case. Angka harga wajib `tabular-nums`.  
   - *Penerapan UMCO:* Format harga `Rp 28.000` dengan font tabular agar digit angka sejajar rapi.

---

## 2. taste-skill (Editorial Typography & High-Craft Place Identity)

> *"A cafe or guesthouse is a 'place'—photographs lead, flat grounds, honest type, no tech shader noise."*

### Prinsip Utama & Penerapan pada UMCO:
1. **Place Business Mode: Photography Leads**  
   - *Prinsip:* Bisnis tempat fisik (kafe, bakery, resto) harus dipimpin oleh fotografi asli dan suasana nyata, bukan ilustrasi kartun atau efek 3D tech.  
   - *Penerapan UMCO:* Menampilkan galeri visual indoor ber-AC yang nyaman untuk laptopan, dan area outdoor dengan lampu hangat dan stan street food.
2. **No Image Captions Below Frames**  
   - *Prinsip:* Dilarang menempelkan teks caption di bawah gambar yang merusak ritme visual editorial. Dokumentasi atau kredit diletakkan di footer.  
   - *Penerapan UMCO:* Foto disajikan bersih, keterangan foto diintegrasikan ke dalam teks narasi editorial atau overlay minimalis.
3. **One Section per Screen & Breathing Room**  
   - *Prinsip:* Setiap seksi di desktop memiliki ruang napas lapang, tanpa elemen yang bertumpukan secara sempit.  
   - *Penerapan UMCO:* Tata letak asimetris dengan whitespace luas, mengundang pembaca untuk menikmati konten seksi per seksi.
4. **Quiet Micro-Interactions (Motion with Taste)**  
   - *Prinsip:* Animasi halus: tombol step & press, hover foto zoom 1.02–1.03 di dalam frame terpotong (`overflow: hidden`), underline link yang terbit halus. Dilarang tilt 3D berlebihan.  
   - *Penerapan UMCO:* Interaksi kartu menu dan galeri dirancang halus dengan transisi CSS cepat (200–300ms) tanpa efek bergetar.
5. **Variety of Compositions (Anti-Generic Grid)**  
   - *Prinsip:* Dilarang menggunakan grid kartu 3-kolom seragam di setiap bagian halaman.  
   - *Penerapan UMCO:* Variasi tampilan menu: item unggulan berfoto asimetris di bagian atas, diikuti daftar menu dengan garis titik penghubung (dot leaders) yang klasik dan rapi.
6. **Clear Concise Text (60–80 Words Per Viewport)**  
   - *Prinsip:* Pengunjung kafe mencari informasi cepat dan rasa ingin tahu tentang suasana, bukan esai panjang bertele-tele.  
   - *Penerapan UMCO:* Narasi ringkas, to the point, dan langsung mengarahkan pada aksi nyata (Lihat Menu, Reservasi Meja, WhatsApp).

---

## 3. hallmark (The Floor & Production Rigor)

> *"A hard floor against the defaults every model was trained into. Above the floor the page is yours."*

### Prinsip Utama & Penerapan pada UMCO:
1. **The Floor: Tokenized Styling Only**  
   - *Prinsip:* Semua warna, font, dan spasi dideklarasikan melalui token di `@theme` CSS. Dilarang menyisipkan hex warna liar di tengah template.  
   - *Penerapan UMCO:* Seluruh warna merek UMCO diatur dalam konfigurasi Tailwind v4 `@theme` di `resources/css/app.css`.
2. **Never `transition: all` & Never Animate Layout Dimensions**  
   - *Prinsip:* Animasikan hanya `transform` dan `opacity`. Jangan pernah menganimasikan `width`, `height`, `top`, `left`, atau `filter` berat.  
   - *Penerapan UMCO:* Transisi hover dan transisi navigasi hanya memanipulasi translate, scale, dan opacity demi menjaga 60fps di HP kelas menengah.
3. **Absolute Prohibition of AI Tells**  
   - *Prinsip:* Dilarang kartu di dalam kartu bertingkat, gradien ungu ke biru, ikon emoji dekoratif, status dot tanpa data nyata.  
   - *Penerapan UMCO:* Ikon menggunakan set Heroicons/Lucide SVG bergaris 1.5px, label status berbasis badge teks tenang ("Favorit", "Baru", "Habis").
4. **Truth in Metrics & Content (No Fake Data)**  
   - *Prinsip:* Dilarang mengarang jumlah pelanggan ("10,000+ Happy Customers") atau ulasan fiktif yang diklaim sebagai ulasan Google asli.  
   - *Penerapan UMCO:* Data rating nyata 4.9 (86+ ulasan) dicantumkan dari setting database. Testimoni contoh diberi tanda `is_sample = true` dan dilabeli transparan di panel admin.
5. **Hero Fold Visibility Guarantee**  
   - *Prinsip:* Headline, deskripsi 1-2 kalimat, dan tombol CTA utama harus terlihat utuh di viewport 1280x800 dan di layar HP tanpa perlu scroll.  
   - *Penerapan UMCO:* Desain hero mobile mengutamakan teks dan CTA di bagian atas dengan visual sinematik terintegrasi di bawahnya.
6. **Accessibility & Focus-Visible**  
   - *Prinsip:* Seluruh kontrol interaktif wajib memiliki `:focus-visible` kontras tinggi dan target sentuh minimal 44px.  
   - *Penerapan UMCO:* Navigasi, input formulir, dan tombol reservasi diuji untuk kemudahan ketukan di ponsel dan navigasi keyboard.

---

## 4. impeccable (Surface Modes & Zero-Defect Craft)

> *"Scale ambition to the surface mode: Persuade & Experience for public landing, Operate for admin dashboard."*

### Prinsip Utama & Penerapan pada UMCO:
1. **Surface Mode Separation (Dual Personality)**  
   - *Prinsip:* Halaman publik adalah mode **Persuade & Experience** (kehangatan visual, tipografi editorial, atmosfer kafe). Halaman Admin adalah mode **Operate** (kejelasan tugas kasir/owner, efisiensi waktu, tanpa distraksi visual).  
   - *Penerapan UMCO:* Halaman publik bernuansa Oat Cream & Forest Deep; panel admin Filament terorganisir rapi dengan aksi 1-klik WhatsApp yang mempercepat pekerjaan kasir.
2. **State Completeness on All Controls**  
   - *Prinsip:* Setiap input dan tombol harus memiliki rancangan untuk 8 state: default, hover, active/press, focus-visible, disabled, loading, valid, dan invalid.  
   - *Penerapan UMCO:* Formulir reservasi meja dan pemilihan menu memiliki validasi instan, status sold-out yang jelas, dan indikator loading saat kalkulasi DP.
3. **Smart WhatsApp Engine with Zero Typing Friction**  
   - *Prinsip:* Desain operasional harus menghilangkan langkah repetitif bagi staf kafe.  
   - *Penerapan UMCO:* Tombol aksi WhatsApp di setiap baris reservasi langsung membuka chat dengan template yang terisi nama, tanggal, jam, dan kode booking tanpa perlu mengetik ulang.
4. **Bulletproof Mobile Ergonomics**  
   - *Prinsip:* Mayoritas pengunjung kafe membuka website dari smartphone. Satu kolom untuk formulir, keyboard input yang tepat (`type="tel"` untuk WhatsApp, datepicker native/custom yang ramah sentuhan).  
   - *Penerapan UMCO:* Halaman `/reservasi` dirancang vertikal satu kolom dengan ukuran tap target besar dan ringkasan biaya yang selalu terlihat.
5. **Architectural Signature: The H-Plan Motif**  
   - *Prinsip:* Temukan keunikan nyata dari tempat tersebut dan jadikan aksen khas yang tak bisa ditiru kafe lain.  
   - *Penerapan UMCO:* Denah gedung huruf H khas Kampus UMRI diangkat sebagai motif mask visual, seksi interaktif pemilih area (Indoor AC vs Outdoor Street Food), dan aksen pembatas seksi.
