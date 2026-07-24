<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Services\PegawaiStarterDataService;
use Illuminate\Database\Seeder;

class DisposisiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Pegawai::with(['user', 'unitKerja'])
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
            ->orderBy('nip')
            ->get();

        if ($employees->isEmpty()) {
            $this->command?->error('Akun pegawai demonstrasi belum tersedia.');
            return;
        }

        $starterData = app(PegawaiStarterDataService::class);
        foreach ($employees as $employee) {
            $starterData->ensureForPegawai($employee);
        }

        $this->command?->info('Disposisi demonstrasi masuk dan terkirim berhasil disiapkan untuk pegawai.');
    }
}
