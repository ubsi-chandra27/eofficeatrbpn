<?php

namespace Database\Seeders;

use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Database\Seeder;

class SuratKeluarPegawaiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = Pegawai::with(['user', 'jabatan'])->where('nip', '198801010001')->first();

        if (! $pegawai?->user || ! $pegawai?->jabatan) {
            $this->command?->error('Akun dan jabatan pegawai demonstrasi utama belum tersedia.');
            return;
        }

        $rows = [
            ['001', 'Undangan rapat koordinasi internal', 'Seluruh Kepala Seksi', 'draft', null],
            ['002', 'Permohonan data pendukung', 'Kantor Wilayah', 'diajukan', null],
            ['003', 'Balasan permohonan informasi', 'Pemohon Informasi', 'dikembalikan', 'Perbaiki tujuan surat dan tambahkan dasar surat.'],
            ['004', 'Pemberitahuan jadwal pelayanan', 'Masyarakat dan Mitra Kerja', 'diverifikasi', null],
            ['005', 'Laporan pelaksanaan kegiatan', 'Kantor Wilayah', 'terkirim', null],
        ];

        foreach ($rows as $index => [$sequence, $subject, $destination, $status, $note]) {
            $date = now()->subDays(5 - $index);
            $letter = Surat::updateOrCreate(
                ['nomor_surat' => "DEMO/PGW/SK/{$sequence}/".$date->format('Y')],
                [
                    'user_id' => $pegawai->user_id,
                    'jenis_surat' => 'keluar',
                    'tanggal_surat' => $date->toDateString(),
                    'tanggal_kirim' => $status === 'terkirim' ? $date->toDateString() : null,
                    'perihal' => $subject,
                    'asal_surat' => 'Administrasi',
                    'tujuan_surat' => $destination,
                    'nama_pimpinan' => $pegawai->nama,
                    'jabatan_pimpinan_id' => $pegawai->jabatan_id,
                    'metode' => 'Sistem',
                    'deskripsi' => 'Data demonstrasi surat keluar pegawai.',
                    'is_priority' => $index === 1,
                    'status' => $status,
                    'catatan_admin' => $note,
                ]
            );

            LogAktivitas::updateOrCreate(
                ['user_id' => $pegawai->user_id, 'surat_id' => $letter->id, 'action' => 'Membuat Surat Keluar'],
                ['description' => 'Membuat surat keluar '.$letter->nomor_surat.'.']
            );
        }

        $this->command?->info('Lima surat keluar pegawai dengan status bervariasi berhasil disiapkan.');
    }
}
