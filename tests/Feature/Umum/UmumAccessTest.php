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
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'umum']);

    $this->actingAs($user)->post(route('umum.surat.store'), [
        'kategori_pengajuan' => 'Permohonan Dokumen',
        'nomor_kontak' => '081234567890',
        'perihal' => 'Pengujian lampiran hosting',
        'deskripsi' => 'Memastikan lampiran dapat tersimpan dan diunduh kembali.',
        'file_path' => UploadedFile::fake()->create($filename, 128, $mime),
    ])->assertRedirect(route('umum.surat.index'));

    $surat = Surat::where('user_id', $user->id)->firstOrFail();
    Storage::disk('public')->assertExists($surat->file_path);
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
