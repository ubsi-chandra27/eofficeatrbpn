<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['198801010001', 'Budi Santoso', 'budi.demo@eoffice.test', '081234567890', 'Kepala Kantor', 'Sekretariat'],
            ['198801010002', 'Siti Aminah', 'siti.demo@eoffice.test', '081234567891', 'Sekretaris', 'Subbagian Tata Usaha'],
            ['198801010003', 'Ahmad Fauzi', 'ahmad.demo@eoffice.test', '081234567892', 'Analis Pertanahan', 'Seksi Penetapan Hak'],
            ['198801010004', 'Dewi Lestari', 'dewi.demo@eoffice.test', '081234567893', 'Staf Administrasi', 'Seksi Survei dan Pemetaan'],
            ['198801010005', 'Rudi Hartono', 'rudi.demo@eoffice.test', '081234567894', 'Kepala Subbagian', 'Seksi Pengadaan Tanah'],
        ];

        DB::transaction(function () use ($rows) {
            foreach ($rows as [$nip, $name, $email, $phone, $position, $unit]) {
                $jabatan = Jabatan::firstOrCreate(['nama' => $position]);
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $unit]);
                $user = User::updateOrCreate(
                    ['nip' => $nip],
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make('PegawaiDemo!2026'),
                        'role' => 'pegawai',
                    ]
                );

                Pegawai::updateOrCreate(
                    ['nip' => $nip],
                    [
                        'user_id' => $user->id,
                        'nama' => $name,
                        'email' => $email,
                        'no_hp' => $phone,
                        'alamat' => 'Data demonstrasi',
                        'jabatan_id' => $jabatan->id,
                        'unit_kerja_id' => $unitKerja->id,
                    ]
                );
            }
        });

        $this->command?->info('Lima pegawai demonstrasi beserta akun login berhasil disiapkan.');
    }
}
