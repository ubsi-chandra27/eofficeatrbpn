<?php

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;

function dispositionEmployee(string $nip, string $name): Pegawai
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => $nip, 'name' => $name]);

    return Pegawai::create([
        'user_id' => $user->id,
        'nip' => $nip,
        'nama' => $name,
        'email' => $user->email,
    ]);
}

function dispositionRecord(User $admin, Pegawai $pegawai): array
{
    $surat = Surat::factory()->create([
        'jenis_surat' => 'masuk',
        'status' => 'diproses',
        'nomor_surat' => 'SM-REC-'.$pegawai->id,
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Instruksi awal disposisi.',
        'prioritas' => 'Sedang',
        'tanggal_disposisi' => '2026-07-23',
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Belum Dibaca',
    ]);

    return [$surat, $disposisi];
}

it('menampilkan data disposisi secara lengkap pada tabel admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-DSP-001']);
    $pegawai = Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => $pegawaiUser->nip,
        'nama' => 'Budi Penerima Disposisi',
        'email' => $pegawaiUser->email,
    ]);
    $surat = Surat::create([
        'user_id' => $admin->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'DSP-ADMIN-001',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Permohonan pemeriksaan berkas pertanahan',
        'status' => 'diproses',
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Periksa kelengkapan dokumen dan laporkan hasilnya.',
        'prioritas' => 'Tinggi',
        'tanggal_disposisi' => '2026-07-23',
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Sudah Dibaca',
        'dibaca_pada' => '2026-07-23 10:30:00',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.disposisi.index'))
        ->assertOk()
        ->assertSee('23/07/2026')
        ->assertSee('DSP-ADMIN-001')
        ->assertSee('Permohonan pemeriksaan berkas pertanahan')
        ->assertSee('Periksa kelengkapan dokumen')
        ->assertSee('Tinggi')
        ->assertSee('Budi Penerima Disposisi')
        ->assertSee('Sudah Dibaca')
        ->assertSee('Dibaca 23/07/2026 10:30')
        ->assertSee($admin->name);
});

it('mencari disposisi berdasarkan nama penerima', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-DSP-002']);
    $pegawai = Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => $pegawaiUser->nip,
        'nama' => 'Siti Tujuan Khusus',
        'email' => $pegawaiUser->email,
    ]);
    $surat = Surat::factory()->create(['jenis_surat' => 'masuk']);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Tindak lanjuti surat.',
        'prioritas' => 'Sedang',
        'tanggal_disposisi' => now()->toDateString(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Belum Dibaca',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.disposisi.index', ['keyword' => 'Siti Tujuan']))
        ->assertOk()
        ->assertSee('Siti Tujuan Khusus');
});

it('menambah disposisi untuk beberapa pegawai dan menampilkan detailnya', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiSatu = dispositionEmployee('PEG-DSP-101', 'Budi Santoso');
    $pegawaiDua = dispositionEmployee('PEG-DSP-102', 'Ahmad Fauzi');
    $surat = Surat::factory()->create([
        'jenis_surat' => 'masuk',
        'status' => 'diverifikasi',
        'nomor_surat' => 'SM-DISPOSISI-001',
        'perihal' => 'Pemeriksaan berkas permohonan',
    ]);

    $this->actingAs($admin)->post(route('admin.disposisi.store'), [
        'surat_id' => $surat->id,
        'pegawai_id' => [$pegawaiSatu->id, $pegawaiDua->id],
        'catatan' => 'Pelajari isi surat dan siapkan ringkasan tindak lanjut.',
        'prioritas' => 'Tinggi',
        'tanggal_disposisi' => '2026-07-23',
    ])->assertRedirect(route('admin.disposisi.index'));

    $disposisi = Disposisi::where('surat_id', $surat->id)->firstOrFail();
    expect($disposisi->tujuans)->toHaveCount(2)
        ->and($surat->fresh()->status)->toBe('diproses');

    $this->actingAs($admin)->get(route('admin.disposisi.show', $disposisi))
        ->assertOk()
        ->assertSee('SM-DISPOSISI-001')
        ->assertSee('Pelajari isi surat')
        ->assertSee('Budi Santoso')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Belum Dibaca')
        ->assertSee('Edit Disposisi');
});

