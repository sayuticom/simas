# Deployment Laravel SIMAS di Nginx

Catatan ini untuk deployment domain `masjidkeren.my.id`.

## Struktur Hosting

- Project Laravel: `/home/webuser/simas`
- Public web root: `/home/webuser/public_html`
- `public_html/index.php` diarahkan ke:
  - `../simas/vendor/autoload.php`
  - `../simas/bootstrap/app.php`

## Environment

Isi `.env` di server, jangan commit file `.env`.

```env
APP_URL=https://masjidkeren.my.id
SIMAS_BASE_DOMAIN=masjidkeren.my.id
```

Website publik masjid akan memakai pola:

```text
{subdomain}.masjidkeren.my.id
```

## Nginx Rewrite Laravel

Karena server memakai Nginx, file `.htaccess` tidak berpengaruh. Agar route seperti `/login` berjalan tanpa `/index.php`, konfigurasi server block perlu memiliki rewrite Laravel:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Tanpa konfigurasi ini, URL yang mungkin masih berjalan sementara adalah:

```text
/index.php/login
```

Setelah Nginx diperbaiki, URL normal harus berjalan:

```text
/login
```

## Cache Setelah Deploy

Jalankan dari folder project:

```bash
cd /home/webuser/simas
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
```

## File Yang Tidak Boleh Dicomit

Jangan commit:

- `.env`
- `vendor/`
- `node_modules/`
- `storage/logs/`
- file `.sql`
- file `.zip`
