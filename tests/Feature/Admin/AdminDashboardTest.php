<?php

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Carbon\Carbon;

it('menghitung antrean dashboard hanya dari surat masuk', function () {
    Carbon::setTestNow('2026-07-23 12:00:00');
    $admin = User::factory()->create(['role' => 'admin']);

    foreach (['diajukan', 'diverifikasi', 'dikembalikan', 'diteruskan_ke_pimpinan'] as $status) {
        Surat::factory()->create(['jenis_surat' => 'masuk', 'status' => $status]);
        Surat::factory()->create(['jenis_surat' => 'keluar', 'status' => $status]);
    }

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('antrean', [
            'diajukan' => 1,
            'diverifikasi' => 1,
            'dikembalikan' => 1,
            'ke_pimpinan' => 1,
        ])
        ->assertViewHas('totalSuratMasuk', 4)
        ->assertViewHas('totalSuratKeluar', 4)
        ->assertSee(route('admin.surat.masuk.index', ['status' => 'diajukan']), false)
        ->assertSee(route('admin.surat.masuk.index', ['status' => 'diverifikasi']), false);
});

it('tidak menghitung tujuan dari disposisi yang telah dihapus', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-DASH-001']);
    $pegawai = Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => $pegawaiUser->nip,
        'nama' => $pegawaiUser->name,
        'email' => $pegawaiUser->email,
    ]);
    $suratAktif = Surat::factory()->create(['jenis_surat' => 'masuk']);
    $suratTerhapus = Surat::factory()->create(['jenis_surat' => 'masuk']);
    $aktif = Disposisi::create([
        'surat_id' => $suratAktif->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Disposisi aktif',
        'prioritas' => 'Sedang',
        'tanggal_disposisi' => now(),
    ]);
    $terhapus = Disposisi::create([
        'surat_id' => $suratTerhapus->id,
        'pengirim_id' => $admin->id,
        'catatan' => 'Disposisi terhapus',
        'prioritas' => 'Sedang',
        'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $aktif->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Belum Dibaca',
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $terhapus->id,
        'pegawai_id' => $pegawai->id,
        'status' => 'Selesai',
    ]);
    $terhapus->delete();

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('indikatorDisposisi', [
            'belum_dibaca' => 1,
            'sudah_dibaca' => 0,
            'selesai' => 0,
        ]);
});

it('menampilkan grafik enam bulan dan tautan detail surat terbaru', function () {
    Carbon::setTestNow('2026-07-23 12:00:00');
    $admin = User::factory()->create(['role' => 'admin']);
    $masuk = Surat::factory()->create([
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'SM-DASH-DETAIL',
        'created_at' => '2026-07-10 10:00:00',
    ]);
    $keluar = Surat::factory()->create([
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'SK-DASH-DETAIL',
        'created_at' => '2026-06-10 10:00:00',
    ]);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('chartLabels', fn ($labels) => count($labels) === 6)
        ->assertViewHas('chartMasuk', fn ($values) => count($values) === 6 && end($values) === 1)
        ->assertViewHas('chartKeluar', fn ($values) => count($values) === 6 && $values[4] === 1)
        ->assertSee(route('admin.surat.masuk.show', $masuk), false)
        ->assertSee(route('admin.surat.keluar.show', $keluar), false);
});
