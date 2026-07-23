<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\UnitKerja;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        Pegawai::withTrashed()->forceDelete();

        $kepalaKantor = Jabatan::where('nama', 'Kepala Kantor')->first();
        $sekretaris   = Jabatan::where('nama', 'Sekretaris')->first();
        $analis       = Jabatan::where('nama', 'Analis Pertanahan')->first();
        $staf         = Jabatan::where('nama', 'Staf Administrasi')->first();
        $kasubag      = Jabatan::where('nama', 'Kepala Subbagian')->first();

        $sekretariat = UnitKerja::where('nama', 'Sekretariat')->first();
        $tu          = UnitKerja::where('nama', 'Subbagian Tata Usaha')->first();
        $hak         = UnitKerja::where('nama', 'Seksi Penetapan Hak')->first();
        $survei      = UnitKerja::where('nama', 'Seksi Survei dan Pemetaan')->first();
        $pengadaan   = UnitKerja::where('nama', 'Seksi Pengadaan Tanah')->first();

        Pegawai::insert([
            [
                'nip' => '198801010001',
                'nipp' => 'NIPP-0001',
                'nama' => 'Budi Santoso',
                'email' => 'budi@atrbpn.com',
                'no_hp' => '081234567890',
                'alamat' => 'Jakarta',
                'jabatan_id' => $kepalaKantor?->id,
                'unit_kerja_id' => $sekretariat?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198801010002',
                'nipp' => 'NIPP-0002',
                'nama' => 'Siti Aminah',
                'email' => 'siti@atrbpn.com',
                'no_hp' => '081234567891',
                'alamat' => 'Bandung',
                'jabatan_id' => $sekretaris?->id,
                'unit_kerja_id' => $tu?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198801010003',
                'nipp' => 'NIPP-0003',
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad@atrbpn.com',
                'no_hp' => '081234567892',
                'alamat' => 'Bogor',
                'jabatan_id' => $analis?->id,
                'unit_kerja_id' => $hak?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198801010004',
                'nipp' => 'NIPP-0004',
                'nama' => 'Dewi Lestari',
                'email' => 'dewi@atrbpn.com',
                'no_hp' => '081234567893',
                'alamat' => 'Depok',
                'jabatan_id' => $staf?->id,
                'unit_kerja_id' => $survei?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '198801010005',
                'nipp' => 'NIPP-0005',
                'nama' => 'Rudi Hartono',
                'email' => 'rudi@atrbpn.com',
                'no_hp' => '081234567894',
                'alamat' => 'Bekasi',
                'jabatan_id' => $kasubag?->id,
                'unit_kerja_id' => $pengadaan?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Pegawai berhasil ditambahkan: ' . Pegawai::count());
    }
}
