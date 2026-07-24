<?php

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Database\Seeders\DisposisiDemoSeeder;
use Database\Seeders\JabatanSeeder;
use Database\Seeders\PegawaiDemoSeeder;
use Database\Seeders\SuratPegawaiDemoSeeder;
use Database\Seeders\UnitKerjaSeeder;

function dispositionEmployeeAccount(string $nip, string $name): array
{
    $user = User::factory()->create(['role' => 'pegawai', 'nip' => $nip, 'name' => $name]);
    $pegawai = Pegawai::create(['user_id' => $user->id, 'nip' => $nip, 'nama' => $name, 'email' => $user->email]);
    return [$user, $pegawai];
}

it('menampilkan isi tabel disposisi masuk secara lengkap dan aman per pegawai', function () {
    [$user, $pegawai] = dispositionEmployeeAccount('PEG-DSP-IN-01', 'Penerima Disposisi');
    [, $other] = dispositionEmployeeAccount('PEG-DSP-IN-02', 'Pegawai Lain');
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pengirim']);
    $surat = Surat::create([
        'user_id' => $admin->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-DSP-TABEL-001',
        'tanggal_surat' => now(), 'perihal' => 'Pengujian isi tabel disposisi', 'status' => 'diproses',
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id, 'pengirim_id' => $admin->id,
        'catatan' => 'Pelajari dokumen dan siapkan laporan tindak lanjut.',
        'prioritas' => 'Tinggi', 'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create(['disposisi_id' => $disposisi->id, 'pegawai_id' => $pegawai->id]);
    DisposisiTujuan::create(['disposisi_id' => $disposisi->id, 'pegawai_id' => $other->id]);

    $this->actingAs($user)->get(route('pegawai.disposisi.index'))
        ->assertOk()->assertSee('SM-DSP-TABEL-001')->assertSee('Pengujian isi tabel disposisi')
        ->assertSee('Pelajari dokumen')->assertSee('Admin Pengirim')->assertSee('Tinggi')
        ->assertSee('Belum Dibaca');
});

it('menyiapkan isi tabel disposisi masuk dan terkirim untuk pegawai demo', function () {
    User::factory()->create(['role' => 'admin', 'name' => 'Admin Demo Disposisi']);

    $this->seed([
        JabatanSeeder::class,
        UnitKerjaSeeder::class,
        PegawaiDemoSeeder::class,
        SuratPegawaiDemoSeeder::class,
        DisposisiDemoSeeder::class,
    ]);

    $pegawai = Pegawai::where('nip', '198801010001')->firstOrFail();

    expect(DisposisiTujuan::where('pegawai_id', $pegawai->id)->count())->toBeGreaterThanOrEqual(5)
        ->and(Disposisi::where('pengirim_id', $pegawai->user_id)->count())->toBeGreaterThanOrEqual(1);

    $this->actingAs($pegawai->user)
        ->get(route('pegawai.disposisi.index'))
        ->assertOk()
        ->assertSee('Disposisi Saya')
        ->assertSee('Pelajari isi surat')
        ->assertSee('Belum Dibaca')
        ->assertSee('Disposisi Terkirim');

    $this->actingAs($pegawai->user)
        ->get(route('pegawai.disposisi.terkirim'))
        ->assertOk()
        ->assertSee('Disposisi Terkirim')
        ->assertSee('Mohon tindak lanjuti surat demo')
        ->assertSee('Detail', false)
        ->assertSee('Edit', false)
        ->assertSee('Hapus', false);
});

it('membuat disposisi kepada pegawai lain dari surat yang dapat diakses', function () {
    [$sender, $senderProfile] = dispositionEmployeeAccount('PEG-DSP-OUT-01', 'Pegawai Pengirim');
    [$recipient, $recipientProfile] = dispositionEmployeeAccount('PEG-DSP-OUT-02', 'Pegawai Penerima');
    $surat = Surat::create([
        'user_id' => $sender->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-DSP-OUT-001',
        'tanggal_surat' => now(), 'perihal' => 'Surat untuk diteruskan', 'status' => 'diverifikasi',
    ]);

    $this->actingAs($sender)->get(route('pegawai.disposisi.create'))
        ->assertOk()->assertSee('SM-DSP-OUT-001')->assertSee('Pegawai Penerima');
    $this->actingAs($sender)->post(route('pegawai.disposisi.store'), [
        'surat_id' => $surat->id, 'pegawai_id' => [$recipientProfile->id],
        'catatan' => 'Mohon siapkan telaah atas surat ini.', 'prioritas' => 'Sedang',
        'tanggal_disposisi' => now()->format('Y-m-d'),
    ])->assertRedirect(route('pegawai.disposisi.index'))->assertSessionHas('success');

    $disposisi = Disposisi::where('pengirim_id', $sender->id)->firstOrFail();
    $this->assertDatabaseHas('disposisi_tujuans', [
        'disposisi_id' => $disposisi->id, 'pegawai_id' => $recipientProfile->id, 'status' => 'Belum Dibaca',
    ]);
    $this->actingAs($recipient)->get(route('pegawai.disposisi.index'))
        ->assertOk()->assertSee('Mohon siapkan telaah');
});

it('mengisi tabel disposisi pegawai kosong saat halaman disposisi dibuka langsung', function () {
    [$user, $pegawai] = dispositionEmployeeAccount('PEG-DSP-EMPTY-INDEX', 'Pegawai Kosong Disposisi');

    expect(DisposisiTujuan::where('pegawai_id', $pegawai->id)->count())->toBe(0);

    $this->actingAs($user)->get(route('pegawai.disposisi.index'))
        ->assertOk()
        ->assertSee('Disposisi Saya')
        ->assertSee('Pelajari isi surat')
        ->assertSee('Belum Dibaca')
        ->assertDontSee('Belum ada disposisi');

    expect(DisposisiTujuan::where('pegawai_id', $pegawai->id)->count())->toBeGreaterThanOrEqual(5);
});


it('menampilkan detail dan memperbarui disposisi terkirim yang belum dibaca', function () {
    [$sender] = dispositionEmployeeAccount('PEG-DSP-EDIT-01', 'Pengirim Edit');
    [, $recipient] = dispositionEmployeeAccount('PEG-DSP-EDIT-02', 'Penerima Edit');
    $surat = Surat::create([
        'user_id' => $sender->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-DSP-EDIT-001',
        'tanggal_surat' => now(), 'perihal' => 'Surat edit disposisi', 'status' => 'diverifikasi',
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id, 'pengirim_id' => $sender->id, 'catatan' => 'Instruksi awal',
        'prioritas' => 'Rendah', 'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create(['disposisi_id' => $disposisi->id, 'pegawai_id' => $recipient->id]);

    $this->actingAs($sender)->get(route('pegawai.disposisi.sent.show', $disposisi))
        ->assertOk()->assertSee('Instruksi awal')->assertSee('Penerima Edit');
    $this->actingAs($sender)->put(route('pegawai.disposisi.update', $disposisi), [
        'surat_id' => $surat->id, 'pegawai_id' => [$recipient->id],
        'catatan' => 'Instruksi sudah diperbarui', 'prioritas' => 'Tinggi',
        'tanggal_disposisi' => now()->format('Y-m-d'),
    ])->assertRedirect(route('pegawai.disposisi.sent.show', $disposisi))->assertSessionHas('success');

    expect($disposisi->fresh())->catatan->toBe('Instruksi sudah diperbarui')->prioritas->toBe('Tinggi');
});

it('menghapus disposisi sendiri yang belum dibaca dan menolak akses milik orang lain', function () {
    [$sender] = dispositionEmployeeAccount('PEG-DSP-DEL-01', 'Pengirim Hapus');
    [$other] = dispositionEmployeeAccount('PEG-DSP-DEL-02', 'Pengirim Lain');
    [, $recipient] = dispositionEmployeeAccount('PEG-DSP-DEL-03', 'Penerima Hapus');
    $surat = Surat::create([
        'user_id' => $sender->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-DSP-DEL-001',
        'tanggal_surat' => now(), 'perihal' => 'Surat hapus disposisi', 'status' => 'diverifikasi',
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id, 'pengirim_id' => $sender->id, 'catatan' => 'Dapat dihapus',
        'prioritas' => 'Rendah', 'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create(['disposisi_id' => $disposisi->id, 'pegawai_id' => $recipient->id]);

    $this->actingAs($other)->get(route('pegawai.disposisi.sent.show', $disposisi))->assertNotFound();
    $this->actingAs($sender)->delete(route('pegawai.disposisi.destroy', $disposisi))
        ->assertRedirect(route('pegawai.disposisi.index'))->assertSessionHas('success');
    expect(Disposisi::find($disposisi->id))->toBeNull();
});

it('mengunci edit dan hapus setelah salah satu penerima membaca disposisi', function () {
    [$sender] = dispositionEmployeeAccount('PEG-DSP-LOCK-01', 'Pengirim Terkunci');
    [, $recipient] = dispositionEmployeeAccount('PEG-DSP-LOCK-02', 'Penerima Membaca');
    $surat = Surat::create([
        'user_id' => $sender->id, 'jenis_surat' => 'masuk', 'nomor_surat' => 'SM-DSP-LOCK-001',
        'tanggal_surat' => now(), 'perihal' => 'Surat disposisi terkunci', 'status' => 'diverifikasi',
    ]);
    $disposisi = Disposisi::create([
        'surat_id' => $surat->id, 'pengirim_id' => $sender->id, 'catatan' => 'Sudah dibaca',
        'prioritas' => 'Sedang', 'tanggal_disposisi' => now(),
    ]);
    DisposisiTujuan::create([
        'disposisi_id' => $disposisi->id, 'pegawai_id' => $recipient->id,
        'status' => 'Sudah Dibaca', 'dibaca_pada' => now(),
    ]);

    $this->actingAs($sender)->get(route('pegawai.disposisi.edit', $disposisi))
        ->assertRedirect(route('pegawai.disposisi.sent.show', $disposisi))->assertSessionHas('error');
    $this->actingAs($sender)->delete(route('pegawai.disposisi.destroy', $disposisi))
        ->assertSessionHas('error');
    expect(Disposisi::find($disposisi->id))->not->toBeNull();
});
