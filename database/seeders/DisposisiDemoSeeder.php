<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisposisiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $systemSender = User::where('role', 'admin')->first()
            ?? User::whereIn('role', ['pegawai', 'admin'])->orderBy('id')->first();
        $employees = Pegawai::with(['user', 'unitKerja'])
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
            ->orderBy('nip')
            ->get();

        if (! $systemSender || $employees->count() < 2) {
            $this->command?->error('Siapkan minimal dua akun pegawai demonstrasi sebelum membuat disposisi demo.');
            return;
        }

        $notes = [
            'Pelajari isi surat dan siapkan ringkasan tindak lanjut.',
            'Koordinasikan dengan unit terkait dan laporkan hasilnya.',
            'Periksa kelengkapan berkas sebelum proses berikutnya.',
            'Siapkan konsep jawaban untuk pemeriksaan administrator.',
            'Arsipkan dokumen setelah seluruh tindak lanjut selesai.',
        ];
        $priorities = ['Tinggi', 'Sedang', 'Sedang', 'Rendah', 'Tinggi'];
        $statuses = ['Belum Dibaca', 'Sudah Dibaca', 'Selesai', 'Belum Dibaca', 'Selesai'];

        DB::transaction(function () use ($systemSender, $employees, $notes, $priorities, $statuses) {
            foreach ($employees as $employeeIndex => $employee) {
                $letters = Surat::where('jenis_surat', 'masuk')
                    ->where('user_id', $employee->user_id)
                    ->where('nomor_surat', 'like', 'DEMO/PGW/SM/%')
                    ->orderBy('tanggal_surat')
                    ->take(5)
                    ->get();

                if ($letters->isEmpty()) {
                    continue;
                }

                foreach ($letters as $index => $letter) {
                    $status = $statuses[$index % count($statuses)];
                    $disposition = Disposisi::updateOrCreate(
                        [
                            'surat_id' => $letter->id,
                            'pengirim_id' => $systemSender->id,
                            'catatan' => $notes[$index % count($notes)],
                        ],
                        [
                            'prioritas' => $priorities[$index % count($priorities)],
                            'tanggal_disposisi' => now()->subDays(6 - $index + $employeeIndex)->toDateString(),
                        ]
                    );

                    DisposisiTujuan::updateOrCreate(
                        [
                            'disposisi_id' => $disposition->id,
                            'pegawai_id' => $employee->id,
                        ],
                        [
                            'status' => $status,
                            'dibaca_pada' => $status === 'Belum Dibaca' ? null : now()->subHours(6),
                            'selesai_pada' => $status === 'Selesai' ? now()->subHour() : null,
                        ]
                    );
                }
            }

            foreach ($employees as $index => $senderEmployee) {
                $recipientEmployee = $employees[($index + 1) % $employees->count()];
                $letter = Surat::where('jenis_surat', 'masuk')
                    ->where('user_id', $senderEmployee->user_id)
                    ->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'])
                    ->where('nomor_surat', 'like', 'DEMO/PGW/SM/%')
                    ->orderBy('tanggal_surat')
                    ->first();

                if (! $letter || ! $senderEmployee->user) {
                    continue;
                }

                $sentDisposition = Disposisi::updateOrCreate(
                    [
                        'surat_id' => $letter->id,
                        'pengirim_id' => $senderEmployee->user_id,
                        'catatan' => 'Mohon tindak lanjuti surat demo dari '.$senderEmployee->nama.'.',
                    ],
                    [
                        'prioritas' => $priorities[$index % count($priorities)],
                        'tanggal_disposisi' => now()->subDays($index)->toDateString(),
                    ]
                );

                DisposisiTujuan::updateOrCreate(
                    [
                        'disposisi_id' => $sentDisposition->id,
                        'pegawai_id' => $recipientEmployee->id,
                    ],
                    [
                        'status' => 'Belum Dibaca',
                        'dibaca_pada' => null,
                        'selesai_pada' => null,
                    ]
                );
            }
        });

        $this->command?->info('Disposisi demonstrasi masuk dan terkirim berhasil disiapkan untuk pegawai.');
    }
}
