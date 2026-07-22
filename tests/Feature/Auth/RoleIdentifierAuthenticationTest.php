<?php

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('mengizinkan admin dan pegawai masuk menggunakan nip', function (string $role) {
    $user = User::factory()->create([
        'role' => $role,
        'nip' => '19880101000'.($role === 'admin' ? '1' : '2'),
        'password' => Hash::make('password'),
    ]);

    $this->post('/login', [
        'login_as' => $role,
        'identifier' => $user->nip,
        'password' => 'password',
    ])->assertRedirect(route($role.'.dashboard'));

    $this->assertAuthenticatedAs($user);
})->with(['admin', 'pegawai']);

it('tetap mengizinkan pengguna umum masuk menggunakan email', function () {
    $user = User::factory()->create(['role' => 'umum', 'password' => Hash::make('password')]);

    $this->post('/login', [
        'login_as' => 'umum',
        'identifier' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('umum.dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('membuat akun dan data pegawai ketika pegawai mendaftar', function () {
    config()->set('registration.allow_staff', true);

    $this->post('/register', [
        'role' => 'pegawai',
        'name' => 'Pegawai Baru',
        'nip' => '199901010001',
        'email' => 'pegawai.baru@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('pegawai.dashboard'));

    $user = User::where('email', 'pegawai.baru@example.test')->firstOrFail();
    expect($user->role)->toBe('pegawai')->and($user->nip)->toBe('199901010001');
    expect(Pegawai::where('user_id', $user->id)->where('nip', $user->nip)->exists())->toBeTrue();
});

it('menolak registrasi staf ketika fitur registrasi staf dinonaktifkan', function () {
    config()->set('registration.allow_staff', false);

    $this->post('/register', [
        'role' => 'admin',
        'name' => 'Admin Tidak Sah',
        'nip' => '199901010099',
        'email' => 'admin.tidak.sah@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('role');

    expect(User::where('email', 'admin.tidak.sah@example.test')->exists())->toBeFalse();
});
