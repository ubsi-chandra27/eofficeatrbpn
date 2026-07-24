<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Services\PegawaiStarterDataService;
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

        $starterData = app(PegawaiStarterDataService::class);
        foreach ($pegawaiList as $pegawai) {
            $starterData->ensureForPegawai($pegawai);
        }

        $this->command?->info(($pegawaiList->count() * 5).' surat masuk pegawai dengan status bervariasi berhasil disiapkan.');
    }
}
