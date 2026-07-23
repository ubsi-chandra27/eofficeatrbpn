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

## File yang Diubah

- `PROGRES-AUDIT.md`
- `PERBAIKAN.md`
- `app/Http/Controllers/Admin/AdminSettingsController.php`
- `app/Http/Controllers/Admin/AdminSuratKeluarController.php`
- `app/Http/Controllers/Admin/AdminPegawaiController.php`
- `app/Http/Controllers/Pegawai/DisposisiController.php`
- `app/Models/Pegawai.php`
- `database/migrations/2026_07_23_000001_add_nipp_to_pegawai_table.php`
- `database/seeders/PegawaiDemoSeeder.php`
- `database/seeders/PegawaiSeeder.php`
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
- `routes/web.php`
- `tests/Feature/Admin/AdminSuratKeluarCrudTest.php`
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
