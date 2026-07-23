<?php

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function employeeMasterData(): array
{
    return [
        Jabatan::create(['kode' => 'J-PEG', 'nama' => 'Analis Pertanahan']),
        UnitKerja::create(['kode' => 'U-PEG', 'nama' => 'Seksi Penetapan Hak']),
    ];
}

function employeePayload(Jabatan $jabatan, UnitKerja $unit, array $overrides = []): array
{
    return array_merge([
        'nip' => '199012312026001',
        'nama' => 'Budi Pegawai',
        'email' => 'budi.pegawai@example.test',
        'no_hp' => '081234567890',
        'alamat' => 'Jl. Administrasi No. 1',
        'jabatan_id' => $jabatan->id,
        'unit_kerja_id' => $unit->id,
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ], $overrides);
}

it('menampilkan daftar dan seluruh halaman CRUD pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    [$jabatan, $unit] = employeeMasterData();
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-LIST-001']);
    $pegawai = Pegawai::create([
        'user_id' => $user->id, 'nip' => $user->nip, 'nama' => 'Pegawai Terintegrasi',
        'email' => $user->email, 'no_hp' => '081111111111',
        'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);

    $this->actingAs($admin)->get(route('admin.pegawai.index'))
        ->assertOk()->assertSee('Pegawai Terintegrasi')->assertSee('PEG-LIST-001')
        ->assertSee('Akun Aktif');
    $this->actingAs($admin)->get(route('admin.pegawai.create'))->assertOk()->assertSee('Tambah Pegawai');
    $this->actingAs($admin)->get(route('admin.pegawai.edit', $pegawai))->assertOk()->assertSee('Edit Pegawai');
    $this->actingAs($admin)->get(route('admin.pegawai.show', $pegawai))
        ->assertOk()->assertSee('Pegawai Terintegrasi')->assertSee('Surat Dibuat')->assertSee('Disposisi Diterima');
});

it('menambah pegawai sekaligus akun login yang tersinkronisasi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    [$jabatan, $unit] = employeeMasterData();

    $this->actingAs($admin)->post(route('admin.pegawai.store'), employeePayload($jabatan, $unit))
        ->assertRedirect(route('admin.pegawai.index'))
        ->assertSessionHas('success');

    $pegawai = Pegawai::where('nip', '199012312026001')->firstOrFail();
    $user = User::where('nip', '199012312026001')->firstOrFail();
    expect($pegawai->user_id)->toBe($user->id)
        ->and($user->role)->toBe('pegawai')
        ->and(Hash::check('Password123!', $user->password))->toBeTrue();
});

it('memperbarui profil akun dan password pegawai secara atomik', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    [$jabatan, $unit] = employeeMasterData();
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-EDIT-001']);
    $pegawai = Pegawai::create([
        'user_id' => $user->id, 'nip' => $user->nip, 'nama' => 'Nama Lama',
        'email' => $user->email, 'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);

    $payload = employeePayload($jabatan, $unit, [
        'nip' => 'PEG-EDIT-002', 'nama' => 'Nama Baru', 'email' => 'nama.baru@example.test',
        'password' => 'PasswordBaru123!', 'password_confirmation' => 'PasswordBaru123!',
    ]);
    $this->actingAs($admin)->put(route('admin.pegawai.update', $pegawai), $payload)
        ->assertRedirect(route('admin.pegawai.index'))->assertSessionHas('success');

    $updatedUser = $user->fresh();
    expect($pegawai->fresh())->nip->toBe('PEG-EDIT-002')->nama->toBe('Nama Baru')
        ->and($updatedUser)->nip->toBe('PEG-EDIT-002')->name->toBe('Nama Baru')
        ->and(Hash::check('PasswordBaru123!', $updatedUser->password))->toBeTrue();
});

it('memvalidasi kontak dan menyediakan filter data pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    [$jabatan, $unit] = employeeMasterData();

    $this->actingAs($admin)->post(route('admin.pegawai.store'), employeePayload($jabatan, $unit, [
        'no_hp' => 'nomor-tidak-valid!',
    ]))->assertSessionHasErrors('no_hp');

    $user = User::factory()->create(['role' => 'pegawai', 'nip' => 'FILTER-PEG-001']);
    Pegawai::create([
        'user_id' => $user->id, 'nip' => $user->nip, 'nama' => 'Target Filter',
        'email' => $user->email, 'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);
    Pegawai::create(['nip' => 'FILTER-LAIN-001', 'nama' => 'Pegawai Tanpa Akun', 'email' => 'tanpa@example.test']);

    $this->actingAs($admin)->get(route('admin.pegawai.index', [
        'keyword' => 'Target', 'jabatan_id' => $jabatan->id,
        'unit_kerja_id' => $unit->id, 'status_akun' => 'aktif',
    ]))->assertOk()->assertSee('Target Filter')->assertDontSee('Pegawai Tanpa Akun');
});

it('menghapus profil dan akun pegawai secara soft delete', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    [$jabatan, $unit] = employeeMasterData();
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-DELETE-001']);
    $pegawai = Pegawai::create([
        'user_id' => $user->id, 'nip' => $user->nip, 'nama' => 'Pegawai Dihapus',
        'email' => $user->email, 'jabatan_id' => $jabatan->id, 'unit_kerja_id' => $unit->id,
    ]);

    $this->actingAs($admin)->delete(route('admin.pegawai.destroy', $pegawai))
        ->assertRedirect(route('admin.pegawai.index'))->assertSessionHas('success');

    expect(Pegawai::find($pegawai->id))->toBeNull()
        ->and(Pegawai::withTrashed()->find($pegawai->id))->not->toBeNull()
        ->and(User::find($user->id))->toBeNull()
        ->and(User::withTrashed()->find($user->id))->not->toBeNull();
});
