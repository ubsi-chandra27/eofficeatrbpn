<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Surat;
use App\Models\User;

class SuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Surat::withTrashed()->forceDelete();

        $user = User::first();

        if (!$user) {
            $this->command->error('Tidak ada data user. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $data = [

            [
                'user_id'        => $user->id,
                'jenis_surat'    => 'masuk',
                'nomor_surat'    => '001/SM/2026',
                'nomor_agenda'   => 'AGD-001',
                'kode_surat'     => 'SM-001',
                'judul_surat'    => 'Permohonan Data',
                'perihal'        => 'Permohonan Data',
                'lampiran'       => null,
                'asal_surat'     => 'Kementerian ATR/BPN',
                'tujuan_surat'   => 'Kantor ATR/BPN',
                'metode'         => 'Manual',
                'deskripsi'      => 'Contoh surat masuk.',
                'file_path'      => null,
                'is_priority'    => false,
                'status'         => 'menunggu',
                'catatan_admin'  => null,
                'tanggal_surat'  => now()->toDateString(),
            ],

            [
                'user_id'        => $user->id,
                'jenis_surat'    => 'masuk',
                'nomor_surat'    => '002/SM/2026',
                'nomor_agenda'   => 'AGD-002',
                'kode_surat'     => 'SM-002',
                'judul_surat'    => 'Undangan Rapat',
                'perihal'        => 'Rapat Bulanan',
                'lampiran'       => null,
                'asal_surat'     => 'BPN Pusat',
                'tujuan_surat'   => 'Kantor ATR/BPN',
                'metode'         => 'Email',
                'deskripsi'      => 'Undangan rapat koordinasi.',
                'file_path'      => null,
                'is_priority'    => true,
                'status'         => 'diproses',
                'catatan_admin'  => null,
                'tanggal_surat'  => now()->subDay()->toDateString(),
            ],

            [
                'user_id'        => $user->id,
                'jenis_surat'    => 'keluar',
                'nomor_surat'    => '001/SK/2026',
                'nomor_agenda'   => 'AGD-003',
                'kode_surat'     => 'SK-001',
                'judul_surat'    => 'Balasan Surat',
                'perihal'        => 'Balasan Permohonan',
                'lampiran'       => null,
                'asal_surat'     => 'Kantor ATR/BPN',
                'tujuan_surat'   => 'Kementerian ATR/BPN',
                'metode'         => 'Manual',
                'deskripsi'      => 'Balasan permohonan data.',
                'file_path'      => null,
                'is_priority'    => false,
                'status'         => 'selesai',
                'catatan_admin'  => null,
                'tanggal_surat'  => now()->subDays(2)->toDateString(),
            ],

            [
                'user_id'        => $user->id,
                'jenis_surat'    => 'keluar',
                'nomor_surat'    => '002/SK/2026',
                'nomor_agenda'   => 'AGD-004',
                'kode_surat'     => 'SK-002',
                'judul_surat'    => 'Pemberitahuan',
                'perihal'        => 'Pemberitahuan Kegiatan',
                'lampiran'       => null,
                'asal_surat'     => 'Kantor ATR/BPN',
                'tujuan_surat'   => 'Seluruh Pegawai',
                'metode'         => 'Email',
                'deskripsi'      => 'Pemberitahuan kegiatan kantor.',
                'file_path'      => null,
                'is_priority'    => false,
                'status'         => 'selesai',
                'catatan_admin'  => null,
                'tanggal_surat'  => now()->subDays(3)->toDateString(),
            ],

            [
                'user_id'        => $user->id,
                'jenis_surat'    => 'masuk',
                'nomor_surat'    => '003/SM/2026',
                'nomor_agenda'   => 'AGD-005',
                'kode_surat'     => 'SM-003',
                'judul_surat'    => 'Permohonan Sertifikat',
                'perihal'        => 'Pengajuan Sertifikat Tanah',
                'lampiran'       => null,
                'asal_surat'     => 'Masyarakat',
                'tujuan_surat'   => 'Kantor ATR/BPN',
                'metode'         => 'Manual',
                'deskripsi'      => 'Pengajuan sertifikat tanah.',
                'file_path'      => null,
                'is_priority'    => true,
                'status'         => 'menunggu',
                'catatan_admin'  => null,
                'tanggal_surat'  => now()->subDays(4)->toDateString(),
            ],

        ];

        foreach ($data as $surat) {
            Surat::create($surat);
        }

        $this->command->info('Berhasil menambahkan ' . Surat::count() . ' data surat.');
    }
}