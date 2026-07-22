<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Database\Seeder;

class SuratPegawaiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = Pegawai::with('user')->where('nip', '198801010001')->first();

        if (! $pegawai?->user) {
            $this->command?->error('Akun pegawai demonstrasi utama belum tersedia.');
            return;
        }

        $rows = [
            ['001', 'Permohonan data pertanahan', 'Instansi Pemerintah', 'draft', null],
            ['002', 'Undangan rapat koordinasi', 'Kantor Wilayah', 'diajukan', null],
            ['003', 'Pemberitahuan pembaruan dokumen', 'Bagian Tata Usaha', 'diverifikasi', null],
            ['004', 'Permohonan klarifikasi berkas', 'Masyarakat', 'dikembalikan', 'Lengkapi identitas pengirim dan lampiran pendukung.'],
            ['005', 'Laporan kegiatan pelayanan', 'Seksi Pelayanan', 'diteruskan_ke_pimpinan', null],
        ];

        foreach ($rows as $index => [$sequence, $subject, $origin, $status, $note]) {
            $date = now()->subDays(5 - $index);
            Surat::updateOrCreate(
                ['nomor_surat' => "DEMO/PGW/SM/{$sequence}/".$date->format('Y')],
                [
                    'user_id' => $pegawai->user_id,
                    'jenis_surat' => 'masuk',
                    'tanggal_surat' => $date->toDateString(),
                    'perihal' => $subject,
                    'asal_surat' => $origin,
                    'tujuan_surat' => 'Administrasi',
                    'metode' => 'Sistem',
                    'deskripsi' => 'Data demonstrasi surat masuk pegawai.',
                    'is_priority' => $index === 1,
                    'status' => $status,
                    'catatan_admin' => $note,
                ]
            );
        }

        $this->command?->info('Lima surat masuk pegawai dengan status bervariasi berhasil disiapkan.');
    }
}
