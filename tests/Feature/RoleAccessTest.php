<?php

use App\Models\Pegawai;
use App\Models\User;

it('memisahkan akses dashboard untuk ketiga role', function () {
    $admin = User::factory()->create(['role' => 'admin', 'nip' => 'ADM-ROLE-001']);
    $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'nip' => 'PEG-ROLE-001']);
    Pegawai::create([
        'user_id' => $pegawaiUser->id,
        'nip' => $pegawaiUser->nip,
        'nama' => $pegawaiUser->name,
        'email' => $pegawaiUser->email,
    ]);
    $umum = User::factory()->create(['role' => 'umum']);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('pegawai.dashboard'))->assertForbidden();
    $this->actingAs($admin)->get(route('umum.dashboard'))->assertForbidden();

    $this->actingAs($pegawaiUser)->get(route('pegawai.dashboard'))->assertOk();
    $this->actingAs($pegawaiUser)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($pegawaiUser)->get(route('umum.dashboard'))->assertForbidden();

    $this->actingAs($umum)->get(route('umum.dashboard'))->assertOk();
    $this->actingAs($umum)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($umum)->get(route('pegawai.dashboard'))->assertForbidden();
});

it('menyediakan health check untuk monitoring hosting', function () {
    $this->get('/up')->assertOk();
});
