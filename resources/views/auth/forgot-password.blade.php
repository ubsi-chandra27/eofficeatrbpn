@extends('layouts.auth')
@section('title','Lupa Password')
@section('content')
<div class="auth-heading"><h1>Lupa Password</h1><p>Masukkan email akun untuk menerima tautan pengaturan ulang password.</p></div>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('password.email') }}">@csrf
    <div class="mb-4"><label class="form-label" for="email">Email</label><div class="auth-input"><i class="bi bi-envelope"></i><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required autofocus></div>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <button class="btn btn-primary btn-auth w-100" type="submit"><i class="bi bi-send me-2"></i>Kirim Tautan Reset</button>
</form>
<div class="public-note mt-4"><i class="bi bi-info-circle-fill"></i><span>Jika tidak dapat mengakses email, hubungi administrator sistem dan siapkan identitas akun Anda.</span></div>
<div class="auth-switch"><a href="{{ route('login') }}"><i class="bi bi-arrow-left me-1"></i>Kembali ke login</a></div>
@endsection
