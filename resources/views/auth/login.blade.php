@extends('layouts.auth')
@section('title','Login')
@section('content')
<div class="auth-heading"><h1>Selamat Datang</h1><p>Masuk menggunakan akun dan jenis akses Anda.</p></div>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login') }}" id="loginForm">@csrf
    <div class="mb-4">
        <label class="form-label">Jenis Akun</label>
        <input type="hidden" name="login_as" id="selectedRole" value="{{ old('login_as','pegawai') }}">
        <div class="role-grid">
            @foreach(['admin'=>['bi-shield-lock','Admin'],'pegawai'=>['bi-person-badge','Pegawai'],'umum'=>['bi-people','Umum']] as $role=>$meta)
                <button type="button" class="role-option" data-role="{{ $role }}"><i class="bi {{ $meta[0] }}"></i>{{ $meta[1] }}</button>
            @endforeach
        </div>
        @error('login_as')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="identifier" id="identifierLabel">NIP</label>
        <div class="auth-input"><i class="bi bi-person-vcard" id="identifierIcon"></i><input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" class="form-control @error('identifier') is-invalid @enderror" placeholder="Masukkan NIP" autocomplete="username" required autofocus></div>
        @error('identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3"><label class="form-label" for="password">Password</label><div class="auth-input"><i class="bi bi-lock"></i><input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" class="password-toggle" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button></div>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="d-flex justify-content-between align-items-center mb-4"><label class="form-check"><input class="form-check-input" type="checkbox" name="remember" value="1" @checked(old('remember'))><span class="form-check-label">Ingat saya</span></label>@if(Route::has('password.request'))<a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold">Lupa password?</a>@endif</div>
    <button class="btn btn-primary btn-auth w-100" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</button>
</form>
<div class="auth-switch">Belum memiliki akun? <a href="{{ route('register') }}">Daftar akun</a></div>
@endsection
@push('scripts')<script>
document.addEventListener('DOMContentLoaded',()=>{
 const roleInput=document.getElementById('selectedRole'),buttons=document.querySelectorAll('[data-role]'),label=document.getElementById('identifierLabel'),field=document.getElementById('identifier'),icon=document.getElementById('identifierIcon');
 const select=role=>{roleInput.value=role;buttons.forEach(button=>button.classList.toggle('active',button.dataset.role===role));const umum=role==='umum';label.textContent=umum?'Email':'NIP';field.placeholder=umum?'nama@email.com':'Masukkan NIP';field.type=umum?'email':'text';icon.className=`bi ${umum?'bi-envelope':'bi-person-vcard'}`;};
 buttons.forEach(button=>button.addEventListener('click',()=>select(button.dataset.role)));select(roleInput.value||'pegawai');
 document.querySelectorAll('[data-toggle-password]').forEach(button=>button.addEventListener('click',()=>{const input=document.querySelector(button.dataset.togglePassword),visible=input.type==='text';input.type=visible?'password':'text';button.querySelector('i').className=`bi ${visible?'bi-eye':'bi-eye-slash'}`;}));
 document.getElementById('loginForm').addEventListener('submit',event=>{const button=event.currentTarget.querySelector('[type=submit]');button.disabled=true;button.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Memeriksa akun...';});
});
</script>@endpush
