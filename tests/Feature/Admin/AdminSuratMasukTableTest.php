<?php

use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function adminIncomingLetter(User $owner, array $attributes = []): Surat
{
    return Surat::create(array_merge([
        'user_id' => $owner->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'SM-ADMIN-001',
        'nomor_agenda' => 'AGD-ADMIN-001',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Undangan rapat koordinasi',
        'asal_surat' => 'Kantor Wilayah',
        'status' => 'diajukan',
        'is_priority' => true,
    ], $attributes));
}

it('menampilkan tabel surat masuk admin dengan data dan status yang benar', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    adminIncomingLetter($admin);
    adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-ADMIN-002',
        'nomor_agenda' => 'AGD-ADMIN-002',
        'perihal' => 'Pemberitahuan pembaruan dokumen',
        'asal_surat' => 'Bagian Tata Usaha',
        'status' => 'diteruskan_ke_pimpinan',
        'is_priority' => false,
    ]);
    Surat::create([
        'user_id' => $admin->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'SK-TIDAK-TAMPIL',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Surat keluar',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.surat.masuk.index'))
        ->assertOk()
        ->assertSee('SM-ADMIN-001')
        ->assertSee('Undangan rapat koordinasi')
        ->assertSee('Kantor Wilayah')
        ->assertSee('Prioritas')
        ->assertSee('Diajukan')
        ->assertSee('Diteruskan ke Pimpinan')
        ->assertDontSee('SK-TIDAK-TAMPIL');
});

it('memakai pencarian dan filter status pada surat masuk admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    adminIncomingLetter($admin, ['nomor_surat' => 'SM-CARI-001', 'asal_surat' => 'Masyarakat']);
    adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-CARI-002',
        'perihal' => 'Klarifikasi khusus',
        'asal_surat' => 'Seksi Pelayanan',
        'status' => 'dikembalikan',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.surat.masuk.index', ['keyword' => 'Klarifikasi', 'status' => 'dikembalikan']))
        ->assertOk()
        ->assertSee('SM-CARI-002')
        ->assertDontSee('SM-CARI-001');
});

it('mengabaikan filter status yang tidak dikenal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    adminIncomingLetter($admin, ['nomor_surat' => 'SM-STATUS-AMAN']);

    $this->actingAs($admin)
        ->get(route('admin.surat.masuk.index', ['status' => 'status_tidak_valid']))
        ->assertOk()
        ->assertSee('SM-STATUS-AMAN');
});

it('menambah dan menampilkan detail surat masuk admin', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.surat.masuk.store'), [
        'nomor_surat' => 'SM-CRUD-001',
        'tanggal_surat' => '2026-07-23',
        'perihal' => 'Surat masuk hasil pencatatan admin',
        'asal_surat' => 'Kantor Pertanahan Kota',
        'nomor_agenda' => 'AGD-CRUD-001',
        'metode' => 'Email',
        'deskripsi' => 'Dokumen diterima dalam kondisi lengkap.',
        'is_priority' => '1',
        'file_path' => UploadedFile::fake()->create('surat-masuk.pdf', 128, 'application/pdf'),
    ])->assertRedirect(route('admin.surat.masuk.index'));

    $surat = Surat::where('nomor_surat', 'SM-CRUD-001')->firstOrFail();
    expect($surat->jenis_surat)->toBe('masuk')
        ->and($surat->is_priority)->toBeTrue()
        ->and($surat->file_path)->not->toBeNull();
    Storage::disk('local')->assertExists($surat->file_path);

    $this->actingAs($admin)->get(route('admin.surat.masuk.show', $surat))
        ->assertOk()
        ->assertSee('SM-CRUD-001')
        ->assertSee('Kantor Pertanahan Kota')
        ->assertSee('Dokumen diterima dalam kondisi lengkap')
        ->assertSee($surat->status_label)
        ->assertSee('Lihat Lampiran');
});

it('mengedit seluruh data dan mengganti lampiran surat masuk secara aman', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('local')->put('surat-masuk/lama.pdf', 'berkas-lama');
    $surat = adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-EDIT-001',
        'asal_surat' => 'Pengirim Lama',
        'metode' => 'Pos',
        'file_path' => 'surat-masuk/lama.pdf',
    ]);

    $this->actingAs($admin)->put(route('admin.surat.masuk.update', $surat), [
        'nomor_surat' => 'SM-EDIT-002',
        'tanggal_surat' => '2026-07-24',
        'perihal' => 'Perihal setelah diperbarui',
        'asal_surat' => 'Pengirim Baru',
        'nomor_agenda' => 'AGD-EDIT-002',
        'metode' => 'Kurir',
        'deskripsi' => 'Deskripsi setelah diperbarui.',
        'file_path' => UploadedFile::fake()->create('pengganti.pdf', 128, 'application/pdf'),
    ])->assertRedirect(route('admin.surat.masuk.index'));

    $surat->refresh();
    expect($surat->nomor_surat)->toBe('SM-EDIT-002')
        ->and($surat->asal_surat)->toBe('Pengirim Baru')
        ->and($surat->nomor_agenda)->toBe('AGD-EDIT-002')
        ->and($surat->metode)->toBe('Kurir')
        ->and($surat->status)->toBe('diajukan')
        ->and($surat->is_priority)->toBeFalse();
    Storage::disk('local')->assertMissing('surat-masuk/lama.pdf');
    Storage::disk('local')->assertExists($surat->file_path);
});

it('menghapus surat masuk yang dikembalikan dengan soft delete dan mempertahankan lampiran', function () {
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('local')->put('surat-masuk/arsip.pdf', 'berkas-arsip');
    $surat = adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-HAPUS-001',
        'status' => 'dikembalikan',
        'file_path' => 'surat-masuk/arsip.pdf',
    ]);

    $this->actingAs($admin)->delete(route('admin.surat.masuk.destroy', $surat))
        ->assertRedirect(route('admin.surat.masuk.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('surats', ['id' => $surat->id]);
    Storage::disk('local')->assertExists('surat-masuk/arsip.pdf');
});

it('menolak penghapusan surat masuk yang sudah diproses', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-HAPUS-DITOLAK',
        'status' => 'diverifikasi',
    ]);

    $this->actingAs($admin)->delete(route('admin.surat.masuk.destroy', $surat))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('surats', ['id' => $surat->id, 'deleted_at' => null]);
});

it('meneruskan surat masuk terverifikasi walaupun tujuan pimpinan belum diisi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $surat = adminIncomingLetter($admin, [
        'nomor_surat' => 'SM-TERUSKAN-TANPA-TUJUAN',
        'status' => 'diverifikasi',
        'jabatan_pimpinan_id' => null,
        'nama_pimpinan' => null,
    ]);

    $this->actingAs($admin)->post(route('admin.surat.masuk.teruskan-pimpinan', $surat), [
        'metode_penerusan' => 'fisik',
        'catatan_pengantar' => 'Mohon arahan tindak lanjut.',
    ])->assertSessionHas('success')
        ->assertSessionMissing('error');

    $surat->refresh();

    expect($surat->status)->toBe('diteruskan_ke_pimpinan')
        ->and($surat->diteruskan_oleh)->toBe($admin->id)
        ->and($surat->catatan_pengantar)->toBe('Mohon arahan tindak lanjut.')
        ->and($surat->metode_penerusan)->toBe('fisik');

    $this->assertDatabaseHas('log_aktivitas', [
        'surat_id' => $surat->id,
        'action' => 'Diteruskan ke Pimpinan',
        'description' => 'Admin meneruskan surat SM-TERUSKAN-TANPA-TUJUAN kepada pimpinan terkait melalui fisik.',
    ]);
});
