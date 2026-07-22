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
        $admin = User::where('role', 'admin')->first();
        $letters = Surat::where('jenis_surat', 'masuk')->where('nomor_surat', 'like', 'DEMO/%')->oldest('created_at')->take(5)->get();
        $employees = Pegawai::whereNotNull('user_id')->orderBy('nama')->get();

        if (! $admin || $letters->isEmpty() || $employees->isEmpty()) {
            $this->command?->error('Siapkan akun Admin, surat demo, dan pegawai demo terlebih dahulu.');
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

        DB::transaction(function () use ($admin, $letters, $employees, $notes, $priorities, $statuses) {
            $primaryRecipient = $employees->firstWhere('nip', '198801010001') ?? $employees->first();

            foreach ($letters as $index => $letter) {
                $disposition = Disposisi::updateOrCreate(
                    ['surat_id' => $letter->id, 'catatan' => $notes[$index]],
                    [
                        'pengirim_id' => $admin->id,
                        'prioritas' => $priorities[$index],
                        'tanggal_disposisi' => now()->subDays(4 - $index)->toDateString(),
                    ]
                );

                DisposisiTujuan::where('disposisi_id', $disposition->id)->delete();
                $status = $statuses[$index];
                DisposisiTujuan::create([
                    'disposisi_id' => $disposition->id,
                    'pegawai_id' => $primaryRecipient->id,
                    'status' => $status,
                    'dibaca_pada' => $status === 'Belum Dibaca' ? null : now()->subHours(6),
                    'selesai_pada' => $status === 'Selesai' ? now()->subHour() : null,
                ]);

                $secondaryRecipient = $employees[$index % $employees->count()];
                if ($secondaryRecipient->id !== $primaryRecipient->id) {
                    DisposisiTujuan::create([
                        'disposisi_id' => $disposition->id,
                        'pegawai_id' => $secondaryRecipient->id,
                        'status' => 'Belum Dibaca',
                    ]);
                }
            }
        });

        $this->command?->info('Lima disposisi demonstrasi dengan status bervariasi berhasil disiapkan.');
    }
}
