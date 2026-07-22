<?php

namespace Database\Seeders;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengajuanUmumDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'caraamellsundy@gmail.com')
            ->where('role', 'umum')
            ->first();

        if (! $user) {
            $this->command?->warn('Akun umum caraamellsundy@gmail.com tidak ditemukan.');
            return;
        }

        $submissions = [
            [
                'nomor_surat' => 'UMUM/20260721/INFO01',
                'kategori_pengajuan' => 'Permohonan Informasi',
                'perihal' => 'Informasi jadwal dan prosedur pelayanan',
                'deskripsi' => 'Memohon informasi mengenai jadwal pelayanan dan dokumen yang perlu dipersiapkan.',
                'status' => 'diajukan',
                'catatan_admin' => null,
                'tanggal_surat' => now()->subDays(3)->toDateString(),
            ],
            [
                'nomor_surat' => 'UMUM/20260721/DOK001',
                'kategori_pengajuan' => 'Permohonan Dokumen',
                'perihal' => 'Permohonan salinan informasi layanan',
                'deskripsi' => 'Memohon salinan dokumen informasi layanan untuk keperluan administrasi.',
                'status' => 'diproses',
                'catatan_admin' => 'Dokumen sedang diperiksa oleh bagian administrasi.',
                'tanggal_surat' => now()->subDays(2)->toDateString(),
            ],
            [
                'nomor_surat' => 'UMUM/20260721/ADU001',
                'kategori_pengajuan' => 'Pengaduan',
                'perihal' => 'Kendala akses informasi pelayanan',
                'deskripsi' => 'Melaporkan kendala saat mengakses informasi pelayanan digital.',
                'status' => 'dikembalikan',
                'catatan_admin' => 'Mohon lengkapi waktu kejadian dan tangkapan layar kendala.',
                'tanggal_surat' => now()->subDay()->toDateString(),
            ],
            [
                'nomor_surat' => 'UMUM/20260721/SRT001',
                'kategori_pengajuan' => 'Penyampaian Surat',
                'perihal' => 'Penyampaian surat permohonan koordinasi',
                'deskripsi' => 'Menyampaikan surat permohonan koordinasi kepada bagian administrasi.',
                'status' => 'selesai',
                'catatan_admin' => 'Surat telah diterima dan dicatat oleh bagian administrasi.',
                'tanggal_surat' => now()->subDays(5)->toDateString(),
            ],
        ];

        foreach ($submissions as $submission) {
            Surat::updateOrCreate(
                ['nomor_surat' => $submission['nomor_surat']],
                array_merge($submission, [
                    'user_id' => $user->id,
                    'jenis_surat' => 'masuk',
                    'asal_surat' => $user->name,
                    'asal_instansi' => $user->organization,
                    'nomor_kontak' => $user->phone ?: '0812 0000 0000',
                    'tujuan_surat' => 'Administrasi Umum',
                    'metode' => 'Sistem',
                    'is_priority' => false,
                ])
            );
        }

        $this->command?->info('Empat pengajuan contoh berhasil tersedia untuk akun caramel anna.');
    }
}
