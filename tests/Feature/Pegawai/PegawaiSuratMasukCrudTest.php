<?php

use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function incomingEmployee(string $nip = 'PEG-SM-CRUD-001'): User
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => $nip]);
    Pegawai::create(['user_id' => $user->id, 'nip' => $nip, 'nama' => $user->name, 'email' => $user->email]);
    return $user;
}

function incomingLetter(User $user, array $overrides = []): Surat
{
    return Surat::create(array_merge([
        'user_id' => $user->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-CRUD-001',
        'tanggal_surat' => '2026-07-23', 'perihal' => 'Audit surat masuk pegawai',
        'asal_surat' => 'Kantor Wilayah', 'tujuan_surat' => 'Kantor Pertanahan',
        'status' => 'draft',
    ], $overrides));
}

it('menampilkan isi tabel surat masuk lengkap dan hanya milik pegawai login', function () {
    $owner = incomingEmployee('PEG-SM-TABLE-001');
    $other = incomingEmployee('PEG-SM-TABLE-002');
    incomingLetter($owner, ['file_path' => 'surat-masuk/lampiran.pdf']);
    incomingLetter($other, ['nomor_surat' => 'SM-RAHASIA-002']);

    $this->actingAs($owner)->get(route('pegawai.surat-masuk.index'))
        ->assertOk()->assertSee('SM-CRUD-001')->assertSee('Audit surat masuk pegawai')
        ->assertSee('Kantor Wilayah')->assertSee('Kantor Pertanahan')
        ->assertSee('PDF')->assertSee('Belum dikirim ke Admin')
        ->assertDontSee('SM-RAHASIA-002');
});

it('menampilkan halaman tambah edit dan detail surat masuk', function () {
    $user = incomingEmployee();
    $surat = incomingLetter($user);

    $this->actingAs($user)->get(route('pegawai.surat-masuk.create'))
        ->assertOk()->assertSee('Catat Surat Masuk');
    $this->actingAs($user)->get(route('pegawai.surat-masuk.edit', $surat))
        ->assertOk()->assertSee('Edit Surat Masuk')->assertSee('SM-CRUD-001');
    $this->actingAs($user)->get(route('pegawai.surat-masuk.show', $surat))
        ->assertOk()->assertSee('Detail Surat Masuk')->assertSee('Status Verifikasi Admin')
        ->assertSee('Hapus');
});

it('memperbarui data dan mengganti lampiran secara atomik', function () {
    Storage::fake('local');
    $user = incomingEmployee();
    Storage::disk('local')->put('surat-masuk/lama.pdf', 'lampiran lama');
    $surat = incomingLetter($user, ['file_path' => 'surat-masuk/lama.pdf']);

    $this->actingAs($user)->put(route('pegawai.surat-masuk.update', $surat), [
        'nomor_surat' => 'SM-CRUD-UPDATED',
        'tanggal_surat' => '2026-07-24',
        'perihal' => 'Perihal sudah diperbarui',
        'asal_surat' => 'Unit Kerja Baru',
        'tujuan_surat' => 'Administrator',
        'deskripsi' => 'Deskripsi hasil perbaikan.',
        'file_path' => UploadedFile::fake()->create('baru.pdf', 20, 'application/pdf'),
    ])->assertRedirect(route('pegawai.surat-masuk.index'))->assertSessionHas('success');

    $updated = $surat->fresh();
    expect($updated)->nomor_surat->toBe('SM-CRUD-UPDATED')
        ->perihal->toBe('Perihal sudah diperbarui');
    Storage::disk('local')->assertMissing('surat-masuk/lama.pdf');
    Storage::disk('local')->assertExists($updated->file_path);
    $this->assertDatabaseHas('log_aktivitas', ['surat_id' => $surat->id, 'action' => 'Perbarui Surat Masuk']);
});

it('mempertahankan lampiran ketika surat dipindahkan ke sampah agar dapat dipulihkan', function () {
    Storage::fake('local');
    $user = incomingEmployee();
    Storage::disk('local')->put('surat-masuk/arsip.pdf', 'arsip');
    $surat = incomingLetter($user, ['file_path' => 'surat-masuk/arsip.pdf']);

    $this->actingAs($user)->delete(route('pegawai.surat-masuk.destroy', $surat))
        ->assertRedirect(route('pegawai.surat-masuk.index'))->assertSessionHas('success');

    expect(Surat::find($surat->id))->toBeNull()
        ->and(Surat::withTrashed()->find($surat->id))->not->toBeNull();
    Storage::disk('local')->assertExists('surat-masuk/arsip.pdf');
});

it('menolak edit dan hapus surat yang sedang atau sudah diverifikasi', function () {
    $user = incomingEmployee();
    $surat = incomingLetter($user, ['status' => 'diajukan']);

    $this->actingAs($user)->get(route('pegawai.surat-masuk.edit', $surat))
        ->assertRedirect(route('pegawai.surat-masuk.index'))->assertSessionHas('error');
    $this->actingAs($user)->delete(route('pegawai.surat-masuk.destroy', $surat))
        ->assertSessionHas('error');
    expect(Surat::find($surat->id))->not->toBeNull();
});

it('menolak akses edit update dan hapus surat milik pegawai lain', function () {
    $owner = incomingEmployee('PEG-SM-OWNER');
    $other = incomingEmployee('PEG-SM-OTHER');
    $surat = incomingLetter($owner);
    $payload = [
        'nomor_surat' => 'SM-DIAMBIL-ALIH', 'tanggal_surat' => '2026-07-23',
        'perihal' => 'Tidak boleh', 'asal_surat' => 'A', 'tujuan_surat' => 'B',
    ];

    $this->actingAs($other)->get(route('pegawai.surat-masuk.edit', $surat))->assertNotFound();
    $this->actingAs($other)->put(route('pegawai.surat-masuk.update', $surat), $payload)->assertNotFound();
    $this->actingAs($other)->delete(route('pegawai.surat-masuk.destroy', $surat))->assertNotFound();
    expect($surat->fresh()->nomor_surat)->toBe('SM-CRUD-001');
});
