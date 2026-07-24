<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return;
        }

        Berita::updateOrCreate(
            ['judul' => 'Pelayanan Digital ATR/BPN'],
            [
                'user_id' => $admin->id,
                'isi' => 'Layanan pertanahan dan penataan ruang kini dapat diakses secara digital. Masyarakat dapat mengajukan permohonan secara online melalui sistem e-Office.',
                'kategori' => 'berita',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ]
        );

        Berita::updateOrCreate(
            ['judul' => 'Pemutihan Pajak Bumi dan Bangunan'],
            [
                'user_id' => $admin->id,
                'isi' => 'Program pemutihan PBB tahun ini telah dimulai. Pastikan data kepemilikan tanah dan bangunan terverifikasi sebelum mengajukan permohonan.',
                'kategori' => 'pengumuman',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ]
        );

        Berita::updateOrCreate(
            ['judul' => 'Jadwal Pelayanan Terbaru'],
            [
                'user_id' => $admin->id,
                'isi' => 'Pelayanan administrasi pertanahan dan tata ruang beroperasional setiap hari kerja mulai pukul 08.00 hingga 16.00 WIB.',
                'kategori' => 'pengumuman',
                'is_published' => true,
                'published_at' => now()->subHours(5),
            ]
        );
    }
}
