<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

# Reimbursement App

Aplikasi pengajuan **Reimbursement** berbasis Laravel API + Vue.js (Inertia.js). Membantu pegawai melakukan klaim pengeluaran secara cepat, terstruktur, dan aman. Mendukung limit per kategori, DataTables, email notifikasi asynchronous, dan activity log.

---

## 🚀 Alur Data Singkat

- User mengisi form pengajuan reimbursement via frontend.
- Frontend mengirim data ke API Laravel via AJAX (axios).
- Laravel memvalidasi, menyimpan data ke database, lalu push job ke Queue.
- Queue mengirim notifikasi email ke manager secara asynchronous.
- Frontend menampilkan DataTables berisi list reimbursement.

---

## 🛠️ Penjelasan Desain

### Laravel API + Vue.js + Inertia.js

- Laravel sebagai backend REST API yang efisien.
- Vue.js + Inertia.js membentuk Single Page Application (SPA).
- UI lebih cepat, smooth, dan interaktif karena minim full-page reload.

### Yajra DataTables

Digunakan untuk:

- Paginate data reimbursement secara efisien.
- Server-side filtering dan searching.
- Export ke Excel, PDF, CSV.
- Cocok untuk data tabular besar agar tetap cepat di frontend.

### Queue (Database Driver)

- Pengiriman email notifikasi ke manager menggunakan Laravel Queue dengan `QUEUE_CONNECTION=database`.
- Pengajuan reimbursement tidak terhambat proses kirim email.
- Email dikirim secara asynchronous (background job).
- Scalable untuk volume pengajuan tinggi, bahkan tanpa Redis.

### Limit Per Category

- Backend menghitung total reimbursement pegawai per kategori & per bulan.
- Jika pengajuan baru melebihi limit, API menolak request dengan pesan error berisi sisa limit.

### Activity Log

- Menggunakan **Spatie Laravel Activity Log**.
- Semua aksi penting (create, edit, delete) dicatat ke database.
- Membantu admin melakukan auditing & troubleshooting.

## Setup & Deploy

### Clone Project

- git clone https://github.com/username/reimbursement-app.git
- cd reimbursement-app
- composer install

Buat File .env lalu copy paste

<p>APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=127.0.0.1:5173,localhost:5173,127.0.0.1:8000,localhost:8000
SESSION_DOMAIN=127.0.0.1
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=271d622fa52176
MAIL_PASSWORD=75a7c10a19a152
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"</p>