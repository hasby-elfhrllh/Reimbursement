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

-----

Aplikasi **Reimbursement App** adalah sistem pengajuan klaim pengeluaran yang efisien dan terstruktur, dibangun dengan kombinasi Laravel API dan Vue.js (menggunakan Inertia.js). Aplikasi ini dirancang untuk memudahkan pegawai dalam mengajukan klaim reimbursement, memastikan proses yang cepat, aman, dan tercatat dengan baik.

-----

## Fitur Utama

Aplikasi ini dilengkapi dengan berbagai fitur penting untuk mendukung proses reimbursement:

  * **Pengajuan Reimbursement Cepat**: Form intuitif untuk pengajuan klaim yang mudah.
  * **Limit Per Kategori**: Sistem validasi otomatis untuk memastikan pengajuan tidak melebihi limit yang ditentukan per kategori pengeluaran.
  * **Tampilan Data Interaktif**: Integrasi dengan Yajra DataTables untuk tampilan data tabular yang efisien, lengkap dengan fitur paginasi, pencarian, dan penyaringan.
  * **Notifikasi Email Asynchronous**: Pengiriman notifikasi email ke manajer secara otomatis melalui Laravel Queue, tanpa menghambat alur pengajuan.
  * **Pencatatan Aktivitas (Activity Log)**: Semua aksi penting dalam aplikasi tercatat untuk tujuan audit dan pelacakan.

-----

## 🚀 Alur Data Singkat

Berikut adalah gambaran singkat mengenai alur data dalam aplikasi ini:

1.  **Pengisian Form**: Pengguna mengisi form pengajuan reimbursement melalui antarmuka frontend.
2.  **Pengiriman Data**: Frontend mengirimkan data pengajuan ke API Laravel menggunakan AJAX (axios).
3.  **Validasi & Penyimpanan**: Laravel melakukan validasi data, menyimpannya ke database, dan kemudian mendorong tugas (job) ke dalam **Queue**.
4.  **Notifikasi Asynchronous**: Queue mengirimkan notifikasi email kepada manajer secara asynchronous di latar belakang.
5.  **Tampilan Data**: Frontend menampilkan daftar pengajuan reimbursement dalam bentuk DataTables yang interaktif.

-----

## 🛠️ Penjelasan Desain Teknis

### Laravel API + Vue.js + Inertia.js

  * **Laravel** berperan sebagai *backend* yang menyediakan REST API yang efisien dan kuat.
  * **Vue.js** dipadukan dengan **Inertia.js** untuk menciptakan *Single Page Application* (SPA). Pendekatan ini menghasilkan antarmuka pengguna yang lebih cepat, mulus, dan interaktif karena meminimalkan *full-page reload*.

### Yajra DataTables

Digunakan untuk mengelola dan menampilkan data tabular secara efisien, dengan fitur-fitur seperti:

  * **Paginasi**: Mengelola data dalam jumlah besar tanpa membebani *frontend*.
  * ***Server-side Filtering & Searching***: Pemfilteran dan pencarian data dilakukan di sisi *server* untuk kinerja optimal.
  * **Ekspor Data**: Kemampuan untuk mengekspor data ke format Excel, PDF, atau CSV.
  * Sangat cocok untuk dataset besar agar tetap responsif di *frontend*.

### Queue (Database Driver)

  * Pengiriman notifikasi email ke manajer diimplementasikan menggunakan **Laravel Queue** dengan `QUEUE_CONNECTION=database`.
  * Ini memastikan bahwa proses pengajuan reimbursement tidak terhambat oleh waktu yang dibutuhkan untuk mengirim email.
  * Email dikirim sebagai *background job* secara asynchronous, yang menjadikan sistem lebih *scalable* bahkan tanpa *driver* Redis.

### Limit Per Category

  * *Backend* secara otomatis menghitung total klaim reimbursement yang diajukan oleh seorang pegawai per kategori dan per bulan.
  * Jika pengajuan baru melebihi batas yang telah ditetapkan, API akan menolak permintaan dan memberikan pesan *error* yang informatif, termasuk sisa limit yang tersedia.

### Activity Log

  * Menggunakan paket **Spatie Laravel Activity Log**, semua aksi penting (seperti *create*, *edit*, dan *delete*) dicatat secara otomatis ke dalam *database*.
  * Fitur ini sangat membantu *admin* dalam melakukan audit dan *troubleshooting* sistem.

-----

## Setup & Deployment

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal.

### 1\. Clone Proyek

```bash
git clone https://github.com/username/reimbursement-app.git
cd reimbursement-app
composer install
```

### 2\. Konfigurasi Lingkungan (`.env`)

Buat file `.env` di *root directory* proyek Anda dan salin *template* berikut. Isi `APP_KEY` dengan menjalankan perintah `php artisan key:generate`.

```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_KEY= # Generate this using php artisan key:generate
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
DB_HOST= # Isi dengan host database Anda
DB_PORT=5432
DB_DATABASE= # Isi dengan nama database Anda
DB_USERNAME= # Isi dengan username database Anda
DB_PASSWORD= # Isi dengan password database Anda

SANCTUM_STATEFUL_DOMAINS=127.0.0.1:5173,localhost:5173,127.0.0.1:8000,localhost:8000
SESSION_DOMAIN=127.0.0.1
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database # Penting untuk notifikasi email asynchronous

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
MAIL_USERNAME=271d622fa52176 # Ganti dengan kredensial Mailtrap Anda
MAIL_PASSWORD=75a7c10a19a152 # Ganti dengan kredensial Mailtrap Anda
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

**Penting**: Ganti nilai untuk `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, serta `MAIL_USERNAME` dan `MAIL_PASSWORD` dengan kredensial Anda.
### 3\. Install Frontend

```bash
npm install
npm run build
```

### 4\. Buat Database

Buat *database* baru (contoh: MySQL/MariaDB/PostgreSQL) dan konfigurasikan nama, *username*, dan *password* di file `.env` seperti di atas.

Contoh perintah SQL untuk MySQL:

```sql
CREATE DATABASE reimbursement_db;
```

### 5\. Migrate & Seed Database

Jalankan migrasi database dan *seeder* untuk mengisi data awal:

```bash
php artisan migrate --seed
```

### 6\. Jalankan Queue Worker

Karena notifikasi email menggunakan *queue*, Anda perlu menjalankan *worker* untuk memprosesnya:

```bash
php artisan queue:work
```

Biarkan *command* ini berjalan di *terminal* terpisah.

### 7\. Jalankan Server

Terakhir, jalankan server pengembangan Laravel:

```bash
php artisan serve
```

### 8\. Akses Aplikasi

Akses aplikasi Anda di *browser* melalui alamat:

[http://localhost:8000](https://www.google.com/search?q=http://localhost:8000)

-----

## Tantangan & Solusi

Dalam pengembangan proyek ini, saya menghadapi tantangan terkait pengiriman notifikasi email:

  * **Tantangan**: Awalnya saya berencana menggunakan SendGrid untuk pengiriman email *real-time*. Namun, akun SendGrid saya mengalami pemblokiran dan proses pengajuan untuk mendapatkan akses kembali membutuhkan waktu yang lama.
  * **Solusi**: Sebagai alternatif, saya memutuskan untuk menggunakan **Mailtrap** sebagai layanan SMTP *testing*. Ini memungkinkan saya untuk memeriksa keberhasilan pengiriman email tanpa perlu menunggu proses *unblock* SendGrid. Selain itu, saya juga mengimplementasikan *logging* pengiriman email ke dalam *database* dengan tabel `email_logs` untuk melacak status email yang dikirim.

-----