it('menolak surat keluar dijadikan disposisi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = dispositionEmployee('PEG-DSP-103', 'Pegawai Tujuan');
    $suratKeluar = Surat::factory()->create(['jenis_surat' => 'keluar', 'status' => 'diverifikasi']);

    $this->actingAs($admin)->post(route('admin.disposisi.store'), [
        'surat_id' => $suratKeluar->id,
        'pegawai_id' => [$pegawai->id],
        'catatan' => 'Data ini tidak boleh tersimpan.',
        'prioritas' => 'Sedang',
        'tanggal_disposisi' => '2026-07-23',
    ])->assertNotFound();

    $this->assertDatabaseMissing('disposisi', ['surat_id' => $suratKeluar->id]);
});

it('mengedit disposisi yang belum dibaca tanpa mereset penerima lama', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiSatu = dispositionEmployee('PEG-DSP-104', 'Penerima Pertama');
    $pegawaiDua = dispositionEmployee('PEG-DSP-105', 'Penerima Tambahan');
    [$surat, $disposisi] = dispositionRecord($admin, $pegawaiSatu);

    $this->actingAs($admin)->put(route('admin.disposisi.update', $disposisi), [
        'surat_id' => $surat->id,
        'pegawai_id' => [$pegawaiSatu->id, $pegawaiDua->id],
        'catatan' => 'Instruksi disposisi sudah diperbarui.',
        'prioritas' => 'Rendah',
        'tanggal_disposisi' => '2026-07-24',
    ])->assertRedirect(route('admin.disposisi.index'));

    $disposisi->refresh();
    expect($disposisi->catatan)->toBe('Instruksi disposisi sudah diperbarui.')
        ->and($disposisi->prioritas)->toBe('Rendah')
        ->and($disposisi->tujuans)->toHaveCount(2)
        ->and($disposisi->tujuans->every(fn ($tujuan) => $tujuan->status === 'Belum Dibaca'))->toBeTrue();
});

it('mengunci edit dan hapus setelah disposisi dibaca', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = dispositionEmployee('PEG-DSP-106', 'Penerima Membaca');
    [$surat, $disposisi] = dispositionRecord($admin, $pegawai);
    $disposisi->tujuans()->first()->update(['status' => 'Sudah Dibaca', 'dibaca_pada' => now()]);

    $this->actingAs($admin)->get(route('admin.disposisi.edit', $disposisi))
        ->assertRedirect(route('admin.disposisi.show', $disposisi))
        ->assertSessionHas('error');
    $this->actingAs($admin)->delete(route('admin.disposisi.destroy', $disposisi))
        ->assertSessionHas('error');
    $this->actingAs($admin)->get(route('admin.disposisi.show', $disposisi))
        ->assertOk()
        ->assertSee('Disposisi terkunci')
        ->assertDontSee('Edit Disposisi');
    $this->assertDatabaseHas('disposisi', ['id' => $disposisi->id, 'deleted_at' => null]);
});

it('menghapus disposisi belum dibaca dan menyembunyikannya dari pegawai hingga dipulihkan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = dispositionEmployee('PEG-DSP-107', 'Penerima Arsip');
    [$surat, $disposisi] = dispositionRecord($admin, $pegawai);
    $pegawaiUser = $pegawai->user;

    $this->actingAs($admin)->delete(route('admin.disposisi.destroy', $disposisi))
        ->assertRedirect(route('admin.disposisi.index'))
        ->assertSessionHas('success');
    $this->assertSoftDeleted('disposisi', ['id' => $disposisi->id]);

    $this->actingAs($pegawaiUser)->get(route('pegawai.disposisi.index'))
        ->assertOk()
        ->assertDontSee($surat->nomor_surat);

    $disposisi->restore();
    $this->actingAs($pegawaiUser)->get(route('pegawai.disposisi.index'))
        ->assertOk()
        ->assertSee($surat->nomor_surat);
});
