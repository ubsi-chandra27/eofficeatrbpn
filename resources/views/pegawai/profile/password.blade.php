@extends('layouts.pegawai')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm mx-auto" style="max-width:720px">
        <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold">Ubah Kata Sandi</h3>
            <p class="text-muted">Gunakan minimal delapan karakter dan hindari kata sandi yang mudah ditebak.</p>
            <form method="POST" action="{{ route('profile.password.update') }}" id="password">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="current_password">Kata sandi saat ini</label>
                    <input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Kata sandi baru</label>
                    <input class="form-control @error('password', 'updatePassword') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="new-password">
                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Konfirmasi kata sandi baru</label>
                    <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button class="btn btn-primary"><i class="bi bi-shield-check me-1"></i>Simpan Kata Sandi</button>
            </form>
        </div>
    </div>
</div>
@endsection
