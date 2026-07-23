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
            ['Registrasi staf nonaktif', ! config('registration.allow_staff'), config('registration.allow_staff') ? 'aktif' : 'nonaktif'],
            ['PHP minimal 8.3', version_compare(PHP_VERSION, '8.3.0', '>='), PHP_VERSION],
            ['Ekstensi PDO MySQL tersedia', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'tersedia' : 'tidak tersedia'],
            ['Ekstensi fileinfo tersedia', extension_loaded('fileinfo'), extension_loaded('fileinfo') ? 'tersedia' : 'tidak tersedia'],
            ['Lampiran memakai disk private', config('filesystems.default') === 'local', (string) config('filesystems.default')],
            ['Storage dapat ditulis', is_writable(storage_path()), storage_path()],
            ['Bootstrap cache dapat ditulis', is_writable(base_path('bootstrap/cache')), base_path('bootstrap/cache')],
            ['Build Vite tersedia', is_file(public_path('build/manifest.json')), public_path('build/manifest.json')],
            ['Storage link tersedia', $this->storageLinkIsValid(), public_path('storage')],
            ['Foto menteri tersedia', $this->assetExists('images/menteri.jpg'), 'public/images/menteri.jpg'],
            ['Foto wakil menteri tersedia', $this->assetExists('images/wakil-menteri.jpg'), 'public/images/wakil-menteri.jpg'],
            ['Struktur organisasi tersedia', $this->assetExists('images/struktur-organisasi.png'), 'public/images/struktur-organisasi.png'],
            ['Logo aplikasi tersedia', $this->assetExists('images/logo-eoffice.svg'), 'public/images/logo-eoffice.svg'],
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

    private function storageLinkIsValid(): bool
    {
        $publicStorage = public_path('storage');

        if (! is_dir($publicStorage)) {
            return false;
        }

        // Windows menggunakan junction, sedangkan hosting Linux menggunakan symlink.
        return realpath($publicStorage) === realpath(storage_path('app/public'));
    }
}
