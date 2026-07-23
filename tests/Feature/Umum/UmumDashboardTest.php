<?php

use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\User;
use Database\Seeders\PengajuanUmumDemoSeeder;

function dashboardUmumSurat(User $owner, string $number, array $attributes = []): Surat
{
    return Surat::factory()->create(array_merge([
        'user_id' => $owner->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => $number,
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'perihal' => 'Permohonan dokumen pelayanan',
        'nomor_kontak' => '081234567890',
        'asal_instansi' => 'Komunitas Warga',
        'status' => 'diajukan',
    ], $attributes));
}

it('menampilkan tabel dashboard umum dari pengajuan milik pengguna saja', function () {
    $owner = User::factory()->create(['role' => 'umum']);
    $other = User::factory()->create(['role' => 'umum']);
    $admin = User::factory()->create(['role' => 'admin']);

    $letter = dashboardUmumSurat($owner, 'UMUM-DASH-TABEL-001', [
        'status' => 'dikembalikan',
        'catatan_admin' => 'Lampiran identitas harus diperjelas.',
        'file_path' => 'surat/umum/identitas.pdf',
    ]);
    dashboardUmumSurat($other, 'UMUM-DASH-MILIK-LAIN');
    Surat::factory()->create([
        'user_id' => $owner->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'UMUM-DASH-SURAT-KELUAR',
        'status' => 'selesai',
    ]);

    LogAktivitas::create([
        'user_id' => $admin->id,
        'surat_id' => $letter->id,
        'action' => 'Dikembalikan Admin',
        'description' => 'Pengajuan dikembalikan untuk dilengkapi.',
    ]);

    $this->actingAs($owner)->get(route('umum.dashboard'))
        ->assertOk()
        ->assertViewHas('statistik', fn (array $stats) => $stats === [
            'total' => 1,
            'diajukan' => 0,
            'diproses' => 0,
            'perbaikan' => 1,
            'selesai' => 0,
        ])
        ->assertSee('UMUM-DASH-TABEL-001')
        ->assertSee('Permohonan Dokumen')
        ->assertSee('Permohonan dokumen pelayanan')
        ->assertSee('081234567890')
        ->assertSee('Komunitas Warga')
        ->assertSee('PDF')
        ->assertSee('Harus diperbaiki')
        ->assertSee('Lampiran identitas harus diperjelas.')
        ->assertSee('Dikembalikan Admin')
        ->assertDontSee('UMUM-DASH-MILIK-LAIN')
        ->assertDontSee('UMUM-DASH-SURAT-KELUAR');
});

it('tidak membocorkan aktivitas surat akun lain melalui log milik pengguna', function () {
    $owner = User::factory()->create(['role' => 'umum']);
    $other = User::factory()->create(['role' => 'umum']);
    $otherLetter = dashboardUmumSurat($other, 'UMUM-LOG-PRIVAT');

    LogAktivitas::create([
        'user_id' => $owner->id,
        'surat_id' => $otherLetter->id,
        'action' => 'Log Tidak Sah',
        'description' => 'Log ini tidak boleh terlihat pada dashboard pemilik akun lain.',
    ]);
    LogAktivitas::create([
        'user_id' => $owner->id,
        'surat_id' => null,
        'action' => 'Profil Diperbarui',
        'description' => 'Data profil akun berhasil diperbarui.',
    ]);

    $this->actingAs($owner)->get(route('umum.dashboard'))
        ->assertOk()
        ->assertSee('Pengajuan Terbaru')
        ->assertSee('Belum ada pengajuan')
        ->assertSee('Buat Pengajuan')
        ->assertSee(route('umum.surat.create'), false)
        ->assertSee('Profil Diperbarui')
        ->assertDontSee('UMUM-LOG-PRIVAT')
        ->assertDontSee('Log Tidak Sah');
});

it('mengisi tabel dashboard setiap akun umum melalui seeder demo secara idempoten', function () {
    $first = User::factory()->create(['role' => 'umum']);
    $second = User::factory()->create(['role' => 'umum']);

    $this->seed(PengajuanUmumDemoSeeder::class);
    $this->seed(PengajuanUmumDemoSeeder::class);

    expect(Surat::where('user_id', $first->id)->where('jenis_surat', 'masuk')->count())->toBe(4)
        ->and(Surat::where('user_id', $second->id)->where('jenis_surat', 'masuk')->count())->toBe(4)
        ->and(LogAktivitas::where('user_id', $first->id)->whereNotNull('surat_id')->count())->toBe(4);

    $this->actingAs($first)->get(route('umum.dashboard'))
        ->assertOk()
        ->assertSee('UMUM/DEMO/'.str_pad((string) $first->id, 5, '0', STR_PAD_LEFT).'/SRT')
        ->assertSee('Penyampaian surat permohonan koordinasi')
        ->assertSee('Proses telah selesai')
        ->assertDontSee('UMUM/DEMO/'.str_pad((string) $second->id, 5, '0', STR_PAD_LEFT).'/SRT');
});
