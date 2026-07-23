# Pembaharuan Proyek eOffice ATR/BPN

Pembaruan dilakukan karena beberapa menu sebelumnya belum lengkap, datanya belum terintegrasi, perhitungan statistik tidak konsisten, dan sebagian fungsi dapat bermasalah setelah aplikasi di-hosting.

## Ringkasan pembaruan

### Dashboard Admin, Pegawai, dan Umum
Statistik disesuaikan dengan status database. Tabel diisi menggunakan data nyata. Data pengguna lain tidak ikut tampil. Diperbaiki karena sebelumnya beberapa angka dan tautan filter tidak sesuai.

### Surat Masuk dan Surat Keluar Admin
Ditambahkan fungsi tambah, edit, detail, hapus, filter, dan verifikasi. Diperbaiki agar proses administrasi surat dapat dilakukan lengkap dari halaman Admin.

### Disposisi Admin
Ditambahkan isi tabel, penerima, pengirim, instruksi, prioritas, status baca, tambah, edit, detail, dan hapus. Diperlukan agar penugasan surat kepada Pegawai dapat dipantau.

### Data Pegawai, Jabatan, dan Unit Kerja
Ditambahkan CRUD lengkap. Validasi relasi ditambahkan agar data yang masih digunakan tidak dapat dihapus sembarang.

### Manajemen Pengguna
Tampilan dirapikan. NIP/NIPP dan hubungan akun dengan profil Pegawai ditampilkan. Diperlukan agar Admin dapat mengenali akun yang belum terhubung dengan data Pegawai.

### Surat Masuk Pegawai
Ditambahkan fungsi tambah, edit, detail, hapus, cetak, kirim ke Admin, dan status verifikasi. Diperbaiki karena sebelumnya belum jelas apakah surat sudah disetujui Admin.

### Surat Keluar Pegawai
Ditambahkan CRUD, cetak, validasi tujuan, dan status proses. Form dibuat bersama agar halaman tambah dan edit konsisten.

### Disposisi Pegawai
Ditambahkan daftar disposisi masuk dan terkirim, tambah, edit, detail, hapus, status baca, penyelesaian, serta cetak. Data dibatasi berdasarkan Pegawai yang login.

### Dashboard Pegawai
Menampilkan disposisi aktif, belum dibaca, surat keluar, dan menunggu verifikasi. Tabel menampilkan pengirim, instruksi, prioritas, status, lampiran, catatan Admin, dan aktivitas. Error 500 ketika tabel disposisi berisi data telah diperbaiki.

### Dashboard Umum
Menampilkan statistik, Pengajuan Terbaru, dan Aktivitas Terbaru. Tabel tetap muncul meskipun belum ada pengajuan. Diperbaiki karena sebelumnya surat keluar dapat ikut dihitung dan tabel menghilang ketika data kosong.

### Menu Surat Saya
Menampilkan nomor, kategori, pokok, kontak, instansi, tanggal, lampiran, status, tahap proses, catatan Admin, dan aksi. Ditambahkan pencarian serta filter kategori/status. Lebar tabel diperbaiki agar kolom Lampiran, Status, dan Aksi tidak terpotong.

### Keamanan
Detail, edit, unduh, pencarian, dan hapus dibatasi kepada pemilik data. Surat masuk dan keluar tidak lagi tercampur. Aktivitas tidak dapat membocorkan surat akun lain. Pengiriman pengajuan diberi pembatasan frekuensi.

### Lampiran
Validasi format, ukuran, dan ekstensi berbahaya diperkuat. Penggantian lampiran dibuat lebih aman agar file lama tidak hilang jika database gagal.

### Sidebar mobile
Sidebar Admin, Pegawai, dan Umum dapat dibuka dan ditutup di HP/tablet. Bisa ditutup melalui backdrop, pilihan menu, dan tombol Escape. Dibuat karena sebelumnya tombol Pegawai dan Umum belum bekerja konsisten.

### Data demonstrasi Umum
Seeder lama hanya menargetkan satu email tertentu. Sekarang setiap akun Umum dapat memperoleh contoh pengajuan Diajukan, Diproses, Perlu Perbaikan, dan Selesai tanpa duplikasi.

## Validasi akhir
- 140 pengujian berhasil.
- 803 assertions berhasil.
- Tidak ada pengujian gagal.
- Blade berhasil dikompilasi.
