# PERBAIKAN AUDIT HOSTING E-OFFICE ATR/BPN

Tanggal: 23 Juli 2026

## Ringkasan Audit

Audit dijalankan pada proyek lokal `C:\laragon\www\eoffice-atrbpn`.

### Command audit yang dijalankan

1. `php artisan about`
   - Berhasil.
   - Laravel 13.15.0, PHP 8.3.31.
   - APP_KEY tersedia.
   - Environment lokal, debug aktif.
   - `public/storage` terdeteksi linked.

2. `php artisan route:list`
   - Berhasil.
   - Route aplikasi dapat dibaca tanpa error.

3. `php artisan view:clear; route:clear; config:clear; cache:clear`
   - Berhasil.
   - Cache view, route, config, dan aplikasi berhasil dibersihkan.

4. `php artisan migrate:status`
   - Berhasil.
   - Semua migration berstatus `Ran`.
   - Migration `2026_07_23_000001_add_nipp_to_pegawai_table` sudah berjalan.

5. `storage/app/public`
   - Direktori tersedia.

6. `public/storage`
   - Tersedia sebagai Junction ke `storage/app/public`.

7. `.env`
   - APP_KEY tersedia.
   - Database menggunakan MySQL lokal `eoffice_atrbpn`.
   - Mail menggunakan driver `log`.
   - Nilai sensitif tidak dicatat di dokumen ini.

8. `bootstrap/cache`
   - Dapat dibaca.
   - Berisi file cache Laravel normal: `.gitignore`, `packages.php`, dan `services.php`.

9. `php artisan test`
   - Berhasil.
   - 142 test lulus.
   - 813 assertion lulus.

10. `npm.cmd run build`
    - Berhasil.
    - Asset production Vite dibuat di `public/build`.
    - `npm` via PowerShell terblokir execution policy, sehingga digunakan `npm.cmd`.

11. `php artisan view:cache; php artisan view:clear`
    - Berhasil.
    - Semua Blade berhasil dikompilasi.

## Kategori Masalah

### A. CRITICAL

Tidak ditemukan masalah critical.

- APP_KEY ada.
- Symlink/Junction storage ada.
- Tidak ada migration pending.
- Test penuh lulus.
- Tidak ada error permission pada direktori yang diperiksa.

### B. MAJOR

#### 1. Route legacy / partial lama mengarah ke nama route yang tidak tersedia

Beberapa view/partial lama memiliki nama route yang tidak sesuai route aktif:

- `resources/views/partials/admin-sidebar.blade.php`
  - Sebelumnya memakai `admin.unitkerja.index`.
  - Route aktif adalah `admin.unit.kerja.index`.

- `resources/views/components/sidebar-layout.blade.php`
  - Sebelumnya memakai `pegawai.surat.masuk.index` dan `pegawai.surat.keluar.index`.
  - Route aktif adalah `pegawai.surat-masuk.index` dan `pegawai.surat-keluar.index`.

- `resources/views/admin/user/index.blade.php`
  - Sebelumnya memakai `admin.user.updateRole` dan `admin.user.destroy`.
  - Route aktif adalah `admin.users.updateRole` dan `admin.users.destroy`.

- `resources/views/pengaturan/*`
  - View legacy pengaturan masih mengarah ke route seperti `admin.settings.profile`, `admin.settings.security`, `admin.settings.format`, `admin.settings.backup`, dan `admin.settings.about`.
  - Route tersebut belum tersedia.

Perbaikan:

- Memperbaiki typo nama route di partial/komponen lama.
- Menambahkan route legacy pengaturan yang aman pada `routes/web.php`.
- Menambahkan method pendukung di `AdminSettingsController`.
- Restore database pada halaman legacy dibuat non-destruktif dan mengembalikan pesan error aman agar tidak mengubah data tanpa kontrol.

### C. MINOR

#### 1. Asset production perlu dipastikan tersedia untuk hosting

`public/build` sudah ada, tetapi build diverifikasi ulang menggunakan:

```bash
npm.cmd run build
```

Hasil:

- Build Vite berhasil.
- Asset production tersedia di `public/build`.

### D. CHECKLIST AUDIT SEBELUMNYA

