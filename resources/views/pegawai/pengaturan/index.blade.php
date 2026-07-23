@extends('layouts.pegawai')

@section('title', 'Pengaturan Pegawai')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Pengaturan Akun</h3>
        <p class="text-muted mb-0">Kelola identitas, keamanan, dan preferensi akun Anda.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                <i class="bi bi-person-vcard fs-2 text-primary"></i>
                <h5 class="mt-3">Profil Pegawai</h5>
                <p class="text-muted">Perbarui nama, email, nomor telepon, alamat, dan foto profil.</p>
                <a href="{{ route('pegawai.profile.index') }}" class="btn btn-primary">Buka Profil</a>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
                <i class="bi bi-shield-lock fs-2 text-success"></i>
                <h5 class="mt-3">Keamanan</h5>
                <p class="text-muted">Gunakan kata sandi yang kuat dan jangan membagikannya kepada pihak lain.</p>
                <a href="{{ route('pegawai.profile.password') }}" class="btn btn-outline-success">Ubah Kata Sandi</a>
            </div></div>
        </div>
    </div>
</div>
@endsection
