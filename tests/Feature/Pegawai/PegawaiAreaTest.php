<?php

use App\Models\Pegawai;
use App\Models\User;

function pegawaiAreaUser(): User
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => '198812120001']);
    Pegawai::create(['user_id' => $user->id, 'nip' => $user->nip, 'nama' => $user->name, 'email' => $user->email]);
    return $user;
}

it('merender seluruh halaman utama pegawai', function () {
    $user = pegawaiAreaUser();
    $this->actingAs($user)->get(route('pegawai.dashboard'))
        ->assertOk()
        ->assertSee('Nomor Surat')
        ->assertSee('Instruksi')
        ->assertSee('Surat Masuk Terbaru')
        ->assertSee('Aktivitas')
        ->assertSee('Keterangan');

    foreach ([
        route('pegawai.surat-masuk.index'),
        route('pegawai.surat-masuk.create'), route('pegawai.surat-keluar.index'),
        route('pegawai.surat-keluar.create'), route('pegawai.disposisi.index'),
        route('profile.edit'),
    ] as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
});

it('menolak pegawai mengakses data pegawai lain', function () {
    $owner = pegawaiAreaUser();
    $other = User::factory()->create(['role' => 'pegawai', 'nip' => '198812120002']);
    Pegawai::create(['user_id' => $other->id, 'nip' => $other->nip, 'nama' => $other->name, 'email' => $other->email]);
    $surat = \App\Models\Surat::create(['user_id' => $owner->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'PRIV-PEG-001', 'tanggal_surat' => now(), 'status' => 'draft']);

    $this->actingAs($other)->get(route('pegawai.surat-masuk.show', $surat))->assertNotFound();
});

it('menampilkan isi tabel surat masuk surat keluar dan disposisi milik pegawai', function () {
    $user = pegawaiAreaUser();
    $pegawai = $user->pegawai;
    $admin = User::factory()->create(['role' => 'admin']);

    $masuk = \App\Models\Surat::create([
        'user_id' => $user->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-PEG-001',
        'tanggal_surat' => now(), 'perihal' => 'Undangan koordinasi', 'asal_surat' => 'Unit Pelayanan',
        'tujuan_surat' => 'Pegawai', 'metode' => 'Sistem', 'status' => 'draft',
    ]);
    \App\Models\Surat::create([
        'user_id' => $user->id, 'jenis_surat' => 'keluar', 'nomor_surat' => 'SK-PEG-001',
        'tanggal_surat' => now(), 'perihal' => 'Balasan permohonan', 'asal_surat' => $user->name,
        'tujuan_surat' => 'Pemohon', 'metode' => 'Sistem', 'status' => 'diajukan',
    ]);
    $disposisi = \App\Models\Disposisi::create([
        'surat_id' => $masuk->id, 'pengirim_id' => $admin->id,
        'catatan' => 'Pelajari dan siapkan bahan tindak lanjut.', 'prioritas' => 'Tinggi',
        'tanggal_disposisi' => now(),
    ]);
    \App\Models\DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id, 'pegawai_id' => $pegawai->id, 'status' => 'Belum Dibaca',
    ]);

    $this->actingAs($user)->get(route('pegawai.surat-masuk.index'))
        ->assertOk()->assertSee('SM-PEG-001')->assertSee('Undangan koordinasi')->assertSee('Unit Pelayanan');
    $this->actingAs($user)->get(route('pegawai.surat-keluar.index'))
        ->assertOk()->assertSee('SK-PEG-001')->assertSee('Balasan permohonan')->assertSee('Pemohon');
    $this->actingAs($user)->get(route('pegawai.disposisi.index'))
        ->assertOk()->assertSee('SM-PEG-001')->assertSee('Pelajari dan siapkan bahan')->assertSee('Tinggi');
});