#### 1. Surat Keluar Admin: workflow verifikasi setujui/tolak

Status: selesai.

Perbaikan:

- Menambahkan method `setujui()` dan `tolak()` di `AdminSuratKeluarController`.
- Menambahkan route:
  - `admin.surat.keluar.setujui`
  - `admin.surat.keluar.tolak`
- Menambahkan tombol setujui/tolak di index dan detail surat keluar admin.
- Menambahkan test workflow verifikasi surat keluar admin.

Alasan:

- Sebelumnya surat keluar admin hanya CRUD dan belum punya alur verifikasi eksplisit.

#### 2. Manajemen Pengguna: NIPP dan badge Hub. Akun

Status: selesai.

Perbaikan:

- Menambahkan kolom `nipp` ke tabel `pegawai`.
- Menambahkan input NIPP pada form pegawai.
- Menampilkan NIPP di index dan detail pegawai.
- Menampilkan badge `Terhubung` / `Tidak Terhubung` di manajemen pengguna.

Alasan:

- Admin perlu melihat keterhubungan akun user dengan data pegawai dan identitas NIPP pegawai.

#### 3. Disposisi Pegawai: route dan view terkirim

Status: selesai.

Perbaikan:

- Menambahkan method `terkirim()` di `Pegawai\DisposisiController`.
- Menambahkan route `pegawai.disposisi.terkirim`.
- Menambahkan view `resources/views/pegawai/disposisi/terkirim.blade.php`.
- Menambahkan tombol menuju halaman disposisi terkirim.

Alasan:

- Pegawai perlu melihat disposisi yang pernah dikirim kepada pegawai lain secara terpisah dari disposisi masuk.

#### 4. Keamanan: throttle create surat pegawai

Status: selesai.

Perbaikan:

- Menambahkan middleware `throttle:10,1` pada:
  - `pegawai.surat-masuk.store`
  - `pegawai.surat-keluar.store`

Alasan:

- Mencegah pengiriman surat/lampiran berulang terlalu cepat.

#### 5. Lampiran: safe replace old file

Status: selesai setelah audit.

Hasil audit:

- Controller surat sudah menyimpan file baru terlebih dahulu.
- Database di-update dalam transaction.
- Jika update gagal, file baru dihapus.
- File lama baru dihapus setelah update database berhasil.

Alasan:

- Mencegah lampiran lama hilang ketika upload baru atau update database gagal.

#### 6. Sidebar mobile

Status: selesai setelah audit.

Hasil audit:

- Layout admin, pegawai, dan umum sudah memiliki toggle mobile.
- Backdrop close tersedia.
- Tombol Escape menutup sidebar.
- Klik menu pada mobile otomatis menutup sidebar.

## Pembaruan 24 Juli 2026 - Surat Masuk Pegawai

### Masalah

Tabel `Surat Masuk` pada role pegawai belum sesuai ekspektasi ketika digunakan di hosting/demo karena data demo surat masuk pegawai belum otomatis ikut dibuat dari `DatabaseSeeder`. View tabel juga masih terlalu padat sehingga informasi penting seperti nomor agenda dan catatan admin kurang terlihat.

### Perbaikan

- `DatabaseSeeder` sekarang memanggil:
  - `PegawaiDemoSeeder`
  - `SuratPegawaiDemoSeeder`
- `SuratPegawaiDemoSeeder` sekarang membuat 5 surat masuk demo untuk setiap pegawai yang sudah punya akun login, bukan hanya satu pegawai hardcoded.
- Tabel `resources/views/pegawai/surat/masuk/index.blade.php` dirapikan dan dilengkapi kolom:
  - No
  - No Agenda
  - Nomor Surat
  - Perihal
  - Asal & Tujuan
  - Tanggal
  - Lampiran
  - Status Verifikasi
  - Catatan Admin
  - Aksi
- Pencarian di `Pegawai\SuratMasukController` diperluas ke nomor agenda, tujuan surat, dan catatan admin.
- Filter status surat masuk pegawai diperluas agar mencakup status `ditolak` dan `diarsipkan`.
- Test `PegawaiSuratMasukCrudTest` ditambah untuk memastikan isi tabel surat masuk pegawai muncul dan data demo tersedia untuk semua pegawai demo.

