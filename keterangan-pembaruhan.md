# Keterangan Pembaruan E-Office ATR/BPN

Tanggal pembaruan: 23 Juli 2026

## Ringkasan

Pembaruan ini merupakan hasil audit dan integrasi modul Administrator, Pegawai, dan Masyarakat Umum. Fokus utamanya adalah melengkapi operasi data, memperbaiki keamanan akses, menyelaraskan status surat, menampilkan isi tabel dari database, dan meningkatkan dukungan perangkat mobile.

## Administrator

- Dashboard Administrator memakai ringkasan surat dan disposisi yang sesuai dengan status pada database.
- Tabel surat masuk dilengkapi pencarian, status verifikasi, detail, tambah, edit, dan hapus.
- Tabel surat keluar dilengkapi tambah, edit, detail, hapus, status, dan informasi tujuan.
- Modul disposisi dilengkapi isi tabel, pengirim, penerima, prioritas, status baca, tambah, edit, detail, dan hapus.
- Modul Data Pegawai dirapikan menggunakan form bersama dan dilengkapi operasi tambah, edit, detail, serta hapus.
- Modul Data Jabatan dilengkapi operasi tambah, edit, detail, dan hapus dengan pemeriksaan relasi.
- Modul Unit Kerja dilengkapi operasi tambah, edit, detail, dan hapus dengan pemeriksaan relasi.
- Manajemen pengguna menampilkan role, NIP/NIPP, profil pegawai terkait, filter, dan ringkasan akun.

## Pegawai

- Dashboard Pegawai menampilkan Disposisi Aktif, Belum Dibaca, Surat Keluar, dan Menunggu Verifikasi.
- Tabel dashboard menampilkan disposisi terbaru, pengirim, instruksi, prioritas, tanggal, status, surat masuk terbaru, tahap verifikasi, lampiran, catatan Admin, dan aktivitas.
- Perhitungan prioritas tinggi tidak lagi memasukkan disposisi yang sudah selesai.
- Error 500 pada dashboard saat tabel disposisi berisi data telah diperbaiki.
- Surat masuk dilengkapi tambah, edit, detail, hapus, cetak, pengajuan ke Admin, dan status verifikasi.
- Surat keluar dilengkapi tambah, edit, detail, hapus, cetak, validasi, serta form bersama.
- Disposisi Pegawai dilengkapi daftar masuk dan terkirim, tambah, edit, detail, hapus, status baca, penyelesaian, dan cetak.
- Seluruh data Pegawai dibatasi berdasarkan akun dan profil pegawai yang sedang login.

## Masyarakat Umum

- Dashboard Umum hanya menghitung dan menampilkan surat masuk milik pengguna yang sedang login.
- Statistik mencakup Total, Diajukan, Diproses, Perlu Perbaikan, dan Selesai.
- Dashboard menampilkan tabel Pengajuan Terbaru dan Aktivitas Terbaru.
- Tabel pengajuan memuat nomor, kategori, pokok, kontak, instansi, tanggal, lampiran, status, tahap proses, catatan Admin, dan aksi.
- Menu Surat Saya dilengkapi pencarian nomor/pokok/kontak/instansi serta filter kategori dan kelompok status.
- Detail Surat Saya menampilkan tahap proses, catatan Admin, lampiran, histori aktivitas, dan tombol perbaikan jika diizinkan.
- Query detail, edit, unduh, pencarian, dan hapus dibatasi berdasarkan pemilik serta jenis surat.
- Aktivitas tidak dapat membocorkan surat milik akun lain.
- Penggantian lampiran tetap menggunakan proses aman dan akses unduh dibatasi kepada pemilik.
- Seeder pengajuan demonstrasi diperbaiki agar idempoten dan dapat mengisi empat status contoh untuk setiap akun Umum.

## Sidebar dan Tampilan Responsif

- Sidebar Administrator, Pegawai, dan Umum dapat dibuka serta ditutup pada HP dan tablet.
- Sidebar menyediakan backdrop, penutupan ketika menu dipilih, tombol Escape, dan penguncian scroll halaman.
- Atribut aksesibilitas tombol dan sidebar telah ditambahkan.
- Tabel memakai pembungkus responsif dan dapat digeser horizontal pada layar kecil.
- Lebar kolom Surat Saya disesuaikan agar Lampiran, Status, dan Aksi tetap terlihat pada desktop.

## Keamanan dan Integritas Data

- Pembatasan kepemilikan diterapkan pada halaman detail, edit, unduh, pencarian, dan penghapusan.
- Jenis surat divalidasi agar data surat masuk dan keluar tidak tercampur.
- Filter kategori dan status menggunakan daftar nilai yang diizinkan.
- Operasi penting menggunakan transaksi database pada bagian yang memerlukan konsistensi.
- Pengajuan umum memiliki pembatasan frekuensi pengiriman.
- Validasi lampiran berbahaya, ukuran berkas, nomor telepon, dan panjang deskripsi telah diuji.

## Data Demonstrasi

`PengajuanUmumDemoSeeder` membuat empat pengajuan untuk setiap akun Umum:

1. Permohonan Informasi — Diajukan.
2. Permohonan Dokumen — Diproses.
3. Pengaduan — Perlu Perbaikan.
4. Penyampaian Surat — Selesai.

Seeder menggunakan nomor unik berdasarkan ID pengguna, membuat aktivitas terkait, dan aman dijalankan ulang tanpa menggandakan data. Seeder ini ikut dijalankan oleh `DatabaseSeeder` jika `SEED_DEMO_DATA=true`.

## Pengujian

- Seluruh Blade berhasil dikompilasi.
- Pemeriksaan perbedaan Git tidak menemukan whitespace error.
- 140 pengujian berhasil.
- 803 assertions berhasil.
- Tidak ada pengujian yang gagal.

