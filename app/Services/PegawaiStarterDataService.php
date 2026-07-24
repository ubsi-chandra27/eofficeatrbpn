<?php

namespace App\Services;

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PegawaiStarterDataService
{
    public function needsStarterData(Pegawai $pegawai): bool
    {
        if (! $pegawai->user_id) {
            return false;
        }

        $hasSuratMasuk = Surat::where('user_id', $pegawai->user_id)
            ->where('jenis_surat', 'masuk')
            ->exists();
        $hasDisposisiMasuk = DisposisiTujuan::where('pegawai_id', $pegawai->id)->exists();

        return ! $hasSuratMasuk || ! $hasDisposisiMasuk;
    }

    public function ensureForPegawai(Pegawai $pegawai): void
    {
        if (! $pegawai->user_id) {
            return;
        }

        $pegawai->loadMissing(['user', 'unitKerja']);

        DB::transaction(function () use ($pegawai) {
            $letters = $this->ensureSuratMasuk($pegawai);
            $this->ensureDisposisiMasuk($pegawai, $letters);
            $this->ensureDisposisiTerkirim($pegawai);
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, Surat>
     */
    private function ensureSuratMasuk(Pegawai $pegawai)
    {
        $rows = [
            ['001', 'AGD-PGW-001', 'Permohonan data pertanahan', 'Instansi Pemerintah', 'draft', null],
            ['002', 'AGD-PGW-002', 'Undangan rapat koordinasi', 'Kantor Wilayah', 'diajukan', null],
            ['003', 'AGD-PGW-003', 'Pemberitahuan pembaruan dokumen', 'Bagian Tata Usaha', 'diverifikasi', null],
            ['004', 'AGD-PGW-004', 'Permohonan klarifikasi berkas', 'Masyarakat', 'dikembalikan', 'Lengkapi identitas pengirim dan lampiran pendukung.'],
            ['005', 'AGD-PGW-005', 'Laporan kegiatan pelayanan', 'Seksi Pelayanan', 'diteruskan_ke_pimpinan', null],
        ];

        return collect($rows)->map(function (array $row, int $index) use ($pegawai) {
            [$sequence, $agenda, $subject, $origin, $status, $note] = $row;
            $date = now()->subDays(5 - $index);
            $identifier = $pegawai->nip ?: 'USER-'.$pegawai->user_id;

            return Surat::updateOrCreate(
                ['nomor_surat' => "DEMO/PGW/SM/{$identifier}/{$sequence}/".$date->format('Y')],
                [
                    'user_id' => $pegawai->user_id,
                    'jenis_surat' => 'masuk',
                    'nomor_agenda' => "{$agenda}-{$identifier}",
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
        });
    }

    private function ensureDisposisiMasuk(Pegawai $pegawai, $letters): void
    {
        $sender = $this->systemSender($pegawai);
        $notes = [
            'Pelajari isi surat dan siapkan ringkasan tindak lanjut.',
            'Koordinasikan dengan unit terkait dan laporkan hasilnya.',
            'Periksa kelengkapan berkas sebelum proses berikutnya.',
            'Siapkan konsep jawaban untuk pemeriksaan administrator.',
            'Arsipkan dokumen setelah seluruh tindak lanjut selesai.',
        ];
        $priorities = ['Tinggi', 'Sedang', 'Sedang', 'Rendah', 'Tinggi'];
        $statuses = ['Belum Dibaca', 'Sudah Dibaca', 'Selesai', 'Belum Dibaca', 'Selesai'];

        foreach ($letters as $index => $letter) {
            $status = $statuses[$index % count($statuses)];
            $disposisi = Disposisi::updateOrCreate(
                [
                    'surat_id' => $letter->id,
                    'pengirim_id' => $sender->id,
                    'catatan' => $notes[$index % count($notes)],
                ],
                [
                    'prioritas' => $priorities[$index % count($priorities)],
                    'tanggal_disposisi' => now()->subDays(6 - $index)->toDateString(),
                ]
            );

            DisposisiTujuan::updateOrCreate(
                [
                    'disposisi_id' => $disposisi->id,
                    'pegawai_id' => $pegawai->id,
                ],
                [
                    'status' => $status,
                    'dibaca_pada' => $status === 'Belum Dibaca' ? null : now()->subHours(6),
                    'selesai_pada' => $status === 'Selesai' ? now()->subHour() : null,
                ]
            );
        }
    }

    private function ensureDisposisiTerkirim(Pegawai $pegawai): void
    {
        $recipient = Pegawai::whereNotNull('user_id')
            ->where('id', '!=', $pegawai->id)
            ->orderBy('id')
            ->first();

        if (! $recipient) {
            return;
        }

        $letter = Surat::where('jenis_surat', 'masuk')
            ->where('user_id', $pegawai->user_id)
            ->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'])
            ->where('nomor_surat', 'like', 'DEMO/PGW/SM/%')
            ->orderBy('tanggal_surat')
            ->first();

        if (! $letter) {
            return;
        }

        $sent = Disposisi::updateOrCreate(
            [
                'surat_id' => $letter->id,
                'pengirim_id' => $pegawai->user_id,
                'catatan' => 'Mohon tindak lanjuti surat demo dari '.$pegawai->nama.'.',
            ],
            [
                'prioritas' => 'Sedang',
                'tanggal_disposisi' => now()->toDateString(),
            ]
        );

        DisposisiTujuan::updateOrCreate(
            [
                'disposisi_id' => $sent->id,
                'pegawai_id' => $recipient->id,
            ],
            [
                'status' => 'Belum Dibaca',
                'dibaca_pada' => null,
                'selesai_pada' => null,
            ]
        );
    }

    private function systemSender(Pegawai $pegawai): User
    {
        return User::where('role', 'admin')->orderBy('id')->first()
            ?? User::where('id', '!=', $pegawai->user_id)->orderBy('id')->first()
            ?? $pegawai->user;
    }
}
