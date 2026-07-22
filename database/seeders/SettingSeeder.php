<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'registration.allow_staff',
                'value' => '1',
                'group' => 'registration',
            ],
            [
                'key' => 'app_name',
                'value' => 'E-Office',
                'group' => 'general',
            ],
            [
                'key' => 'app_subtitle',
                'value' => 'Administrasi Digital',
                'group' => 'general',
            ],
        ];

        foreach ($defaults as $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
