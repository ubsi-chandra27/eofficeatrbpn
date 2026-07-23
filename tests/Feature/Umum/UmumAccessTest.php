<?php

use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createUmumSurat(User $owner, string $number): Surat
{
    return Surat::create([
        'user_id' => $owner->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => $number,
        'tanggal_surat' => now()->toDateString(),
        'perihal' => 'Permohonan pengujian',
        'asal_surat' => $owner->name,
        'tujuan_surat' => 'Bagian Administrasi',
        'metode' => 'Email',
        'status' => 'diajukan',
    ]);
}

it('menampilkan halaman utama umum tanpa error', function () {
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($user)->get(route('umum.dashboard'))
        ->assertOk()
        ->assertSee('Aktivitas Terbaru')
        ->assertSee('Nomor Pengajuan')
        ->assertSee('VISI')
        ->assertSee('MISI')
        ->assertSee('Makna lambang kementerian')
        ->assertSee('Empat Butir Padi')
        ->assertSee('Gelombang Hijau dan Biru');
    $this->actingAs($user)->get(route('umum.surat.index'))->assertOk();
    $this->actingAs($user)->get(route('umum.surat.create'))->assertOk();
    $this->actingAs($user)->get(route('umum.cari.form'))->assertOk();
    $this->actingAs($user)->get(route('umum.layanan.index'))->assertOk();
    $this->actingAs($user)->get(route('umum.menteri'))->assertOk();
    $this->actingAs($user)->get(route('umum.wakil'))->assertOk();
    $this->actingAs($user)->get(route('umum.struktur'))->assertOk();
});

it('membatasi detail dan pencarian surat berdasarkan pemilik', function () {
    $owner = User::factory()->create(['role' => 'umum']);
    $other = User::factory()->create(['role' => 'umum']);
    $letter = createUmumSurat($owner, 'UMUM-PRIVASI-001');

    $this->actingAs($other)->get(route('umum.surat.show', $letter))->assertNotFound();
    $this->actingAs($other)->post(route('umum.cari.proses'), [
        'nomor_berkas' => $letter->nomor_surat,
    ])->assertSessionHas('error');

    $this->actingAs($owner)->get(route('umum.surat.show', $letter))->assertOk();
});

it('menampilkan isi tabel surat saya beserta tahap proses dan hanya surat masuk milik pengguna', function () {
    $owner = User::factory()->create(['role' => 'umum']);
    $other = User::factory()->create(['role' => 'umum']);

    $letter = createUmumSurat($owner, 'UMUM-TABEL-001');
    $letter->update([
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'perihal' => 'Permohonan salinan dokumen pertanahan',
        'nomor_kontak' => '081234567890',
        'asal_instansi' => 'Komunitas Warga',
        'file_path' => 'surat/umum/contoh.pdf',
        'status' => 'dikembalikan',
        'catatan_admin' => 'Lampiran identitas perlu diperjelas.',
    ]);

    createUmumSurat($other, 'UMUM-MILIK-LAIN');
    Surat::factory()->create([
        'user_id' => $owner->id,
        'jenis_surat' => 'keluar',
        'nomor_surat' => 'UMUM-SURAT-KELUAR',
    ]);

    $this->actingAs($owner)->get(route('umum.surat.index'))
        ->assertOk()
        ->assertSee('UMUM-TABEL-001')
        ->assertSee('Permohonan Dokumen')
        ->assertSee('Permohonan salinan dokumen pertanahan')
        ->assertSee('081234567890')
        ->assertSee('Komunitas Warga')
        ->assertSee('PDF')
        ->assertSee('Harus diperbaiki dan dikirim ulang')
        ->assertSee('Lampiran identitas perlu diperjelas.')
        ->assertDontSee('UMUM-MILIK-LAIN')
        ->assertDontSee('UMUM-SURAT-KELUAR');
});

it('mencari surat saya berdasarkan kontak atau instansi dan menampilkan catatan admin di detail', function () {
    $owner = User::factory()->create(['role' => 'umum']);
    $letter = createUmumSurat($owner, 'UMUM-CARI-001');
    $letter->update([
        'nomor_kontak' => '081299998888',
        'asal_instansi' => 'Forum Masyarakat',
        'status' => 'dikembalikan',
        'catatan_admin' => 'Mohon unggah ulang dokumen yang terbaca.',
    ]);

    $this->actingAs($owner)->get(route('umum.surat.index', ['keyword' => '081299998888']))
        ->assertOk()->assertSee('UMUM-CARI-001');
    $this->actingAs($owner)->get(route('umum.surat.index', ['keyword' => 'Forum Masyarakat']))
        ->assertOk()->assertSee('UMUM-CARI-001');
    $this->actingAs($owner)->get(route('umum.surat.show', $letter))
        ->assertOk()
        ->assertSee('Harus diperbaiki dan dikirim ulang')
        ->assertSee('Mohon unggah ulang dokumen yang terbaca.')
        ->assertSee('Perbaiki Pengajuan');
});

