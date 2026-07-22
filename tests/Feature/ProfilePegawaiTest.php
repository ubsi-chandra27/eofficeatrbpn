<?php

use App\Models\Pegawai;
use App\Models\User;

function createProfilePegawai(): array
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => '198801010099']);
    $pegawai = Pegawai::create(['user_id' => $user->id, 'nip' => $user->nip, 'nama' => $user->name, 'email' => $user->email]);
    return [$user, $pegawai];
}

it('menampilkan profil khusus pegawai', function () {
    [$user] = createProfilePegawai();
    $this->actingAs($user)->get(route('profile.edit'))->assertOk()
        ->assertSee('Informasi Pegawai')->assertSee('Keamanan Login')->assertSee($user->nip);
});

it('menyinkronkan kontak akun dengan data pegawai', function () {
    [$user, $pegawai] = createProfilePegawai();
    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Pegawai Diperbarui', 'email' => 'pegawai.update@example.test',
        'phone' => '0812 0000 1111', 'address' => 'Alamat pegawai diperbarui',
    ])->assertRedirect(route('profile.edit'));

    $user->refresh(); $pegawai->refresh();
    expect($user->phone)->toBe('0812 0000 1111')->and($pegawai->no_hp)->toBe($user->phone)
        ->and($pegawai->nama)->toBe($user->name)->and($pegawai->alamat)->toBe($user->address);
});
