<?php

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;

it('menampilkan halaman daftar tambah edit dan detail unit kerja', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $unit = UnitKerja::create(['kode' => 'SPH', 'nama' => 'Seksi Penetapan Hak', 'deskripsi' => 'Mengelola penetapan hak tanah.']);

    $this->actingAs($admin)->get(route('admin.unit.kerja.index'))
        ->assertOk()->assertSee('Seksi Penetapan Hak')->assertSee('SPH');
    $this->actingAs($admin)->get(route('admin.unit.kerja.create'))
        ->assertOk()->assertSee('Tambah Unit Kerja');
    $this->actingAs($admin)->get(route('admin.unit.kerja.edit', $unit))
        ->assertOk()->assertSee('Edit Unit Kerja')->assertSee('Seksi Penetapan Hak');
    $this->actingAs($admin)->get(route('admin.unit.kerja.show', $unit))
        ->assertOk()->assertSee('Detail Unit Kerja')->assertSee('Mengelola penetapan hak tanah.');
});

it('menambah unit kerja dengan normalisasi dan log aktivitas', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.unit.kerja.store'), [
        'kode' => '  tata-usaha ',
        'nama' => '  Bagian Tata Usaha  ',
        'deskripsi' => '  Mengelola administrasi internal.  ',
    ])->assertRedirect(route('admin.unit.kerja.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('unit_kerja', [
        'kode' => 'TATA-USAHA',
        'nama' => 'Bagian Tata Usaha',
        'deskripsi' => 'Mengelola administrasi internal.',
    ])->assertDatabaseHas('log_aktivitas', [
        'user_id' => $admin->id,
        'action' => 'Tambah Unit Kerja',
    ]);
});

it('memvalidasi kode nama unik format dan deskripsi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    UnitKerja::create(['kode' => 'DUPLIKAT', 'nama' => 'Unit Duplikat']);

    $this->actingAs($admin)->post(route('admin.unit.kerja.store'), [
        'kode' => ' DUPLIKAT ',
        'nama' => ' Unit Duplikat ',
        'deskripsi' => str_repeat('x', 1001),
    ])->assertSessionHasErrors(['kode', 'nama', 'deskripsi']);

    $this->actingAs($admin)->post(route('admin.unit.kerja.store'), [
        'kode' => 'kode tidak valid!', 'nama' => 'Unit Baru',
    ])->assertSessionHasErrors('kode');
});

it('memperbarui unit kerja tanpa memutus relasi pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $unit = UnitKerja::create(['kode' => 'OLD', 'nama' => 'Unit Lama']);
    $jabatan = Jabatan::create(['kode' => 'J-UNIT', 'nama' => 'Analis Unit']);
    $pegawai = Pegawai::create([
        'nip' => 'PEG-UNIT-001', 'nama' => 'Pegawai Unit', 'email' => 'pegawai.unit@example.test',
        'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);

    $this->actingAs($admin)->put(route('admin.unit.kerja.update', $unit), [
        'kode' => 'new-unit', 'nama' => 'Unit Kerja Baru', 'deskripsi' => 'Deskripsi baru.',
    ])->assertRedirect(route('admin.unit.kerja.index'))->assertSessionHas('success');

    expect($unit->fresh())->kode->toBe('NEW-UNIT')->nama->toBe('Unit Kerja Baru')
        ->and($pegawai->fresh()->unit_kerja_id)->toBe($unit->id);
});

it('memfilter unit berdasarkan penggunaannya oleh pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $terpakai = UnitKerja::create(['kode' => 'USED', 'nama' => 'Unit Terpakai']);
    UnitKerja::create(['kode' => 'EMPTY', 'nama' => 'Unit Kosong']);
    Pegawai::create(['nip' => 'PEG-FILTER-UNIT', 'nama' => 'Pegawai Filter Unit', 'email' => 'filter.unit@example.test', 'unit_kerja_id' => $terpakai->id]);

    $this->actingAs($admin)->get(route('admin.unit.kerja.index', ['penggunaan' => 'terpakai']))
        ->assertOk()->assertSee('Unit Terpakai')->assertDontSee('Unit Kosong');
    $this->actingAs($admin)->get(route('admin.unit.kerja.index', ['penggunaan' => 'kosong']))
        ->assertOk()->assertSee('Unit Kosong')->assertDontSee('Unit Terpakai');
});

it('menghapus unit kosong tetapi melindungi unit yang digunakan pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kosong = UnitKerja::create(['kode' => 'DEL', 'nama' => 'Unit Boleh Dihapus']);
    $terpakai = UnitKerja::create(['kode' => 'LOCK', 'nama' => 'Unit Tidak Boleh Dihapus']);
    Pegawai::create(['nip' => 'PEG-LOCK-UNIT', 'nama' => 'Pegawai Pengunci Unit', 'email' => 'lock.unit@example.test', 'unit_kerja_id' => $terpakai->id]);

    $this->actingAs($admin)->delete(route('admin.unit.kerja.destroy', $terpakai))
        ->assertSessionHas('error');
    $this->assertDatabaseHas('unit_kerja', ['id' => $terpakai->id]);

    $this->actingAs($admin)->delete(route('admin.unit.kerja.destroy', $kosong))
        ->assertRedirect(route('admin.unit.kerja.index'))->assertSessionHas('success');
    $this->assertDatabaseMissing('unit_kerja', ['id' => $kosong->id]);
});
