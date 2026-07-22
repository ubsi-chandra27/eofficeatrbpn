# E-Office

Sistem administrasi persuratan digital berbasis Laravel untuk tiga jenis pengguna: Administrator, Pegawai, dan Masyarakat Umum. Aplikasi menangani pencatatan surat, pengajuan layanan, verifikasi, disposisi, pelacakan status, laporan, dan histori aktivitas.

## Alur pengguna

- **Admin** mengelola master pegawai/jabatan/unit kerja, memverifikasi surat, membuat disposisi, memantau dashboard, laporan, pengguna, dan pengaturan.
- **Pegawai** mencatat surat masuk/keluar, mengajukannya ke Admin, membaca dan menyelesaikan disposisi, serta mengelola profil.
- **Umum** memilih layanan, mengirim pengajuan, memantau status/catatan Admin, mencari nomor berkas, dan mengelola profil.

Login Admin dan Pegawai menggunakan **NIP + password**. Login Umum menggunakan **email + password**.

## Kebutuhan server

- PHP 8.3 atau lebih baru beserta ekstensi umum Laravel (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`)
- MySQL 8 atau MariaDB yang kompatibel
- Composer 2
- Node.js 20+ dan npm untuk membangun aset
- Document root domain diarahkan ke folder `public`

## Instalasi lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
php artisan serve
```

Sesuaikan database dan ubah `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://127.0.0.1:8000` pada `.env` lokal.

## Membuat Admin pertama

Isi variabel berikut pada `.env` sebelum menjalankan seeder:

```dotenv
INITIAL_ADMIN_NAME="Administrator"
INITIAL_ADMIN_NIP="NIP-ADMIN"
INITIAL_ADMIN_EMAIL="admin@example.com"
INITIAL_ADMIN_PASSWORD="password-kuat"
```

Kemudian jalankan:

```bash
php artisan db:seed
```

Kosongkan `INITIAL_ADMIN_PASSWORD` setelah akun berhasil dibuat. Akun Pegawai berikutnya dibuat dari menu **Data Pegawai**, sehingga akun login dan profil pegawainya otomatis terhubung.

## Deployment hosting

1. Unggah source code dan arahkan document root ke `public`.
2. Salin `.env.example` menjadi `.env`, isi domain, database, SMTP, dan kredensial Admin awal.
3. Pastikan `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, `APP_FORCE_HTTPS=true`, `SESSION_SECURE_COOKIE=true`, dan `ALLOW_STAFF_REGISTRATION=false`.
4. Jalankan perintah berikut dari folder proyek:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
php artisan app:deployment-check
```

5. Pastikan web server dapat menulis ke `storage` dan `bootstrap/cache`.
6. Gunakan HTTPS. Uji health check pada `/up` dan login ketiga role.
7. Jika queue digunakan, jalankan worker melalui Supervisor atau panel hosting:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Untuk shared hosting tanpa akses Node.js, jalankan `npm ci && npm run build` di komputer lokal lalu unggah folder `public/build` bersama source code.

## Keamanan registrasi

`ALLOW_STAFF_REGISTRATION=false` wajib digunakan pada situs publik. Dengan nilai ini, halaman registrasi hanya membuat akun Umum. Admin dan Pegawai tidak dapat dibuat oleh pengunjung; akun Pegawai dibuat melalui Admin.

Untuk demonstrasi lokal saja, ubah menjadi `true` bila penguji perlu melihat registrasi ketiga role.

## Data demonstrasi

Data master jabatan dan unit kerja dapat dibuat melalui `php artisan db:seed`. Data surat/disposisi contoh hanya dimuat bila `SEED_DEMO_DATA=true`. Jangan mengaktifkannya pada database produksi.

Seeder pengajuan khusus akun contoh dapat dijalankan secara eksplisit:

```bash
php artisan db:seed --class=Database\\Seeders\\PengajuanUmumDemoSeeder
```

## Pemeriksaan sebelum presentasi

```bash
php artisan test
php artisan route:list --except-vendor
php artisan view:cache
php artisan about
```

Pastikan hasil tes lulus, storage berstatus linked, mode debug mati pada hosting, serta dashboard setiap role dapat dibuka.

## Pemeliharaan

Setelah mengubah `.env` atau source di hosting, jalankan:

```bash
php artisan optimize:clear
php artisan optimize
```

Backup database dan folder `storage/app/public` secara berkala karena keduanya menyimpan histori dan lampiran pengguna. Ekspor database sebagai SQL dan arsipkan folder public sebagai ZIP dengan nama bertanggal. Simpan minimal dua salinan di lokasi berbeda dan lakukan uji restore pada database kosong sebelum aplikasi dipublikasikan.

Bootstrap, Bootstrap Icons, Choices.js, dan Chart.js sudah masuk ke build Vite lokal sehingga halaman utama tidak bergantung pada CDN. Jalankan `npm ci && npm run build` setiap kali versi aset berubah.
