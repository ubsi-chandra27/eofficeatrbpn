<?php

use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;

function pegawaiSuratMasukUser(): User
{
    $user = User::factory()->create([
        'role' => 'pegawai',
        'nip' => '199001012026001',
    ]);

    Pegawai::create([
        'user_id' => $user->id,
        'nip' => $user->nip,
        'nama' => $user->name,
        'email' => $user->email,
    ]);

    return $user;
}

function suratMasukPayload(string $action = 'submit'): array
{
    return [
        'nomor_surat' => 'SM-VERIFIKASI-001',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Permohonan verifikasi surat masuk',
        'asal_surat' => 'Kantor Pertanahan',
        'tujuan_surat' => 'Administrator E-Office',
        'deskripsi' => 'Surat ini digunakan untuk menguji alur persetujuan admin.',
        'submit_action' => $action,
    ];
}

it('menampilkan formulir catat surat masuk dengan pilihan draft dan kirim ke admin', function () {
    $pegawai = pegawaiSuratMasukUser();

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.create'))
        ->assertOk()
        ->assertSee('Tambah Surat Masuk')
        ->assertSee('Simpan Draft')
        ->assertSee('Simpan & Kirim ke Admin')
        ->assertSee('asal_surat', false)
        ->assertDontSee('Jabatan Pimpinan')
        ->assertDontSee('Nama Pimpinan');
});

it('menyimpan draft tanpa memasukkannya ke antrean verifikasi admin', function () {
    $pegawai = pegawaiSuratMasukUser();
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($pegawai)
        ->post(route('pegawai.surat-masuk.store'), suratMasukPayload('draft'))
        ->assertRedirect(route('pegawai.surat-masuk.index'));

    $surat = Surat::where('nomor_surat', 'SM-VERIFIKASI-001')->firstOrFail();
    expect($surat->status)->toBe('draft');

    $this->actingAs($admin)
        ->get(route('admin.surat.masuk.index'))
        ->assertOk()
        ->assertDontSee('SM-VERIFIKASI-001');
});

it('mengirim surat pegawai ke admin dan menampilkan status menunggu verifikasi', function () {
    $pegawai = pegawaiSuratMasukUser();
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($pegawai)
        ->post(route('pegawai.surat-masuk.store'), suratMasukPayload())
        ->assertRedirect(route('pegawai.surat-masuk.index'));

    $surat = Surat::where('nomor_surat', 'SM-VERIFIKASI-001')->firstOrFail();
    expect($surat->status)->toBe('diajukan');

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.show', $surat))
        ->assertOk()
        ->assertSee('Menunggu verifikasi Admin');

    $this->actingAs($admin)
        ->get(route('admin.surat.masuk.index'))
        ->assertOk()
        ->assertSee('SM-VERIFIKASI-001');
});

it('menampilkan hasil persetujuan admin kepada pegawai', function () {
    $pegawai = pegawaiSuratMasukUser();
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::create([
        ...suratMasukPayload(),
        'user_id' => $pegawai->id,
        'jenis_surat' => 'masuk',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.masuk.setujui', $surat), [
            'catatan_admin' => 'Berkas lengkap dan dapat diproses.',
        ])
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('diverifikasi');

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.show', $surat))
        ->assertOk()
        ->assertSee('Surat sudah disetujui Admin')
        ->assertSee('Berkas lengkap dan dapat diproses.');

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.index'))
        ->assertOk()
        ->assertSee('Disetujui Admin');
});

it('menampilkan penolakan dan catatan perbaikan admin kepada pegawai', function () {
    $pegawai = pegawaiSuratMasukUser();
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::create([
        ...suratMasukPayload(),
        'user_id' => $pegawai->id,
        'jenis_surat' => 'masuk',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.surat.masuk.tolak', $surat), [
            'catatan_admin' => 'Mohon perbaiki tujuan surat sebelum dikirim ulang.',
        ])
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('dikembalikan');

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.show', $surat))
        ->assertOk()
        ->assertSee('Surat belum disetujui')
        ->assertSee('Mohon perbaiki tujuan surat sebelum dikirim ulang.');

    $this->actingAs($pegawai)
        ->get(route('pegawai.surat-masuk.index'))
        ->assertOk()
        ->assertSee('Perlu Perbaikan')
        ->assertSee('Mohon perbaiki tujuan surat sebelum dikirim ulang.');

    $this->actingAs($pegawai)
        ->put(route('pegawai.surat-masuk.kirim', $surat))
        ->assertSessionHas('success');

    expect($surat->fresh())
        ->status->toBe('diajukan')
        ->catatan_admin->toBeNull();
});
