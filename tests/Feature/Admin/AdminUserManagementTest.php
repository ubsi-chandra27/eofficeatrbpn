<?php

use App\Models\User;
use App\Models\Pegawai;

it('menghapus pengguna lain dengan soft delete tanpa error', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-001']);
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('menolak role pegawai untuk akun yang belum terhubung data pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-002']);
    $user = User::factory()->create(['role' => 'umum', 'nip' => 'PEG-TEST-001']);

    $this->actingAs($admin)
        ->patch(route('admin.users.updateRole', $user->id), ['role' => 'pegawai'])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($user->fresh()->role)->toBe('umum');
});

it('melindungi akun admin yang sedang digunakan', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-003']);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin->id))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($admin->fresh())->not->toBeNull();
});

it('menampilkan NIP dari profil pegawai ketika NIP akun masih kosong', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-004']);
    $pegawaiUser = User::factory()->create([
        'role' => 'pegawai',
        'nip' => null,
        'name' => 'Pegawai Dengan NIP Profil',
    ]);
    Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => '19876543210001',
        'nama' => $pegawaiUser->name,
        'email' => $pegawaiUser->email,
    ]);

    $this->actingAs($admin)->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Pegawai Dengan NIP Profil')
        ->assertSee('19876543210001')
        ->assertSee('Diambil dari profil pegawai');
});

it('mencari pengguna berdasarkan NIP profil pegawai dan memfilter role', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-005']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'nip' => null, 'name' => 'Target Pencarian']);
    Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => 'NIP-CARI-7788',
        'nama' => $pegawaiUser->name,
        'email' => $pegawaiUser->email,
    ]);
    User::factory()->create(['role' => 'umum', 'name' => 'Tidak Boleh Tampil']);

    $this->actingAs($admin)->get(route('admin.users.index', [
        'keyword' => 'NIP-CARI-7788',
        'role' => 'pegawai',
        'profil' => 'terhubung',
    ]))
        ->assertOk()
        ->assertSee('Target Pencarian')
        ->assertDontSee('Tidak Boleh Tampil');
});

it('mengizinkan role pegawai menggunakan NIP dari profil dan menyinkronkannya ke akun', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-006']);
    $user = User::factory()->create(['role' => 'umum', 'nip' => null]);
    Pegawai::create([
        'user_id' => $user->id,
        'nip' => 'NIP-SINKRON-001',
        'nama' => $user->name,
        'email' => $user->email,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.users.updateRole', $user), ['role' => 'pegawai'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->role)->toBe('pegawai')
        ->and($user->fresh()->nip)->toBe('NIP-SINKRON-001');
});

it('memperbarui NIP akun dan profil pegawai secara bersamaan', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-007']);
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => 'NIP-LAMA']);
    $pegawai = Pegawai::create([
        'user_id' => $user->id,
        'nip' => 'NIP-LAMA',
        'nama' => $user->name,
        'email' => $user->email,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.users.updateNip', $user), ['nip' => 'NIP-BARU-2026'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->nip)->toBe('NIP-BARU-2026')
        ->and($pegawai->fresh()->nip)->toBe('NIP-BARU-2026');
});

it('menolak reset password akun admin yang sedang digunakan', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-TEST-008']);
    $passwordLama = $admin->password;

    $this->actingAs($admin)->patch(route('admin.users.resetPassword', $admin), [
        'password' => 'PasswordBaru123!',
        'password_confirmation' => 'PasswordBaru123!',
    ])->assertSessionHas('error');

    expect($admin->fresh()->password)->toBe($passwordLama);
});
