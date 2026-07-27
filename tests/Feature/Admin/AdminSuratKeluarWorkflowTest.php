<?php

use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('admin dapat menyetujui surat keluar yang diajukan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::factory()->create([
        'jenis_surat' => 'keluar',
        'status' => 'diajukan',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.keluar.setujui', $surat->id), [
            'catatan_admin' => 'Sudah lengkap',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('diverifikasi');
});

it('admin dapat mengembalikan surat keluar yang diajukan dengan catatan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::factory()->create([
        'jenis_surat' => 'keluar',
        'status' => 'diajukan',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.keluar.tolak', $surat->id), [
            'catatan_admin' => 'Perbaiki nomor agenda',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('dikembalikan');
});

it('admin tidak bisa menyetujui surat keluar yang bukan diajukan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::factory()->create([
        'jenis_surat' => 'keluar',
        'status' => 'draft',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.keluar.setujui', $surat->id), [
            'catatan_admin' => 'OK',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($surat->fresh()->status)->toBe('draft');
});

it('admin tidak bisa menolak surat keluar tanpa catatan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::factory()->create([
        'jenis_surat' => 'keluar',
        'status' => 'diajukan',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.keluar.tolak', $surat->id), [])
        ->assertSessionHasErrors('catatan_admin');
});
