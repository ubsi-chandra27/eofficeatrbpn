<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disposisi;
use App\Models\Surat;
use App\Models\User;

class DisposisiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Disposisi::withTrashed()->forceDelete();

        $surat = Surat::first();
        $pengirim = User::where('role', '!=', 'umum')->first();

        if (!$surat || !$pengirim) {
            $this->command->error('Data surat atau pengguna tidak ditemukan.');
            return;
        }

        $data = [
            [
                'surat_id' => $surat->id,
                'pengirim_id' => $pengirim->id,
                'catatan' => 'Segera diproses oleh bagian tata usaha.',
                'prioritas' => 'Tinggi',
                'tanggal_disposisi' => now(),
            ],
            [
                'surat_id' => $surat->id,
                'pengirim_id' => $pengirim->id,
                'catatan' => 'Pelajari isi surat dan buat balasan.',
                'prioritas' => 'Sedang',
                'tanggal_disposisi' => now()->subDay(),
            ],
            [
                'surat_id' => $surat->id,
                'pengirim_id' => $pengirim->id,
                'catatan' => 'Arsipkan setelah selesai.',
                'prioritas' => 'Rendah',
                'tanggal_disposisi' => now()->subDays(2),
            ],
        ];

        foreach ($data as $item) {
            Disposisi::create($item);
        }

        $this->command->info(
            'Disposisi berhasil ditambahkan: '.Disposisi::count()
        );
    }
}