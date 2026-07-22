<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('menampilkan profil khusus pengguna umum', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $this->actingAs($user)->get(route('profile.edit'))
        ->assertOk()->assertSee('Informasi Pribadi')->assertSee('Nonaktifkan Akun');
});

it('memperbarui kontak profil umum', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Pemohon Umum', 'email' => 'pemohon@example.test',
        'phone' => '0812 3456 7890', 'organization' => 'Komunitas Warga',
        'address' => 'Alamat korespondensi pemohon',
    ])->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->phone)->toBe('0812 3456 7890')
        ->and($user->organization)->toBe('Komunitas Warga');
});

it('mewajibkan password ketika menonaktifkan akun umum', function () {
    $user = User::factory()->create(['role' => 'umum', 'password' => Hash::make('password')]);
    $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'salah'])
        ->assertSessionHasErrors('password', null, 'userDeletion');
    expect($user->fresh())->not->toBeNull();
});

it('dapat menambah mengganti dan menghapus foto profil', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($user)->patch(route('profile.photo.update'), [
        'photo' => UploadedFile::fake()->image('profil.jpg', 400, 400),
    ])->assertRedirect(route('profile.edit'));
    $firstPhoto = $user->fresh()->profile_photo_path;
    Storage::disk('public')->assertExists($firstPhoto);

    $this->actingAs($user)->patch(route('profile.photo.update'), [
        'photo' => UploadedFile::fake()->image('profil-baru.png', 500, 500),
    ])->assertRedirect(route('profile.edit'));
    Storage::disk('public')->assertMissing($firstPhoto);

    $newPhoto = $user->fresh()->profile_photo_path;
    $this->actingAs($user)->delete(route('profile.photo.destroy'))->assertRedirect(route('profile.edit'));
    Storage::disk('public')->assertMissing($newPhoto);
    expect($user->fresh()->profile_photo_path)->toBeNull();
});
