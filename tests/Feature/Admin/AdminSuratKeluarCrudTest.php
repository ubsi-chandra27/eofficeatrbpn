<?php

use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function adminOutgoingLetter(User $owner, array $attributes = []): Surat
{
    return Surat::create(array_merge([
        'user_id' => $owner->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'SK-ADMIN-001',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Pemberitahuan administrasi pertanahan',
        'tujuan_surat' => 'Kantor Wilayah',
        'penandatangan' => 'Kepala Kantor',
        'metode' => 'Email',
        'status' => 'draft',
    ], $attributes));
}

function outgoingPayload(array $attributes = []): array
{
    return array_merge([
        'nomor_surat' => 'SK-CRUD-001',
        'tanggal_surat' => '2026-07-23',
        'tanggal_keluar' => '2026-07-24',
        'tanggal_kirim' => '2026-07-25',
        'tujuan_surat' => 'Kantor Wilayah ATR/BPN',
        'penandatangan' => 'Kepala Bagian Tata Usaha',
        'perihal' => 'Pemberitahuan hasil pemeriksaan',
        'nomor_agenda' => 'AGD-SK-001',
        'metode' => 'Email',
        'deskripsi' => 'Surat telah diperiksa dan siap dikirim.',
        'status' => 'draft',
        'is_priority' => '1',
    ], $attributes);
}

it('menampilkan dan memfilter daftar surat keluar admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    adminOutgoingLetter($admin, ['nomor_surat' => 'SK-DAFTAR-001']);
    adminOutgoingLetter($admin, [
        'nomor_surat' => 'SK-DAFTAR-002',
        'perihal' => 'Surat khusus tujuan eksternal',
        'status' => 'terkirim',
    ]);
    Surat::factory()->create(['jenis_surat' => 'masuk', 'nomor_surat' => 'SM-TIDAK-TAMPIL']);

    $this->actingAs($admin)
        ->get(route('admin.surat.keluar.index', ['keyword' => 'khusus', 'status' => 'terkirim']))
        ->assertOk()
        ->assertSee('SK-DAFTAR-002')
        ->assertSee('Terkirim')
        ->assertDontSee('SK-DAFTAR-001')
        ->assertDontSee('SM-TIDAK-TAMPIL');
});

it('menambah surat keluar beserta lampiran privat dan menampilkan detail lengkap', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    $payload = outgoingPayload([
        'file_path' => UploadedFile::fake()->create('surat-keluar.pdf', 128, 'application/pdf'),
    ]);

    $this->actingAs($admin)->post(route('admin.surat.keluar.store'), $payload)
        ->assertRedirect(route('admin.surat.keluar.index'));

    $surat = Surat::where('nomor_surat', 'SK-CRUD-001')->firstOrFail();
    expect($surat->jenis_surat)->toBe('keluar')
        ->and($surat->is_priority)->toBeTrue()
        ->and($surat->status)->toBe('draft');
    Storage::disk('local')->assertExists($surat->file_path);

    $this->actingAs($admin)->get(route('admin.surat.keluar.show', $surat))
        ->assertOk()
        ->assertSee('Kepala Bagian Tata Usaha')
        ->assertSee('Status Surat')
        ->assertSee('Draft')
        ->assertSee('Tanggal Keluar')
        ->assertSee('Tanggal Kirim')
        ->assertSee('Lihat Lampiran');
});

it('mengedit seluruh data dan mengganti lampiran surat keluar secara aman', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('local')->put('surat-keluar/lama.pdf', 'berkas-lama');
    $surat = adminOutgoingLetter($admin, [
        'nomor_surat' => 'SK-EDIT-001',
        'file_path' => 'surat-keluar/lama.pdf',
    ]);

    $this->actingAs($admin)->put(route('admin.surat.keluar.update', $surat), outgoingPayload([
        'nomor_surat' => 'SK-EDIT-002',
        'tujuan_surat' => 'Direktorat Jenderal',
        'penandatangan' => 'Sekretaris Kantor',
        'status' => 'terkirim',
        'is_priority' => null,
        'file_path' => UploadedFile::fake()->create('pengganti.pdf', 128, 'application/pdf'),
    ]))->assertRedirect(route('admin.surat.keluar.index'));

    $surat->refresh();
    expect($surat->nomor_surat)->toBe('SK-EDIT-002')
        ->and($surat->tujuan_surat)->toBe('Direktorat Jenderal')
        ->and($surat->penandatangan)->toBe('Sekretaris Kantor')
        ->and($surat->status)->toBe('terkirim')
        ->and($surat->is_priority)->toBeFalse();
    Storage::disk('local')->assertMissing('surat-keluar/lama.pdf');
    Storage::disk('local')->assertExists($surat->file_path);
});

it('menghapus draft dengan soft delete dan mempertahankan lampiran untuk pemulihan', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('local')->put('surat-keluar/arsip.pdf', 'berkas-arsip');
    $surat = adminOutgoingLetter($admin, [
        'nomor_surat' => 'SK-HAPUS-001',
        'file_path' => 'surat-keluar/arsip.pdf',
    ]);

    $this->actingAs($admin)->delete(route('admin.surat.keluar.destroy', $surat))
        ->assertRedirect(route('admin.surat.keluar.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('surats', ['id' => $surat->id]);
    Storage::disk('local')->assertExists('surat-keluar/arsip.pdf');
});

it('menolak penghapusan surat keluar yang bukan draft', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = adminOutgoingLetter($admin, [
        'nomor_surat' => 'SK-HISTORI-001',
        'status' => 'terkirim',
    ]);

    $this->actingAs($admin)->delete(route('admin.surat.keluar.destroy', $surat))
        ->assertSessionHas('error');
    $this->assertDatabaseHas('surats', ['id' => $surat->id, 'deleted_at' => null]);
});

it('memverifikasi surat keluar yang diajukan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = User::factory()->create(['role' => 'pegawai']);
    $surat = adminOutgoingLetter($pegawai, [
        'nomor_surat' => 'SK-VERIF-001',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.surat.keluar.show', $surat))
        ->post(route('admin.surat.keluar.setujui', $surat), [
            'catatan_admin' => 'Sudah sesuai.',
        ])
        ->assertRedirect(route('admin.surat.keluar.show', $surat))
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('diverifikasi')
        ->and($surat->fresh()->catatan_admin)->toBe('Sudah sesuai.');
});

it('menolak surat keluar yang diajukan dengan catatan admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = User::factory()->create(['role' => 'pegawai']);
    $surat = adminOutgoingLetter($pegawai, [
        'nomor_surat' => 'SK-TOLAK-001',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.surat.keluar.show', $surat))
        ->post(route('admin.surat.keluar.tolak', $surat), [
            'catatan_admin' => 'Tujuan surat perlu diperjelas.',
        ])
        ->assertRedirect(route('admin.surat.keluar.show', $surat))
        ->assertSessionHas('success');

    expect($surat->fresh()->status)->toBe('ditolak')
        ->and($surat->fresh()->catatan_admin)->toBe('Tujuan surat perlu diperjelas.');
});

it('menolak tanggal kirim yang lebih awal dari tanggal surat', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.surat.keluar.store'), outgoingPayload([
        'tanggal_kirim' => '2026-07-22',
    ]))->assertSessionHasErrors('tanggal_kirim');
});
