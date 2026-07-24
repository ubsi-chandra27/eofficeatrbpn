<?php

use App\Models\Surat;
use App\Models\User;
use App\Notifications\SystemNotification;

it('menampilkan notifikasi real untuk admin saat ada pengajuan umum baru', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $umum = User::factory()->create(['role' => 'umum', 'name' => 'Maria Anna']);

    $this->actingAs($umum)->post(route('umum.surat.store'), [
        'kategori_pengajuan' => 'Permohonan Informasi',
        'nomor_kontak' => '081234567890',
        'asal_instansi' => 'Perorangan',
        'perihal' => 'Permohonan informasi layanan',
        'deskripsi' => 'Mohon informasi alur layanan administrasi digital.',
    ])->assertRedirect(route('umum.surat.index'));

    expect($admin->fresh()->unreadNotifications()->count())->toBe(1);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Pengajuan umum baru')
        ->assertSee('belum dibaca');
});

it('pemilik surat menerima notifikasi saat surat masuk disetujui admin dan bisa dibuka', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $umum = User::factory()->create(['role' => 'umum']);
    $surat = Surat::create([
        'user_id' => $umum->id,
        'jenis_surat' => 'masuk',
        'nomor_surat' => 'UMUM-NOTIF-001',
        'tanggal_surat' => now()->toDateString(),
        'perihal' => 'Pengujian notifikasi persetujuan',
        'asal_surat' => $umum->name,
        'tujuan_surat' => 'Administrasi Umum',
        'metode' => 'Sistem',
        'status' => 'diajukan',
    ]);

    $this->actingAs($admin)->post(route('admin.surat.masuk.setujui', $surat), [
        'catatan_admin' => 'Berkas lengkap.',
    ])->assertSessionHas('success');

    $notification = $umum->fresh()->unreadNotifications()->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data['title'])->toBe('Surat disetujui');

    $this->actingAs($umum)->get(route('notifications.open', $notification->id))
        ->assertRedirect(route('umum.surat.show', $surat->id));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('pengguna dapat menandai semua notifikasi sebagai sudah dibaca', function () {
    $user = User::factory()->create(['role' => 'pegawai']);
    $user->notify(new SystemNotification(
        'Disposisi baru',
        'Anda menerima disposisi baru.',
        route('pegawai.dashboard'),
        'info',
        'bi-send-fill'
    ));

    expect($user->fresh()->unreadNotifications()->count())->toBe(1);

    $this->actingAs($user)->patch(route('notifications.read-all'))
        ->assertSessionHas('success');

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});
