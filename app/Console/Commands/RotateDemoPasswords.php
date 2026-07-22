<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RotateDemoPasswords extends Command
{
    protected $signature = 'app:rotate-demo-passwords {password : Password baru minimal 12 karakter}';
    protected $description = 'Mengganti password seluruh akun pada database demonstrasi';

    public function handle(): int
    {
        $password = (string) $this->argument('password');

        if (mb_strlen($password) < 12) {
            $this->error('Password minimal 12 karakter.');
            return self::FAILURE;
        }

        if (app()->environment('production')) {
            $this->error('Perintah ini tidak boleh dijalankan pada lingkungan production.');
            return self::FAILURE;
        }

        $count = User::query()->update(['password' => Hash::make($password)]);
        $this->info("Password {$count} akun demonstrasi berhasil diganti.");

        return self::SUCCESS;
    }
}
