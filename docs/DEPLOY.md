# Panduan Deployment Produksi UM.CO House

Panduan ini berisi instruksi langkah-demi-langkah untuk melakukan deployment platform website **UM.CO House** ke berbagai lingkungan hosting produksi, mulai dari Shared Hosting (cPanel), Virtual Private Server (VPS Ubuntu/Debian), hingga Laravel Forge / Cloud.

---

## 1. Persyaratan Sistem Produksi

- **PHP:** Versi 8.4 (atau minimal PHP 8.2) dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `tokenizer`, `xml`.
- **Database:** MySQL 8.0+ atau MariaDB 10.5+ (Disarankan encoding `utf8mb4_unicode_ci`).
- **Web Server:** Nginx 1.24+ atau Apache 2.4+ (dengan modul `mod_rewrite`).
- **Composer:** Versi 2.7+.
- **Node.js & NPM:** Node v20 LTS atau v22+ (hanya diperlukan untuk tahapan build aset).

---

## 2. Deployment ke Shared Hosting (cPanel)

### Langkah 1: Pengaturan Dokumen Root Web Server
Pastikan folder root domain/subdomain Anda mengarah ke subdirektori **`public/`**, BUKAN ke root project:
- Di cPanel &rarr; **Domains** &rarr; Ubah *Document Root* ke: `/home/username/public_html/website-umco/public`
- Jika hosting Anda tidak mengizinkan mengubah document root dari `public_html`, buat symlink dari SSH terminal:
  ```bash
  rm -rf public_html
  ln -s website-umco/public public_html
  ```

### Langkah 2: Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env` dan sesuaikan parameter produksi:
```env
APP_NAME="UM.CO House"
APP_ENV=production
APP_KEY=base64:... # Generate dengan: php artisan key:generate
APP_DEBUG=false
APP_URL=https://umco.house

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_cpanel_db
DB_USERNAME=nama_cpanel_user
DB_PASSWORD=PasswordDatabaseKuat123!

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Langkah 3: Eksekusi Migrasi & Symlink Media
Jalankan perintah berikut melalui Terminal SSH cPanel:
```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force # (Hanya saat inisialisasi awal)
php artisan storage:link
```

### Langkah 4: Optimasi Cache Framework
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

### Langkah 5: Penjadwalan Cron Job (Laravel Scheduler)
Di menu cPanel &rarr; **Cron Jobs**, tambahkan satu baris cron setiap menit:
```cron
* * * * * cd /home/username/website-umco && php artisan schedule:run >> /dev/null 2>&1
```

---

## 3. Deployment ke VPS Linux (Ubuntu 24.04 / Nginx)

### A. Konfigurasi Nginx Server Block
Simpan konfigurasi berikut pada `/etc/nginx/sites-available/umco.house`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name umco.house www.umco.house;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name umco.house www.umco.house;
    root /var/www/website-umco/public;

    ssl_certificate /etc/letsencrypt/live/umco.house/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/umco.house/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    index index.php index.html;
    charset utf-8;

    # Gzip & Brotli Static Assets
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

### B. Konfigurasi Supervisor (Queue Worker)
Simpan pada `/etc/supervisor/conf.d/umco-worker.conf`:
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

---

## 4. Script CI/CD Otomatis (Zero-Downtime Deployment)

Gunakan skrip `deploy.sh` berikut di server untuk pembaruan kode rutin:

```bash
#!/bin/bash
set -e

echo "🚀 Memulai Deployment UM.CO House..."

# 1. Masuk mode maintenance
php artisan down --render="errors::503" --secret="umco-bypass-key"

# 2. Tarik kode terbaru dari Git
git pull origin master

# 3. Install composer dependencies tanpa dev packages
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Build frontend assets
npm ci
npm run build

# 5. Jalankan migrasi database
php artisan migrate --force

# 6. Segarkan seluruh cache aplikasi
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

# 7. Restart queue worker
php artisan queue:restart

# 8. Keluar dari mode maintenance
php artisan up

echo "✅ Deployment selesai dengan sukses!"
```

---

## 5. Prosedur Backup & Pemulihan (Disaster Recovery)

1. **Backup Database Harian:**
   ```bash
   mysqldump -u umco_user -p website_umco | gzip > /backups/db_umco_$(date +\%Y\%m\%d).sql.gz
   ```
2. **Backup Bukti Transfer Pembayaran (`storage/app/public/payment-proofs`):**
   ```bash
   tar -czf /backups/proofs_$(date +\%Y\%m\%d).tar.gz storage/app/public/payment-proofs/
   ```
