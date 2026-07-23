# Pembaharuan E-Office ATR/BPN

Tanggal: 23 Juli 2026

## Perubahan Utama

### Administrator

- Audit dan integrasi Dashboard Administrator.
- CRUD Surat Masuk dan Surat Keluar.
- CRUD Disposisi beserta penerima, prioritas, dan status baca.
- CRUD Data Pegawai, Jabatan, dan Unit Kerja.
- Perapian Manajemen Pengguna serta penampilan NIP/NIPP pegawai.

### Pegawai

- Dashboard Pegawai menampilkan statistik dan tabel dari database.
- Surat Masuk dilengkapi pengajuan dan verifikasi Admin.
- Surat Keluar dilengkapi tambah, edit, detail, hapus, dan cetak.
- Disposisi dilengkapi daftar masuk/terkirim, tambah, edit, detail, hapus, status baca, penyelesaian, dan cetak.
- Seluruh data dibatasi berdasarkan akun Pegawai yang sedang login.

### Masyarakat Umum

- Dashboard Umum menampilkan statistik, Pengajuan Terbaru, dan Aktivitas Terbaru.
- Menu Surat Saya menampilkan nomor, kategori, pokok, kontak, instansi, tanggal, lampiran, status, tahap proses, catatan Admin, dan aksi.
- Pencarian dan filter kategori/status telah diintegrasikan.
- Akses detail, edit, unduh, pencarian, dan hapus dibatasi kepada pemilik pengajuan.
- Seeder demo Umum menyediakan data status Diajukan, Diproses, Perlu Perbaikan, dan Selesai secara idempoten.

### Tampilan Mobile

- Sidebar Administrator, Pegawai, dan Umum dapat dibuka dan ditutup pada HP/tablet.
- Sidebar mendukung backdrop, penutupan saat menu dipilih, tombol Escape, dan penguncian scroll.
- Tabel mendukung tampilan responsif dan pergeseran horizontal pada layar kecil.

### Keamanan dan Validasi

- Isolasi data antar pengguna diperketat.
- Jenis surat masuk dan keluar tidak tercampur.
- Validasi kategori, status, lampiran, ukuran file, nomor telepon, dan deskripsi diperkuat.
- Pengajuan Umum memiliki pembatasan frekuensi.
- Operasi yang memerlukan konsistensi menggunakan transaksi database.

## Pengujian

- 140 pengujian berhasil.
- 803 assertions berhasil.
- Tidak ada pengujian gagal.
- Blade berhasil dikompilasi.
- Pemeriksaan `git diff --check` bersih.

Dokumentasi yang lebih terperinci tersedia pada `keterangan-pembaruhan.md`.

