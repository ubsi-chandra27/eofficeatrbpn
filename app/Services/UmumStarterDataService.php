<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UmumStarterDataService
{
    public function ensureForUser(User $user): void
    {
        if ($user->role !== 'umum') {
            return;
        }

        $submissions = [
            ['INFO', 'Permohonan Informasi', 'Informasi jadwal dan prosedur pelayanan', 'diajukan', null, 4],
            ['DOK', 'Permohonan Dokumen', 'Permohonan salinan informasi layanan', 'diproses', 'Dokumen sedang diperiksa oleh bagian administrasi.', 3],
            ['ADU', 'Pengaduan', 'Kendala akses informasi pelayanan', 'dikembalikan', 'Mohon lengkapi waktu kejadian dan tangkapan layar kendala.', 2],
            ['SRT', 'Penyampaian Surat', 'Penyampaian surat permohonan koordinasi', 'selesai', 'Surat telah diterima dan dicatat oleh bagian administrasi.', 1],
        ];

        DB::transaction(function () use ($user, $submissions) {
            foreach ($submissions as $index => [$code, $category, $subject, $status, $note, $daysAgo]) {
                $date = now()->subDays($daysAgo);
                $number = sprintf('UMUM/DEMO/%05d/%s', $user->id, $code);

                $letter = Surat::updateOrCreate(
                    ['nomor_surat' => $number],
                    [
                        'user_id' => $user->id,
                        'jenis_surat' => 'masuk',
                        'kategori_pengajuan' => $category,
                        'nomor_kontak' => $user->phone ?: '0812 0000 0000',
                        'asal_instansi' => $user->organization ?: 'Perorangan',
                        'tanggal_surat' => $date->toDateString(),
                        'perihal' => $subject,
                        'deskripsi' => 'Data demonstrasi pengajuan masyarakat untuk pengujian alur dashboard.',
                        'asal_surat' => $user->name,
                        'tujuan_surat' => 'Administrasi Umum',
                        'metode' => 'Sistem',
                        'is_priority' => $index === 2,
                        'status' => $status,
                        'catatan_admin' => $note,
                    ]
                );

                $letter->timestamps = false;
                $letter->created_at = $date;
                $letter->updated_at = $date;
                $letter->saveQuietly();

                LogAktivitas::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'surat_id' => $letter->id,
                        'action' => 'Status Pengajuan: '.$letter->status_label,
                    ],
                    ['description' => $note ?: 'Pengajuan '.$letter->nomor_surat.' berstatus '.$letter->status_label.'.']
                );
            }
        });
    }
}