### Verifikasi

- `php -l app/Http/Controllers/Pegawai/SuratMasukController.php`: berhasil.
- `php -l database/seeders/DatabaseSeeder.php`: berhasil.
- `php -l database/seeders/SuratPegawaiDemoSeeder.php`: berhasil.
- `php artisan test --filter=PegawaiSuratMasukCrudTest`: 7 test lulus, 53 assertion.

## Pembaruan 24 Juli 2026 - Disposisi Pegawai

### Masalah

Menu `Disposisi Pegawai` sudah memiliki route dan halaman, tetapi data demo yang dipanggil dari `DatabaseSeeder` masih memakai seeder lama sehingga tabel disposisi pegawai dapat terlihat kosong setelah aplikasi disiapkan ulang di hosting. Hal ini membuat tampilan tidak sesuai ekspektasi karena pegawai tidak langsung melihat isi tabel disposisi masuk dan disposisi terkirim.

### Perbaikan

- `DatabaseSeeder` sekarang memakai `DisposisiDemoSeeder` agar data demo disposisi benar-benar terhubung ke tabel tujuan disposisi pegawai.
- `DisposisiDemoSeeder` diperbarui untuk membuat:
  - disposisi masuk untuk setiap pegawai demo yang punya akun login;
  - disposisi terkirim dari setiap pegawai demo ke pegawai lain;
  - variasi status `Belum Dibaca`, `Sudah Dibaca`, dan `Selesai`;
  - variasi prioritas `Tinggi`, `Sedang`, dan `Rendah`.
- Halaman `resources/views/pegawai/disposisi/index.blade.php` diberi heading `Tabel Disposisi Masuk` dan jumlah data agar isi tabel lebih jelas.
- Pencarian disposisi masuk pegawai diperluas ke nomor surat, perihal, instruksi/catatan, nama pengirim, dan email pengirim.
- Test `PegawaiDisposisiCrudTest` ditambah untuk memastikan tabel disposisi masuk dan disposisi terkirim pegawai demo benar-benar terisi.

### Status CRUD Disposisi Pegawai

- Tambah: tersedia di `/pegawai/disposisi/create` dan route `pegawai.disposisi.store`.
- Detail disposisi masuk: tersedia di `/pegawai/disposisi/{id}`.
- Detail disposisi terkirim: tersedia di `/pegawai/disposisi-terkirim/{id}`.
- Edit disposisi terkirim: tersedia di `/pegawai/disposisi-terkirim/{id}/edit`.
- Hapus disposisi terkirim: tersedia melalui route `pegawai.disposisi.destroy`.
- Catatan: edit dan hapus disposisi terkirim dikunci otomatis jika disposisi sudah dibaca penerima, supaya riwayat disposisi tetap aman.

### Verifikasi

- `php -l app/Http/Controllers/Pegawai/DisposisiController.php`: berhasil.
- `php -l database/seeders/DatabaseSeeder.php`: berhasil.
- `php -l database/seeders/DisposisiDemoSeeder.php`: berhasil.
- `php artisan route:list --name=pegawai.disposisi`: 12 route terdaftar.
- `php artisan test --filter=PegawaiDisposisiCrudTest`: 6 test lulus, 49 assertion.
- `php artisan view:cache` lalu `php artisan view:clear`: berhasil.
- `php artisan test`: 144 test lulus, 841 assertion.

## Pembaruan 24 Juli 2026 - Data Awal Pegawai Baru

### Masalah

Akun pegawai yang baru register dapat masuk ke menu `Surat Masuk` dan `Disposisi`, tetapi tabelnya kosong karena belum ada surat/disposisi yang dibuat untuk akun tersebut. Seeder manual memang bisa mengisi data, namun akun baru setelah register tetap kosong jika tidak dibuatkan data awal.

### Perbaikan

- Ditambahkan `PegawaiStarterDataService` sebagai satu sumber pembuatan data awal pegawai.
- Saat pegawai baru register, sistem otomatis menyiapkan:
  - 5 surat masuk pegawai dengan status bervariasi;
  - 5 disposisi masuk yang ditujukan kepada pegawai tersebut;
  - 1 disposisi terkirim jika sudah ada pegawai lain sebagai penerima.
