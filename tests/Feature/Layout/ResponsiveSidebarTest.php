<?php

use App\Models\Pegawai;
use App\Models\User;

it('menyediakan kontrol sidebar mobile yang aksesibel untuk setiap role', function (string $role, string $route, string $toggleId, string $label) {
    $user = User::factory()->create(array_filter([
        'role' => $role,
        'nip' => $role === 'pegawai' ? '198812120099' : null,
    ]));
    if ($role === 'pegawai') {
        Pegawai::create([
            'user_id' => $user->id,
            'nip' => $user->nip,
            'nama' => $user->name,
            'email' => $user->email,
        ]);
    }

    $this->actingAs($user)->get(route($route))
        ->assertOk()
        ->assertSee('id="sidebar"', false)
        ->assertSee('aria-label="'.$label.'"', false)
        ->assertSee('id="'.$toggleId.'"', false)
        ->assertSee('aria-controls="sidebar"', false)
        ->assertSee('aria-expanded="false"', false)
        ->assertSee('aria-label="Buka menu navigasi"', false);
})->with([
    'administrator' => ['admin', 'admin.dashboard', 'toggleSidebar', 'Menu utama Administrator'],
    'pegawai' => ['pegawai', 'pegawai.dashboard', 'toggleSidebar', 'Menu utama Pegawai'],
    'masyarakat umum' => ['umum', 'umum.dashboard', 'menuToggle', 'Menu utama Masyarakat Umum'],
]);

it('memuat perilaku buka tutup sidebar mobile termasuk backdrop dan tombol escape', function () {
    $adminLayout = file_get_contents(resource_path('views/layouts/admin.blade.php'));
    $pegawaiLayout = file_get_contents(resource_path('views/layouts/pegawai.blade.php'));
    $umumScript = file_get_contents(public_path('js/dashboard-umum.js'));

    foreach ([$adminLayout, $pegawaiLayout, $umumScript] as $source) {
        expect($source)
            ->toContain('sidebar-backdrop')
            ->toContain('sidebar-mobile-open')
            ->toContain('Escape')
            ->toContain('aria-expanded');
    }
});
