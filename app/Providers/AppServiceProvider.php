<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // 1. Gate "Super Admin": Memberikan akses penuh kepada admin
        // Memeriksa kolom 'role' langsung di database
        Gate::before(function ($user, $ability) {
            return $user->role === 'admin' ? true : null;
        });

        // 2. Definisi Gate untuk Pegawai
        Gate::define('pegawai', function ($user) {
            return $user->role === 'pegawai';
        });

        // 3. Definisi Gate untuk Umum
        Gate::define('umum', function ($user) {
            return $user->role === 'umum';
        });
    }
}
