<?php

use App\Models\User;

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
