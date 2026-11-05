# ASUMSI DEFAULT: UM.CO HOUSE (UMCO)

Sesuai aturan main bagian 0 dan bagian 3.2 pada brief, seluruh data yang belum diberikan secara definitif oleh klien menggunakan **asumsi default** di bawah ini. Semua nilai asumsi ini disimpan dalam database dan **dapat diubah kapan saja oleh Admin** melalui panel CMS tanpa mengubah kode program.

---

## Tabel Asumsi Operasional & Bisnis

| Parameter | Nilai Default | Penjelasan & Rationale | Lokasi Pengaturan Admin |
|---|---|---|---|
| **Jam Buka** | `08:00 WIB` | Klien baru menyebutkan jam tutup (21.00 WIB). Mengingat lokasi di dalam kampus UMRI yang ramai sejak pagi untuk kuliah/nugas, jam 08.00 adalah asumsi paling realistis. | Pengaturan -> Jam Operasional |
| **Jam Tutup** | `21:00 WIB` | Data resmi dari klien. | Pengaturan -> Jam Operasional |
| **Interval Slot Reservasi** | `30 menit` | Memberikan fleksibilitas bagi pengunjung memilih waktu kedatangan tanpa membebani kapasitas dapur sekaligus. | Pengaturan -> Reservasi |
| **Slot Reservasi Terakhir** | `60 menit sebelum tutup (20:00 WIB)` | Memastikan pengunjung memiliki waktu minimal 1 jam untuk menikmati pesanan sebelum kafe tutup. | Pengaturan -> Reservasi |
| **Kapasitas Kursi per Slot** | `40 orang` | Total kapasitas gabungan indoor AC dan area outdoor H-building per rentang 30 menit. | Pengaturan -> Reservasi |
| **Waktu Minimum Pemesanan** | `60 menit sebelum kedatangan` | Memberi waktu bagi staf dan barista untuk menyiapkan meja dan pesanan menu (jika memilih Mode B). | Pengaturan -> Reservasi |
| **Jangkauan Hari Reservasi** | `Maksimal 14 hari ke depan` | Menghindari reservasi terlalu jauh di masa depan yang rawan *no-show*. | Pengaturan -> Reservasi |
| **Batas Maksimal Orang Online** | `12 orang` | Reservasi kelompok besar (>12 orang) membutuhkan pengaturan tata meja khusus dan diarahkan langsung ke WhatsApp Admin. | Pengaturan -> Reservasi |
| **Down Payment (DP) Mode B** | `30% dari subtotal (min. Rp 25.000, dibulatkan ke atas per Rp 1.000)` | Formula: `dp = max(25000, ceil((subtotal * 30 / 100) / 1000) * 1000)`. Mengurangi risiko pembatalan sepihak setelah bahan disiapkan. | Pengaturan -> Pembayaran |
| **Rekening Bank & QRIS** | `Belum terisi (kosong)` | Halaman pembayaran menampilkan notifikasi ramah: *"Informasi rekening & QRIS sedang diperbarui, silakan konfirmasi pembayaran via WhatsApp Admin"* sampai Admin mengisi data bank/QRIS di panel. | Pengaturan -> Pembayaran |
| **Nomor WhatsApp Admin Reservasi** | `082248001571` (Owner) | Dinormalisasi secara otomatis menjadi `6282248001571`. | Pengaturan -> Kontak & WhatsApp |
| **Zona Waktu Aplikasi** | `Asia/Jakarta (WIB)` | Seluruh perhitungan slot tanggal dan jam disesuaikan dengan zona waktu resmi Pekanbaru (WIB). | Konfigurasi Sistem |
| **Retensi Bukti Transfer** | `90 hari` | File bukti transfer yang diunggah disimpan di disk privat dan otomatis dibersihkan setelah 90 hari untuk efisiensi penyimpanan server. | Pengaturan -> Sistem |

---

## Log Pembaruan Nilai Asumsi
Setiap kali nilai asumsi diubah oleh Admin melalui CMS, sistem akan mencatat riwayat perubahan ke dalam `ActivityLog` (meliputi siapa pengubah, nilai lama, nilai baru, dan waktu perubahan).
