<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeploymentCheck extends Command
{
    protected $signature = 'app:deployment-check';
    protected $description = 'Memeriksa konfigurasi dan aset penting sebelum aplikasi dipublikasikan';

    public function handle(): int
    {
        $checks = [
            ['APP_ENV production', app()->environment('production'), app()->environment()],
            ['APP_DEBUG nonaktif', ! config('app.debug'), config('app.debug') ? 'true' : 'false'],
            ['APP_URL memakai HTTPS', str_starts_with((string) config('app.url'), 'https://'), config('app.url')],
            ['APP_FORCE_HTTPS aktif', (bool) config('app.force_https'), config('app.force_https') ? 'true' : 'false'],
            ['Registrasi staf nonaktif', ! config('registration.allow_staff_registration'), config('registration.allow_staff_registration') ? 'aktif' : 'nonaktif'],
            ['Build Vite tersedia', is_file(public_path('build/manifest.json')), public_path('build/manifest.json')],
            ['Storage link tersedia', is_dir(public_path('storage')), public_path('storage')],
            ['Foto menteri tersedia', $this->assetExists('images/menteri.jpg'), 'public/images/menteri.jpg'],
            ['Foto wakil menteri tersedia', $this->assetExists('images/wakil-menteri.jpg'), 'public/images/wakil-menteri.jpg'],
            ['Struktur organisasi tersedia', $this->assetExists('images/struktur-organisasi.png'), 'public/images/struktur-organisasi.png'],
        ];

        try {
            DB::connection()->getPdo();
            $checks[] = ['Koneksi database', true, DB::connection()->getDatabaseName()];
        } catch (\Throwable $exception) {
            $checks[] = ['Koneksi database', false, $exception->getMessage()];
        }

        $failed = false;
        $rows = array_map(function (array $check) use (&$failed) {
            $failed = $failed || ! $check[1];
            return [$check[1] ? 'LULUS' : 'GAGAL', $check[0], (string) $check[2]];
        }, $checks);

        $this->table(['Status', 'Pemeriksaan', 'Nilai'], $rows);

        if ($failed) {
            $this->error('Aplikasi belum siap dipublikasikan. Perbaiki pemeriksaan yang berstatus GAGAL.');
            return self::FAILURE;
        }

        $this->info('Seluruh pemeriksaan deployment lulus.');
        return self::SUCCESS;
    }

    private function assetExists(string $path): bool
    {
        $file = public_path($path);
        return is_file($file) && filesize($file) > 0;
    }
}