- `SuratPegawaiDemoSeeder` dan `DisposisiDemoSeeder` sekarang memakai service yang sama agar isi tabel hasil seeder dan hasil register konsisten.
- Test register pegawai diperluas untuk memastikan pegawai baru langsung punya data `Surat Masuk` dan `Disposisi`.

### Verifikasi

- `php -l app/Services/PegawaiStarterDataService.php`: berhasil.
- `php -l app/Http/Controllers/Auth/RegisteredUserController.php`: berhasil.
- `php -l database/seeders/SuratPegawaiDemoSeeder.php`: berhasil.
- `php -l database/seeders/DisposisiDemoSeeder.php`: berhasil.
- `php artisan test --filter=RoleIdentifierAuthenticationTest`: 5 test lulus, 26 assertion.
- `php artisan test --filter=PegawaiSuratMasukCrudTest`: 7 test lulus, 53 assertion.
- `php artisan test --filter=PegawaiDisposisiCrudTest`: 6 test lulus, 49 assertion.

## Pembaruan 24 Juli 2026 - Data Awal Akun Umum

### Masalah

Akun umum yang baru register dapat membuka menu `Surat Saya` / `Pengajuan Saya`, tetapi tabelnya kosong jika belum pernah membuat pengajuan. Akun umum lama bisa diisi lewat `PengajuanUmumDemoSeeder`, namun akun baru setelah register belum otomatis mendapatkan data awal untuk kebutuhan demo/pengujian.

### Perbaikan

- Ditambahkan `UmumStarterDataService` sebagai satu sumber pembuatan data awal akun umum.
- Saat akun umum baru register, sistem otomatis menyiapkan 4 pengajuan demo dengan status bervariasi:
  - `Diajukan`
  - `Diproses`
  - `Perlu Perbaikan`
  - `Selesai`
- `PengajuanUmumDemoSeeder` sekarang memakai service yang sama agar isi tabel hasil seeder dan hasil register konsisten.
- Test register umum diperluas untuk memastikan akun umum baru langsung punya data `Pengajuan Saya`.

### Verifikasi

- `php -l app/Services/UmumStarterDataService.php`: berhasil.
- `php -l app/Http/Controllers/Auth/RegisteredUserController.php`: berhasil.
- `php -l database/seeders/PengajuanUmumDemoSeeder.php`: berhasil.
- `php artisan test --filter=RegistrationTest`: 2 test lulus, 6 assertion.
- `php artisan test --filter=UmumAccessTest`: 17 test lulus, 112 assertion.
- `php artisan test --filter=UmumDashboardTest`: 3 test lulus, 29 assertion.

## Pembaruan 24 Juli 2026 - Form Surat Masuk Pegawai

### Masalah

Form `Surat Masuk` pegawai masih memuat bagian tujuan/pimpinan seperti `Jabatan Pimpinan` dan `Nama Pimpinan`, sehingga terasa seperti form surat keluar. Judul/tombol juga masih memakai istilah `Catat Surat Masuk`, sementara ekspektasi tampilan adalah form tambah surat masuk yang jelas dan tidak memakai penandatangan/pimpinan.

### Perbaikan

- Form tambah surat masuk diubah menjadi `Tambah Surat Masuk`.
- Form edit surat masuk dirapikan agar hanya berisi data yang relevan untuk surat masuk:
  - Nomor Surat
  - Nomor Agenda
  - Tanggal Surat
  - Asal Surat / Pengirim
  - Perihal
  - Tujuan Surat
  - Deskripsi / Catatan
  - Lampiran
- Field `Jabatan Pimpinan` dan `Nama Pimpinan` dihapus dari form tambah/edit surat masuk.
- Validasi `SuratMasukController` disesuaikan: tidak lagi menerima field pimpinan untuk surat masuk, dan sekarang menerima `nomor_agenda`.
- Tombol di halaman daftar diubah menjadi `Tambah Surat Masuk`.

### Verifikasi

