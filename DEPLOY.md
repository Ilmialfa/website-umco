# Panduan Deployment Produksi UM.CO House

Panduan terpadu untuk mendeploy website dan backoffice **UM.CO House** (Kampus 2 UMRI, Pekanbaru) ke lingkungan produksi (Shared Hosting / cPanel atau VPS Ubuntu + Nginx).

---

## 1. Persyaratan Sistem Produksi

- **PHP:** 8.3 atau 8.4 dengan ekstensi `bcmath`, `ctype`, `curl`, `dom`, `exif`, `fileinfo`, `filter`, `gd` (dukungan JPEG, PNG, WebP, dan disarankan AVIF), `hash`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `tokenizer`, `xml`.
- **Database:** MySQL 8.0+ atau MariaDB 10.6+ (encoding `utf8mb4_unicode_ci`).
- **Web Server:** Nginx 1.24+ atau Apache 2.4+ (`mod_rewrite` aktif).
- **Composer:** v2.7+.
- **Node.js & NPM:** v20 LTS / v22+ (untuk build aset Vite saat deploy).

---

## 2. Daftar Periksa Wajib Sebelum Tayang (`Go-Live Checklist`)

1. Arahkan *Document Root* web server ke folder **`public/`**, bukan root proyek.
2. Salin `.env.example` menjadi `.env` dan isi variabel produksi berikut:
   ```env
   APP_NAME="UM.CO House"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_TIMEZONE=Asia/Jakarta
   APP_URL=https://domain-umco.id

   APP_LOCALE=id
   APP_FALLBACK_LOCALE=id

   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   CACHE_STORE=file
   FILESYSTEM_DISK=public
   QUEUE_CONNECTION=database
   ```
3. Generate kunci aplikasi & pasang database:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   ```
4. Isi data awal website (hanya saat instalasi pertama kali):
   ```bash
   php artisan db:seed --class=Redesign2026Seeder --force
   ```
   > **Peringatan keamanan:** `Redesign2026Seeder` menanam akun contoh development (`password123`) untuk `owner@umco.house`, `admin@umco.house`, dan `kasir@umco.house`. Di server produksi, segera buat akun owner resmi dan hapus/ganti akun demo lewat `/admin/users` atau perintah di bawah.
5. Buat akun Owner produksi yang aman:
   ```bash
   php artisan umco:create-owner --name="Nama Owner" --email="owner@domain-umco.id"
   ```
   Jika `--password` dikosongkan, perintah akan membuat sandi acak kuat 16 karakter dan memaksa penggantian sandi pada login pertama di `/admin/profile`.
6. Tautkan folder storage publik dan bangun aset Vite produksi (pastikan tidak ada file `public/hot`):
   ```bash
   rm -f public/hot
   php artisan storage:link
   npm ci
   npm run build
   ```
7. Atur izin direktori agar proses PHP/web server dapat menulis log, cache, dan upload:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
8. Optimasi cache Laravel & Filament:
   ```bash
   php artisan optimize
   php artisan filament:optimize
   php artisan icons:cache
   ```

---

## 3. Penjadwalan Otomatis (Cron Scheduler) & Queue Worker

Aplikasi menjadwalkan tiga tugas latar belakang di `routes/console.php`:
- `reservations:remind` (tiap menit) — mengirim pengingat bel di backoffice sebelum jam kedatangan tamu.
- `instagram:sync` (tiap 15 menit) — menyinkronkan feed `@um.co_house` ke cache agar TTFB beranda tetap cepat tanpa request HTTP sinkron.
- `reservations:prune-proofs` (setiap hari 02.30 WIB) — menghapus file bukti transfer yang melewati masa retensi (`reservation.retention_days`, default 90 hari).

### Cron Job (cPanel & VPS)
Tambahkan satu baris cron berikut agar ketiga jadwal berjalan otomatis:
```cron
* * * * * cd /path/to/website-umco && php artisan schedule:run >> /dev/null 2>&1
```

### Worker Antrean (`queue:work`)
Jika mengaktifkan notifikasi WhatsApp otomatis Lapis 2 (`fonnte` atau `wablas`) di menu **Pengaturan Website & CMS**, atau memakai `QUEUE_CONNECTION=database`:
- **Lokal:** `php artisan queue:work --tries=3`
- **VPS Supervisor (`/etc/supervisor/conf.d/umco-worker.conf`):**
  ```ini
  [program:umco-worker]
  process_name=%(program_name)s_%(process_num)02d
  command=php /var/www/website-umco/artisan queue:work --sleep=3 --tries=3 --max-time=3600
  autostart=true
  autorestart=true
  stopasgroup=true
  killasgroup=true
  user=www-data
  numprocs=2
  redirect_stderr=true
  stdout_logfile=/var/www/website-umco/storage/logs/worker.log
  stopwaitsecs=3600
  ```
  > Jika token gateway WA dikosongkan (`gateway_driver = log`), sistem tidak menimbulkan error dan tombol WhatsApp Lapis 1 di `/admin` tetap berjalan normal.

---

## 4. Konfigurasi Web Server

### Shared Hosting (cPanel)
- **Domains &rarr; Document Root:** arahkan ke `/home/username/website-umco/public` (atau symlink `public_html -> website-umco/public`).
- Jangan biarkan `.env`, `storage/`, atau `database/` dapat diakses langsung dari browser.

### VPS Ubuntu + Nginx (`/etc/nginx/sites-available/umco`)
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name domain-umco.id www.domain-umco.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name domain-umco.id www.domain-umco.id;
    root /var/www/website-umco/public;

    ssl_certificate /etc/letsencrypt/live/domain-umco.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-umco.id/privkey.pem;

    index index.php;
    charset utf-8;
    client_max_body_size 12M;

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. Integrasi Instagram Graph API Resmi

Secara baku, beranda membaca cache feed atau galeri kurasi @um.co_house tanpa angka like/komentar buatan. Untuk menampilkan postingan Instagram terbaru secara resmi di server produksi:
1. Buat token Instagram Graph API / Basic Display yang valid untuk akun `@um.co_house`.
2. Buka `/admin/manage-settings` &rarr; tab **Kontak, Lokasi & Sosmed**, lalu tempel ke kolom **Instagram Graph API Token**.
3. Jalankan uji sinkronisasi manual sekali:
   ```bash
   php artisan instagram:sync --force
   ```

---

## 6. Pembaruan Kode & Backup Rutin

### Alur Update Kode Tanpa Downtime Panjang
```bash
php artisan down --render="errors::503"
git pull origin master
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize
php artisan icons:cache
php artisan queue:restart
php artisan up
```

### Backup Harian Database & Bukti Pembayaran
```bash
# Backup database MySQL
mysqldump -u umco_user -p website_umco | gzip > /backups/db_umco_$(date +\%Y\%m\%d).sql.gz

# Backup unggahan publik (foto menu, QRIS, denah, bukti transfer aktif)
tar -czf /backups/storage_public_$(date +\%Y\%m\%d).tar.gz storage/app/public/
```