it('mengintegrasikan pilihan layanan dengan form pengajuan', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $this->actingAs($user)->get(route('umum.layanan.index'))
        ->assertOk()->assertSee('Permohonan Informasi')->assertSee('Informasi Layanan');

    foreach ([
        'informasi' => 'Ajukan Pertanyaan',
        'dokumen' => 'Minta Dokumen',
        'penyampaian-surat' => 'Sampaikan Surat',
        'pengaduan' => 'Buat Pengaduan',
        'lainnya' => 'Ajukan Kebutuhan',
    ] as $slug => $action) {
        $this->actingAs($user)->get(route('umum.layanan.show', $slug))
            ->assertOk()
            ->assertSee($action);
    }

    $this->actingAs($user)->get(route('umum.surat.create', ['kategori' => 'Pengaduan']))
        ->assertOk()->assertSee('value="Pengaduan" selected', false);

    $this->actingAs($user)->get('/umum/layanan/tidak-ada')->assertNotFound();
});

it('mengunci perubahan surat yang sedang diverifikasi', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $letter = createUmumSurat($user, 'UMUM-LOCK-001');

    $this->actingAs($user)->get(route('umum.surat.edit', $letter))->assertNotFound();
    $this->actingAs($user)->delete(route('umum.surat.destroy', $letter))->assertNotFound();
});

it('membuat nomor otomatis untuk pengajuan masyarakat', function () {
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($user)->post(route('umum.surat.store'), [
        'kategori_pengajuan' => 'Permohonan Informasi',
        'nomor_kontak' => '0812 3456 7890',
        'asal_instansi' => 'Komunitas Warga',
        'perihal' => 'Permohonan informasi layanan',
        'deskripsi' => 'Mohon diberikan informasi mengenai prosedur layanan yang tersedia.',
    ])->assertRedirect(route('umum.surat.index'));

    $surat = Surat::where('user_id', $user->id)->firstOrFail();
    expect($surat->nomor_surat)->toStartWith('UMUM/'.now()->format('Ymd').'/')
        ->and($surat->tanggal_surat->isToday())->toBeTrue()
        ->and($surat->jenis_surat)->toBe('masuk')
        ->and($surat->status)->toBe('diajukan')
        ->and($surat->kategori_pengajuan)->toBe('Permohonan Informasi')
        ->and($surat->nomor_kontak)->toBe('0812 3456 7890');
});

it('mengunggah dan mengunduh lampiran yang didukung secara aman', function (string $filename, string $mime) {
    Storage::fake('local');
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($user)->post(route('umum.surat.store'), [
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Pengujian lampiran hosting',
        'deskripsi' => 'Memastikan lampiran dapat tersimpan dan diunduh kembali.',
        'file_path' => UploadedFile::fake()->create($filename, 128, $mime),
    ])->assertRedirect(route('umum.surat.index'));

    $surat = Surat::where('user_id', $user->id)->firstOrFail();
    Storage::disk('local')->assertExists($surat->file_path);
    $this->actingAs($user)->get(route('umum.surat.download', $surat))->assertOk();

    $other = User::factory()->create(['role' => 'umum']);
    $this->actingAs($other)->get(route('umum.surat.download', $surat))->assertNotFound();
})->with([
    ['lampiran.pdf', 'application/pdf'],
    ['lampiran.doc', 'application/msword'],
    ['lampiran.jpg', 'image/jpeg'],
]);

it('memfilter daftar pengajuan berdasarkan kelompok status dan kategori', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $surat = createUmumSurat($user, 'UMUM-FILTER-001');
    $surat->update(['kategori_pengajuan' => 'Pengaduan', 'status' => 'dikembalikan']);

    $this->actingAs($user)->get(route('umum.surat.index', [
        'kategori' => 'Pengaduan', 'status' => 'perbaikan',
    ]))->assertOk()->assertSee('UMUM-FILTER-001')->assertSee('Perlu Perbaikan');

    $this->actingAs($user)->get(route('umum.surat.index', ['status' => 'selesai']))
        ->assertOk()->assertDontSee('UMUM-FILTER-001');
});

