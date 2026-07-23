<?php

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function outgoingEmployee(string $nip = 'PEG-SK-CRUD-001'): User
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => $nip]);
    Pegawai::create(['user_id' => $user->id, 'nip' => $nip, 'nama' => $user->name, 'email' => $user->email]);
    return $user;
}

function outgoingLeader(): Pegawai
{
    $jabatan = Jabatan::create(['kode' => 'PIMP-SK', 'nama' => 'Kepala Seksi']);
    return Pegawai::create([
        'nip' => 'PIMP-SK-001', 'nama' => 'Pimpinan Penandatangan',
        'email' => 'pimpinan.sk@example.test', 'jabatan_id' => $jabatan->id,
    ]);
}

function outgoingLetter(User $user, Pegawai $leader, array $overrides = []): Surat
{
    return Surat::create(array_merge([
        'user_id' => $user->id, 'jenis_surat' => 'keluar', 'nomor_surat' => 'SK-CRUD-001',
        'tanggal_surat' => '2026-07-23', 'kode_surat' => 'KODE-SK',
        'perihal' => 'Audit surat keluar pegawai', 'tujuan_surat' => 'Kantor Wilayah',
        'jabatan_pimpinan_id' => $leader->jabatan_id, 'nama_pimpinan' => $leader->nama,
        'status' => 'draft',
    ], $overrides));
}

it('menampilkan isi tabel surat keluar lengkap dan hanya milik pegawai', function () {
    $owner = outgoingEmployee('PEG-SK-TABLE-01');
    $other = outgoingEmployee('PEG-SK-TABLE-02');
    $leader = outgoingLeader();
    outgoingLetter($owner, $leader, ['file_path' => 'surat-keluar/lampiran.pdf']);
    outgoingLetter($other, $leader, ['nomor_surat' => 'SK-RAHASIA-002']);

    $this->actingAs($owner)->get(route('pegawai.surat-keluar.index'))
        ->assertOk()->assertSee('SK-CRUD-001')->assertSee('Audit surat keluar pegawai')
        ->assertSee('Kantor Wilayah')->assertSee('Pimpinan Penandatangan')
        ->assertSee('Kepala Seksi')->assertSee('PDF')->assertSee('Draft')
        ->assertDontSee('SK-RAHASIA-002');
});

it('menambah draft dan langsung mengajukan surat keluar', function () {
    $user = outgoingEmployee();
    $leader = outgoingLeader();
    $payload = [
        'nomor_surat' => 'SK-STORE-001', 'tanggal_surat' => '2026-07-23',
        'kode_surat' => 'STORE', 'perihal' => 'Surat baru pegawai',
        'tujuan_surat' => 'Instansi Tujuan', 'pimpinan_pegawai_id' => $leader->id,
        'deskripsi' => 'Isi ringkas surat.', 'status' => 'draft',
    ];
    $this->actingAs($user)->post(route('pegawai.surat-keluar.store'), $payload)
        ->assertRedirect(route('pegawai.surat-keluar.index'))->assertSessionHas('success');
    $this->assertDatabaseHas('surats', [
        'nomor_surat' => 'SK-STORE-001', 'status' => 'draft',
        'nama_pimpinan' => 'Pimpinan Penandatangan', 'kode_surat' => 'STORE',
    ]);

    $payload['nomor_surat'] = 'SK-STORE-002';
    $payload['status'] = 'diajukan';
    $this->actingAs($user)->post(route('pegawai.surat-keluar.store'), $payload)
        ->assertRedirect(route('pegawai.surat-keluar.index'));
    $this->assertDatabaseHas('surats', ['nomor_surat' => 'SK-STORE-002', 'status' => 'diajukan']);
});

