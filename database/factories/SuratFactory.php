<?php

namespace Database\Factories;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuratFactory extends Factory
{
    protected $model = Surat::class;

    public function definition(): array
    {
        $jenis = ['masuk', 'keluar'];

        return [
            'nomor_surat' => fake()->unique()->bothify('BPN/??/####/2026'),
            'jenis_surat' => fake()->randomElement($jenis),
            'perihal' => fake()->sentence(4),
            'asal_surat' => fake()->company(),
            'tujuan_surat' => fake()->company(),
            'tanggal_surat' => fake()->dateTimeBetween('-1 month', 'now'),
            'deskripsi' => fake()->paragraph(),
            'status' => 'draft',
            'metode' => 'Sistem',
            'user_id' => User::factory(),
        ];
    }
    
    // State khusus untuk surat masuk agar lebih mudah dipanggil di Seeder
    public function masuk(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => 'masuk',
        ]);
    }
}
