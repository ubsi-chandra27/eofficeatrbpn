<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([JabatanSeeder::class, UnitKerjaSeeder::class]);

        if ($email = env('INITIAL_ADMIN_EMAIL')) {
            $nip = env('INITIAL_ADMIN_NIP');
            $password = env('INITIAL_ADMIN_PASSWORD');

            if (! $nip || ! $password || strlen($password) < 8) {
                $this->command?->warn('Admin awal tidak dibuat: lengkapi NIP dan password minimal 8 karakter.');
            } else {
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => env('INITIAL_ADMIN_NAME', 'Administrator'),
                        'nip' => $nip,
                        'password' => Hash::make($password),
                        'role' => 'admin',
                    ]
                );
                $this->command?->info('Akun Admin awal berhasil disiapkan.');
            }
        }

        if ((bool) env('SEED_DEMO_DATA', false)) {
            $this->call([PegawaiSeeder::class, SuratSeeder::class, DisposisiSeeder::class]);
        }
    }
}
