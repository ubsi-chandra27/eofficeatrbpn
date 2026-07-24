<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Database\Seeder;

class SuratPegawaiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiList = Pegawai::with(['user', 'unitKerja'])
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
            ->orderBy('nip')
            ->get();

        if ($pegawaiList->isEmpty()) {
            $this->command?->error('Akun pegawai demonstrasi belum tersedia.');
            return;
        }

        $rows = [
            ['001', 'AGD-PGW-001', 'Permohonan data pertanahan', 'Instansi Pemerintah', 'draft', null],
            ['002', 'AGD-PGW-002', 'Undangan rapat koordinasi', 'Kantor Wilayah', 'diajukan', null],
            ['003', 'AGD-PGW-003', 'Pemberitahuan pembaruan dokumen', 'Bagian Tata Usaha', 'diverifikasi', null],
            ['004', 'AGD-PGW-004', 'Permohonan klarifikasi berkas', 'Masyarakat', 'dikembalikan', 'Lengkapi identitas pengirim dan lampiran pendukung.'],
            ['005', 'AGD-PGW-005', 'Laporan kegiatan pelayanan', 'Seksi Pelayanan', 'diteruskan_ke_pimpinan', null],
        ];

        foreach ($pegawaiList as $pegawaiIndex => $pegawai) {
            foreach ($rows as $index => [$sequence, $agenda, $subject, $origin, $status, $note]) {
                $date = now()->subDays(5 - $index + $pegawaiIndex);
                Surat::updateOrCreate(
                    ['nomor_surat' => "DEMO/PGW/SM/{$pegawai->nip}/{$sequence}/".$date->format('Y')],
                    [
                        'user_id' => $pegawai->user_id,
                        'jenis_surat' => 'masuk',
                        'nomor_agenda' => "{$agenda}-{$pegawai->nip}",
                        'tanggal_surat' => $date->toDateString(),
                        'perihal' => $subject,
                        'asal_surat' => $origin,
                        'tujuan_surat' => $pegawai->unitKerja?->nama ?? 'Administrasi',
                        'metode' => 'Sistem',
                        'deskripsi' => 'Data demonstrasi surat masuk pegawai untuk tabel pegawai.',
                        'is_priority' => $index === 1,
                        'status' => $status,
                        'catatan_admin' => $note,
                    ]
                );
            }
        }

        $this->command?->info(($pegawaiList->count() * count($rows)).' surat masuk pegawai dengan status bervariasi berhasil disiapkan.');
    }
}
