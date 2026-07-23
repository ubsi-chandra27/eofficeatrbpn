<?php

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;

it('menampilkan halaman daftar tambah edit dan detail jabatan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $jabatan = Jabatan::create(['kode' => 'ANL', 'nama' => 'Analis Pertanahan', 'deskripsi' => 'Menganalisis data pertanahan.']);

    $this->actingAs($admin)->get(route('admin.jabatan.index'))
        ->assertOk()->assertSee('Analis Pertanahan')->assertSee('ANL');
    $this->actingAs($admin)->get(route('admin.jabatan.create'))
        ->assertOk()->assertSee('Tambah Jabatan');
    $this->actingAs($admin)->get(route('admin.jabatan.edit', $jabatan))
        ->assertOk()->assertSee('Edit Jabatan')->assertSee('Analis Pertanahan');
    $this->actingAs($admin)->get(route('admin.jabatan.show', $jabatan))
        ->assertOk()->assertSee('Detail Jabatan')->assertSee('Menganalisis data pertanahan.');
});

it('menambah jabatan dengan normalisasi kode dan mencatat aktivitas', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.jabatan.store'), [
        'kode' => '  kabag-01 ',
        'nama' => '  Kepala Bagian Administrasi  ',
        'deskripsi' => '  Memimpin administrasi kantor.  ',
    ])->assertRedirect(route('admin.jabatan.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('jabatan', [
        'kode' => 'KABAG-01',
        'nama' => 'Kepala Bagian Administrasi',
        'deskripsi' => 'Memimpin administrasi kantor.',
    ])->assertDatabaseHas('log_aktivitas', [
        'user_id' => $admin->id,
        'action' => 'Tambah Jabatan',
    ]);
});

it('memvalidasi kode nama unik dan panjang deskripsi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Jabatan::create(['kode' => 'DUPLIKAT', 'nama' => 'Jabatan Duplikat']);

    $this->actingAs($admin)->post(route('admin.jabatan.store'), [
        'kode' => 'DUPLIKAT',
        'nama' => 'Jabatan Duplikat',
        'deskripsi' => str_repeat('a', 1001),
    ])->assertSessionHasErrors(['kode', 'nama', 'deskripsi']);

    $this->actingAs($admin)->post(route('admin.jabatan.store'), [
        'kode' => 'kode tidak valid!',
        'nama' => 'Nama Baru',
    ])->assertSessionHasErrors('kode');
});

it('memperbarui jabatan tanpa mengubah relasi pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $jabatan = Jabatan::create(['kode' => 'OLD', 'nama' => 'Nama Lama']);
    $unit = UnitKerja::create(['kode' => 'UNIT-JBT', 'nama' => 'Unit Pengujian Jabatan']);
    $pegawai = Pegawai::create([
        'nip' => 'PEG-JBT-001', 'nama' => 'Pegawai Jabatan', 'email' => 'pegawai.jabatan@example.test',
        'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);

    $this->actingAs($admin)->put(route('admin.jabatan.update', $jabatan), [
        'kode' => 'new', 'nama' => 'Nama Jabatan Baru', 'deskripsi' => 'Deskripsi baru.',
    ])->assertRedirect(route('admin.jabatan.index'))->assertSessionHas('success');

    expect($jabatan->fresh())->kode->toBe('NEW')->nama->toBe('Nama Jabatan Baru')
        ->and($pegawai->fresh()->jabatan_id)->toBe($jabatan->id);
});

it('memfilter jabatan berdasarkan penggunaan oleh pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $terpakai = Jabatan::create(['kode' => 'USED', 'nama' => 'Jabatan Terpakai']);
    Jabatan::create(['kode' => 'EMPTY', 'nama' => 'Jabatan Kosong']);
    Pegawai::create(['nip' => 'PEG-FILTER-JBT', 'nama' => 'Pegawai Filter', 'email' => 'filter.jabatan@example.test', 'jabatan_id' => $terpakai->id]);

    $this->actingAs($admin)->get(route('admin.jabatan.index', ['penggunaan' => 'terpakai']))
        ->assertOk()->assertSee('Jabatan Terpakai')->assertDontSee('Jabatan Kosong');
    $this->actingAs($admin)->get(route('admin.jabatan.index', ['penggunaan' => 'kosong']))
        ->assertOk()->assertSee('Jabatan Kosong')->assertDontSee('Jabatan Terpakai');
});

it('menghapus jabatan kosong tetapi menolak jabatan yang digunakan pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kosong = Jabatan::create(['kode' => 'DEL', 'nama' => 'Boleh Dihapus']);
    $terpakai = Jabatan::create(['kode' => 'LOCK', 'nama' => 'Tidak Boleh Dihapus']);
    Pegawai::create(['nip' => 'PEG-LOCK-JBT', 'nama' => 'Pegawai Pengunci', 'email' => 'lock.jabatan@example.test', 'jabatan_id' => $terpakai->id]);

    $this->actingAs($admin)->delete(route('admin.jabatan.destroy', $terpakai))
        ->assertSessionHas('error');
    $this->assertDatabaseHas('jabatan', ['id' => $terpakai->id]);

    $this->actingAs($admin)->delete(route('admin.jabatan.destroy', $kosong))
        ->assertRedirect(route('admin.jabatan.index'))->assertSessionHas('success');
    $this->assertDatabaseMissing('jabatan', ['id' => $kosong->id]);
});
