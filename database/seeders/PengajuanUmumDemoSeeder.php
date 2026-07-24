<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UmumStarterDataService;
use Illuminate\Database\Seeder;

class PengajuanUmumDemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'umum')->orderBy('id')->get();

        if ($users->isEmpty()) {
            $this->command?->warn('Belum ada akun masyarakat umum untuk diisi data demonstrasi.');

            return;
        }

        $starterData = app(UmumStarterDataService::class);
        foreach ($users as $user) {
            $starterData->ensureForUser($user);
        }

        $this->command?->info('Empat pengajuan demonstrasi beserta aktivitasnya tersedia untuk setiap akun umum.');
    }
}
