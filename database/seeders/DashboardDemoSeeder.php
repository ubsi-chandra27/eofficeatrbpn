<?php

namespace Database\Seeders;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Seeder;

class DashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $this->command?->error('Akun Admin belum tersedia.');
            return;
        }

        $statuses = ['diajukan', 'diverifikasi', 'dikembalikan', 'diteruskan_ke_pimpinan', 'diproses', 'selesai'];

        for ($month = 5; $month >= 0; $month--) {
            foreach (['masuk', 'keluar'] as $index => $type) {
                $date = now()->startOfMonth()->subMonths($month)->addDays(5 + ($index * 7));
                $number = sprintf('DEMO/%s/%s-%02d', strtoupper($type), $date->format('Ym'), $index + 1);
                $letter = Surat::updateOrCreate(
                    ['nomor_surat' => $number],
                    [
                        'user_id' => $admin->id,
                        'jenis_surat' => $type,
                        'tanggal_surat' => $date->toDateString(),
                        'perihal' => $type === 'masuk' ? 'Contoh surat masuk bulanan' : 'Contoh surat keluar bulanan',
                        'asal_surat' => $type === 'masuk' ? 'Instansi Pengirim' : 'Administrasi',
                        'tujuan_surat' => $type === 'masuk' ? 'Administrasi' : 'Instansi Tujuan',
                        'metode' => 'Sistem',
                        'deskripsi' => 'Data demonstrasi untuk grafik dashboard.',
                        'is_priority' => false,
                        'status' => $statuses[($month + $index) % count($statuses)],
                    ]
                );

                $letter->timestamps = false;
                $letter->created_at = $date;
                $letter->updated_at = $date;
                $letter->saveQuietly();
            }
        }

        $this->command?->info('Data grafik demonstrasi enam bulan berhasil disiapkan tanpa menghapus surat lain.');
    }
}