it('menautkan peringatan dashboard ke filter perbaikan yang benar', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $surat = createUmumSurat($user, 'UMUM-DASHBOARD-PERBAIKAN');
    $surat->update(['status' => 'dikembalikan']);

    $this->actingAs($user)->get(route('umum.dashboard'))
        ->assertOk()
        ->assertSee(route('umum.surat.index', ['status' => 'perbaikan']), false);
});

it('menolak lampiran berbahaya dan lampiran yang melebihi batas', function () {
    Storage::fake('local');
    $user = User::factory()->create(['role' => 'umum']);
    $base = [
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Validasi lampiran',
        'deskripsi' => 'Memastikan hanya dokumen yang aman dan sesuai ukuran yang diterima.',
    ];

    $this->actingAs($user)->post(route('umum.surat.store'), $base + [
        'file_path' => UploadedFile::fake()->create('program.exe', 10, 'application/x-msdownload'),
    ])->assertSessionHasErrors('file_path');

    $this->actingAs($user)->post(route('umum.surat.store'), $base + [
        'file_path' => UploadedFile::fake()->create('terlalu-besar.pdf', 6 * 1024, 'application/pdf'),
    ])->assertSessionHasErrors('file_path');

    expect(Surat::where('user_id', $user->id)->count())->toBe(0);
});

it('menolak nomor kontak dan deskripsi yang tidak valid', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $base = [
        'kategori_pengajuan' => 'Pengaduan',
        'perihal' => 'Validasi data pengajuan',
    ];

    $this->actingAs($user)->post(route('umum.surat.store'), $base + [
        'nomor_kontak' => 'telepon-tidak-valid',
        'deskripsi' => 'Deskripsi valid.',
    ])->assertSessionHasErrors('nomor_kontak');

    $this->actingAs($user)->post(route('umum.surat.store'), $base + [
        'nomor_kontak' => '081234567890',
        'deskripsi' => str_repeat('A', 2001),
    ])->assertSessionHasErrors('deskripsi');
});

it('membatasi frekuensi pembuatan pengajuan', function () {
    $user = User::factory()->create(['role' => 'umum']);
    $payload = [
        'kategori_pengajuan' => 'Permohonan Informasi',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Uji pembatasan pengajuan',
        'deskripsi' => 'Pengajuan untuk memastikan pembatasan frekuensi berjalan.',
    ];

    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($user)->post(route('umum.surat.store'), $payload)->assertRedirect();
    }

    $this->actingAs($user)->post(route('umum.surat.store'), $payload)
        ->assertTooManyRequests();
});

it('membersihkan lampiran baru ketika pembuatan pengajuan gagal', function () {
    Storage::fake('local');
    $this->withoutExceptionHandling();
    $user = User::factory()->create(['role' => 'umum']);
    Surat::creating(fn () => throw new RuntimeException('Simulasi kegagalan database'));

    expect(fn () => $this->actingAs($user)->post(route('umum.surat.store'), [
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Simulasi kegagalan',
        'deskripsi' => 'Memastikan berkas baru dibersihkan ketika database gagal.',
        'file_path' => UploadedFile::fake()->create('baru.pdf', 128, 'application/pdf'),
    ]))->toThrow(RuntimeException::class, 'Simulasi kegagalan database');

    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('mempertahankan lampiran lama ketika penggantian lampiran gagal', function () {
    Storage::fake('local');
    $this->withoutExceptionHandling();
    $user = User::factory()->create(['role' => 'umum']);
    Storage::disk('local')->put('surat-umum/lama.pdf', 'lampiran-lama');
    $surat = createUmumSurat($user, 'UMUM-ROLLBACK-001');
    $surat->update(['status' => 'dikembalikan', 'file_path' => 'surat-umum/lama.pdf']);
    Surat::updating(fn () => throw new RuntimeException('Simulasi kegagalan pembaruan'));

    expect(fn () => $this->actingAs($user)->put(route('umum.surat.update', $surat), [
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Penggantian lampiran',
        'deskripsi' => 'Memastikan lampiran lama tetap tersedia saat pembaruan gagal.',
        'file_path' => UploadedFile::fake()->create('pengganti.pdf', 128, 'application/pdf'),
    ]))->toThrow(RuntimeException::class, 'Simulasi kegagalan pembaruan');

    Storage::disk('local')->assertExists('surat-umum/lama.pdf');
    expect(Storage::disk('local')->allFiles())->toBe(['surat-umum/lama.pdf']);
});
