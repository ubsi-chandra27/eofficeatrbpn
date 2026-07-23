<?php

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function suratDenganLampiran(User $owner, string $jenis = 'masuk'): Surat
{
    Storage::disk('local')->put('surat/rahasia.pdf', 'dokumen-rahasia');

    return Surat::create([
        'user_id' => $owner->id,
        'jenis_surat' => $jenis,
        'nomor_surat' => 'SEC-'.strtoupper($jenis).'-'.fake()->unique()->numerify('###'),
        'tanggal_surat' => now(),
        'status' => 'diajukan',
        'file_path' => 'surat/rahasia.pdf',
    ]);
}

it('hanya mengizinkan pemilik dan admin mengunduh lampiran private', function () {
    Storage::fake('local');
    $owner = User::factory()->create(['role' => 'umum']);
    $other = User::factory()->create(['role' => 'umum']);
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = suratDenganLampiran($owner);

    $this->get(route('surat.lampiran', $surat))->assertRedirect(route('login'));
    $this->actingAs($other)->get(route('surat.lampiran', $surat))->assertNotFound();
    $this->actingAs($owner)->get(route('surat.lampiran', $surat))->assertOk();
    $this->actingAs($admin)->get(route('surat.lampiran', $surat))->assertOk();
});

it('mengizinkan pegawai penerima disposisi mengunduh lampiran', function () {
    Storage::fake('local');
    $owner = User::factory()->create(['role' => 'umum']);
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai']);
    $pegawai = Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nama' => $pegawaiUser->name,
        'email' => $pegawaiUser->email,
        'nip' => 'SEC-PEG-001',
    ]);
    $surat = suratDenganLampiran($owner);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Tindak lanjuti',
        'prioritas' => 'Tinggi',
        'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Belum Dibaca',
    ]);

    $this->actingAs($pegawaiUser)->get(route('surat.lampiran', $surat))->assertOk();
});

it('memisahkan akses admin antara surat masuk dan surat keluar', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create(['role' => 'pegawai']);
    $masuk = Surat::create([
        'user_id' => $owner->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SEC-MASUK-001',
        'tanggal_surat' => now(), 'status' => 'diajukan',
    ]);
    $keluar = Surat::create([
        'user_id' => $owner->id, 'jenis_surat' => 'keluar', 'nomor_surat' => 'SEC-KELUAR-001',
        'tanggal_surat' => now(), 'status' => 'draft',
    ]);

    $this->actingAs($admin)->get(route('admin.surat.keluar.show', $masuk))->assertNotFound();
    $this->actingAs($admin)->get(route('admin.surat.masuk.show', $keluar))->assertNotFound();
});