it('menampilkan halaman tambah edit dan detail dengan riwayat yang benar', function () {
    $user = outgoingEmployee();
    $leader = outgoingLeader();
    $surat = outgoingLetter($user, $leader);
    \App\Models\LogAktivitas::create([
        'user_id' => $user->id, 'surat_id' => $surat->id,
        'action' => 'Riwayat Pengujian', 'description' => 'Deskripsi aktivitas surat keluar.',
    ]);

    $this->actingAs($user)->get(route('pegawai.surat-keluar.create'))->assertOk()->assertSee('Tambah Surat Keluar');
    $this->actingAs($user)->get(route('pegawai.surat-keluar.edit', $surat))
        ->assertOk()->assertSee('Edit Surat Keluar')->assertSee('pimpinan_pegawai_id', false);
    $this->actingAs($user)->get(route('pegawai.surat-keluar.show', $surat))
        ->assertOk()->assertSee('Detail Surat Keluar')->assertSee('Riwayat Pengujian')
        ->assertSee('Deskripsi aktivitas surat keluar.')->assertSee('Hapus');
});

it('memperbarui surat dan mengganti lampiran secara atomik', function () {
    Storage::fake('local');
    $user = outgoingEmployee();
    $leader = outgoingLeader();
    Storage::disk('local')->put('surat-keluar/lama.pdf', 'lama');
    $surat = outgoingLetter($user, $leader, ['file_path' => 'surat-keluar/lama.pdf']);

    $this->actingAs($user)->put(route('pegawai.surat-keluar.update', $surat), [
        'nomor_surat' => 'SK-UPDATED-001', 'tanggal_surat' => '2026-07-24',
        'kode_surat' => 'UPDATED', 'perihal' => 'Perihal diperbarui',
        'tujuan_surat' => 'Tujuan Baru', 'pimpinan_pegawai_id' => $leader->id,
        'deskripsi' => 'Deskripsi baru.',
        'file_path' => UploadedFile::fake()->create('baru.pdf', 20, 'application/pdf'),
    ])->assertRedirect(route('pegawai.surat-keluar.index'))->assertSessionHas('success');

    $updated = $surat->fresh();
    expect($updated)->nomor_surat->toBe('SK-UPDATED-001')->kode_surat->toBe('UPDATED');
    Storage::disk('local')->assertMissing('surat-keluar/lama.pdf');
    Storage::disk('local')->assertExists($updated->file_path);
});

it('menghapus draft dengan soft delete tanpa menghilangkan lampiran', function () {
    Storage::fake('local');
    $user = outgoingEmployee();
    $leader = outgoingLeader();
    Storage::disk('local')->put('surat-keluar/arsip.pdf', 'arsip');
    $surat = outgoingLetter($user, $leader, ['file_path' => 'surat-keluar/arsip.pdf']);

    $this->actingAs($user)->delete(route('pegawai.surat-keluar.destroy', $surat))
        ->assertRedirect(route('pegawai.surat-keluar.index'))->assertSessionHas('success');
    expect(Surat::find($surat->id))->toBeNull()
        ->and(Surat::withTrashed()->find($surat->id))->not->toBeNull();
    Storage::disk('local')->assertExists('surat-keluar/arsip.pdf');
});

it('menolak perubahan surat yang diproses dan akses surat milik pegawai lain', function () {
    $owner = outgoingEmployee('PEG-SK-OWNER');
    $other = outgoingEmployee('PEG-SK-OTHER');
    $leader = outgoingLeader();
    $processed = outgoingLetter($owner, $leader, ['status' => 'diajukan']);

    $this->actingAs($owner)->get(route('pegawai.surat-keluar.edit', $processed))
        ->assertSessionHas('error');
    $this->actingAs($owner)->delete(route('pegawai.surat-keluar.destroy', $processed))
        ->assertSessionHas('error');
    $this->actingAs($other)->get(route('pegawai.surat-keluar.show', $processed))->assertNotFound();
    $this->actingAs($other)->delete(route('pegawai.surat-keluar.destroy', $processed))->assertNotFound();
    expect(Surat::find($processed->id))->not->toBeNull();
});
