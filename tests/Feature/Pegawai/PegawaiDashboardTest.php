<?php

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;

function dashboardPegawaiUser(string $nip): User
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => $nip]);
    Pegawai::create([
        'user_id' => $user->id,
        'nip' => $nip,
        'nama' => $user->name,
        'email' => $user->email,
    ]);

    return $user->fresh('pegawai');
}

it('menampilkan isi dashboard pegawai dari data miliknya saja', function () {
    $user = dashboardPegawaiUser('198812120101');
    $other = dashboardPegawaiUser('198812120102');
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pengirim']);

    $suratMasuk = Surat::factory()->create([
        'user_id' => $user->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'DASH-SM-001',
        'perihal' => 'Undangan rapat dashboard',
        'asal_surat' => 'Kantor Wilayah',
        'status' => 'dikembalikan',
        'catatan_admin' => 'Nomor agenda harus diperbaiki.',
        'file_path' => 'surat-masuk/undangan.pdf',
    ]);
    Surat::factory()->create([
        'user_id' => $other->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'DASH-MILIK-LAIN',
    ]);
    Surat::factory()->create([
        'user_id' => $user->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'DASH-SK-001',
        'status' => 'diajukan',
    ]);
    Surat::factory()->create([
        'user_id' => $user->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'DASH-SK-DRAFT',
        'status' => 'draft',
    ]);

    $disposisi = Disposisi::create([
        'surat_id' => $suratMasuk->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Pelajari dan buat ringkasan tindak lanjut.',
        'prioritas' => 'Tinggi',
        'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $user->pegawai->id,
        'status' => 'Belum Dibaca',
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $other->pegawai->id,
        'status' => 'Selesai',
    ]);
    LogAktivitas::create([
        'user_id' => $user->id,
        'surat_id' => $suratMasuk->id,
        'action' => 'Perbaikan Diperlukan',
        'description' => 'Admin mengembalikan surat untuk diperbaiki.',
    ]);

    $this->actingAs($user)->get(route('pegawai.dashboard'))
        ->assertOk()
        ->assertViewHas('menunggu', 1)
        ->assertViewHas('prioritasTinggi', 1)
        ->assertViewHas('disposisiBelum', 1)
        ->assertSee('DASH-SM-001')
        ->assertSee('Undangan rapat dashboard')
        ->assertSee('Kantor Wilayah')
        ->assertSee('Admin Pengirim')
        ->assertSee('Pelajari dan buat ringkasan')
        ->assertSee('PDF')
        ->assertSee('Perlu diperbaiki')
        ->assertSee('Nomor agenda harus diperbaiki.')
        ->assertSee('Perbaikan Diperlukan')
        ->assertDontSee('DASH-MILIK-LAIN');
});

it('tidak menghitung disposisi prioritas tinggi yang telah selesai sebagai pekerjaan aktif', function () {
    $user = dashboardPegawaiUser('198812120103');
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = Surat::factory()->masuk()->create(['user_id' => $user->id]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Tugas telah selesai.',
        'prioritas' => 'Tinggi',
        'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id,
        'pegawai_id' => $user->pegawai->id,
        'status' => 'Selesai',
    ]);

    $this->actingAs($user)->get(route('pegawai.dashboard'))
        ->assertOk()
        ->assertViewHas('prioritasTinggi', 0)
        ->assertViewHas('disposisiAktif', 0);
});