- `php -l app/Http/Controllers/Pegawai/SuratMasukController.php`: berhasil.
- `php -l resources/views/pegawai/surat/masuk/create.blade.php`: berhasil.
- `php -l resources/views/pegawai/surat/masuk/edit.blade.php`: berhasil.
- `php artisan route:list --name=pegawai.surat-masuk`: 9 route terdaftar.
- `php artisan test --filter=PegawaiSuratMasukCrudTest`: 7 test lulus, 56 assertion.
- `php artisan test --filter=PegawaiSuratMasukVerificationTest`: 5 test lulus, 37 assertion.

## File yang Diubah

- `PROGRES-AUDIT.md`
- `PERBAIKAN.md`
- `app/Http/Controllers/Admin/AdminSettingsController.php`
- `app/Http/Controllers/Admin/AdminSuratKeluarController.php`
- `app/Http/Controllers/Admin/AdminPegawaiController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Pegawai/SuratMasukController.php`
- `app/Http/Controllers/Pegawai/DisposisiController.php`
- `app/Models/Pegawai.php`
- `app/Services/PegawaiStarterDataService.php`
- `app/Services/UmumStarterDataService.php`
- `database/seeders/DatabaseSeeder.php`
- `database/migrations/2026_07_23_000001_add_nipp_to_pegawai_table.php`
- `database/seeders/PegawaiDemoSeeder.php`
- `database/seeders/PegawaiSeeder.php`
- `database/seeders/DisposisiDemoSeeder.php`
- `database/seeders/PengajuanUmumDemoSeeder.php`
- `database/seeders/SuratPegawaiDemoSeeder.php`
- `resources/views/admin/pegawai/_form.blade.php`
- `resources/views/admin/pegawai/index.blade.php`
- `resources/views/admin/pegawai/show.blade.php`
- `resources/views/admin/surat/keluar/_form.blade.php`
- `resources/views/admin/surat/keluar/index.blade.php`
- `resources/views/admin/surat/keluar/show.blade.php`
- `resources/views/admin/user/index.blade.php`
- `resources/views/admin/users.blade.php`
- `resources/views/components/sidebar-layout.blade.php`
- `resources/views/partials/admin-sidebar.blade.php`
- `resources/views/pegawai/disposisi/index.blade.php`
- `resources/views/pegawai/disposisi/terkirim.blade.php`
- `resources/views/pegawai/surat/masuk/index.blade.php`
- `resources/views/pegawai/surat/masuk/create.blade.php`
- `resources/views/pegawai/surat/masuk/edit.blade.php`
- `routes/web.php`
- `tests/Feature/Admin/AdminSuratKeluarCrudTest.php`
- `tests/Feature/Auth/RoleIdentifierAuthenticationTest.php`
- `tests/Feature/Auth/RegistrationTest.php`
- `tests/Feature/Pegawai/PegawaiSuratMasukCrudTest.php`
- `public/build/*` melalui `npm.cmd run build`

## Cara Verifikasi Manual

### Admin

- `/admin/dashboard`
- `/admin/users`
- `/admin/pegawai`
- `/admin/surat-keluar`
- `/admin/surat-keluar/{id}`
- `/admin/settings`
- `/admin/settings/profile`
- `/admin/settings/security`
- `/admin/settings/format`
- `/admin/settings/backup`
- `/admin/settings/about`

### Pegawai

- `/pegawai/dashboard`
- `/pegawai/disposisi`
- `/pegawai/disposisi/terkirim`
- `/pegawai/surat-masuk`
- `/pegawai/surat-masuk/create`
- `/pegawai/surat-keluar/create`

### Umum

- `/umum/dashboard`
- `/umum/surat`

## Status Akhir

Sampai dokumen ini dibuat:

- Tidak ada masalah critical tersisa.
- Route legacy yang terdeteksi missing sudah diperbaiki.
- Asset production berhasil dibuild.
- Blade berhasil dikompilasi.
- Test penuh sebelumnya lulus 142 test / 813 assertion.
- Pembaruan surat masuk pegawai lulus test khusus `PegawaiSuratMasukCrudTest`.
- Pembaruan disposisi pegawai lulus test khusus `PegawaiDisposisiCrudTest`.
- Test penuh terbaru lulus 144 test / 841 assertion.
