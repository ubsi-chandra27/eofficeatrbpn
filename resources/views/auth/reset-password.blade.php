@extends('layouts.auth')
@section('title','Reset Password')
@section('content')
<div class="auth-heading"><h1>Reset Password</h1><p>Buat password baru untuk mengamankan akun Anda.</p></div>
<form method="POST" action="{{ route('password.store') }}">@csrf<input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="mb-3"><label class="form-label" for="email">Email</label><div class="auth-input"><i class="bi bi-envelope"></i><input id="email" type="email" name="email" value="{{ old('email',$request->email) }}" class="form-control @error('email') is-invalid @enderror" required autofocus></div>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label" for="password">Password Baru</label><div class="auth-input"><i class="bi bi-lock"></i><input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required><button type="button" class="password-toggle" data-toggle-password="#password"><i class="bi bi-eye"></i></button></div>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-4"><label class="form-label" for="password_confirmation">Konfirmasi Password</label><div class="auth-input"><i class="bi bi-shield-check"></i><input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required><button type="button" class="password-toggle" data-toggle-password="#password_confirmation"><i class="bi bi-eye"></i></button></div></div>
    <button class="btn btn-primary btn-auth w-100"><i class="bi bi-shield-lock me-2"></i>Simpan Password Baru</button>
</form>
@endsection
@push('scripts')<script>document.addEventListener('DOMContentLoaded',()=>document.querySelectorAll('[data-toggle-password]').forEach(button=>button.addEventListener('click',()=>{const field=document.querySelector(button.dataset.togglePassword),visible=field.type==='text';field.type=visible?'password':'text';button.querySelector('i').className=`bi ${visible?'bi-eye':'bi-eye-slash'}`;})));</script>@endpush
