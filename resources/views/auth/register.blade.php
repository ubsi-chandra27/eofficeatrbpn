@extends('layouts.auth')
@section('title','Daftar Akun')
@push('styles')
<style>
    .auth-page{align-items:start;padding-block:24px}
    .auth-shell{min-height:min(900px,calc(100vh - 48px))}
    .auth-content{padding:34px 50px;align-items:flex-start}
    .auth-content-inner{max-width:620px}
    .auth-heading h1{font-size:30px}
    .auth-heading p{margin-bottom:20px}
    #registerForm .role-grid{gap:12px}
    #registerForm .role-option{min-height:76px;flex-direction:row;font-size:15px;gap:9px}
    #registerForm .role-option i{font-size:19px}
    #registerForm .form-label{margin-bottom:6px}
    #registerForm .auth-input .form-control{height:47px}
    #registerForm .public-note{margin-block:16px!important;padding:11px 14px}
    @media(max-width:900px){.auth-page{padding:0}.auth-shell{min-height:100vh}.auth-content{padding:30px 28px}.auth-content-inner{max-width:680px}}
    @media(max-width:560px){.auth-content{padding:24px 20px}.auth-heading h1{font-size:26px}#registerForm .role-option{min-height:68px;flex-direction:column;gap:3px}.auth-heading p{margin-bottom:16px}}
</style>
@endpush
@section('content')
<div class="auth-heading"><h1>Daftar Akun</h1><p>Lengkapi identitas dan pilih jenis akun yang akan dibuat.</p></div>
@if($errors->any())<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i>Periksa kembali data pendaftaran.</div>@endif
<form method="POST" action="{{ route('register') }}" id="registerForm">@csrf
    <div class="mb-4"><label class="form-label">Jenis Akun</label><input type="hidden" name="role" id="registerRole" value="{{ old('role','umum') }}"><div class="role-grid">
        @foreach(($allowStaffRegistration ? ['admin'=>['bi-shield-lock','Admin'],'pegawai'=>['bi-person-badge','Pegawai']] : []) + ['umum'=>['bi-people','Umum']] as $role=>$meta)<button type="button" class="role-option" data-register-role="{{ $role }}"><i class="bi {{ $meta[0] }}"></i>{{ $meta[1] }}</button>@endforeach
    </div>@error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
    <div class="row g-3">
        <div class="col-md-6" id="nameGroup"><label class="form-label" for="name">Nama Lengkap</label><div class="auth-input"><i class="bi bi-person"></i><input id="name" name="name" value="{{ old('name') }}" maxlength="100" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap" autocomplete="name" required autofocus></div>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6" id="nipGroup"><label class="form-label" for="nip">NIP</label><div class="auth-input"><i class="bi bi-person-vcard"></i><input id="nip" name="nip" value="{{ old('nip') }}" maxlength="50" class="form-control @error('nip') is-invalid @enderror" placeholder="Masukkan NIP"></div>@error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label" for="email">Email</label><div class="auth-input"><i class="bi bi-envelope"></i><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" autocomplete="email" required></div>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="password">Password</label><div class="auth-input"><i class="bi bi-lock"></i><input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" autocomplete="new-password" required><button type="button" class="password-toggle" data-toggle-password="#password"><i class="bi bi-eye"></i></button></div>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="password_confirmation">Konfirmasi Password</label><div class="auth-input"><i class="bi bi-shield-check"></i><input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" autocomplete="new-password" required><button type="button" class="password-toggle" data-toggle-password="#password_confirmation"><i class="bi bi-eye"></i></button></div></div>
    </div>
    <div class="public-note my-4" id="roleNote"><i class="bi bi-info-circle-fill"></i><span></span></div>
    <button class="btn btn-primary btn-auth w-100" type="submit"><i class="bi bi-person-plus me-2"></i>Buat Akun</button>
</form>
<div class="auth-switch">Sudah memiliki akun? <a href="{{ route('login') }}">Kembali ke login</a></div>
@endsection
@push('scripts')<script>
document.addEventListener('DOMContentLoaded',()=>{
 const roleInput=document.getElementById('registerRole'),buttons=document.querySelectorAll('[data-register-role]'),nameGroup=document.getElementById('nameGroup'),nipGroup=document.getElementById('nipGroup'),nip=document.getElementById('nip'),note=document.querySelector('#roleNote span');
 const select=role=>{roleInput.value=role;buttons.forEach(button=>button.classList.toggle('active',button.dataset.registerRole===role));const internal=role!=='umum';nipGroup.hidden=!internal;nameGroup.classList.toggle('col-md-6',internal);nameGroup.classList.toggle('col-12',!internal);nip.required=internal;note.textContent=internal?'NIP digunakan sebagai identitas login. Email tetap disimpan untuk informasi akun dan pemulihan password.':'Akun Umum masuk menggunakan email dan password.';};
 buttons.forEach(button=>button.addEventListener('click',()=>select(button.dataset.registerRole)));select(roleInput.value||'umum');
 document.querySelectorAll('[data-toggle-password]').forEach(button=>button.addEventListener('click',()=>{const field=document.querySelector(button.dataset.togglePassword),visible=field.type==='text';field.type=visible?'password':'text';button.querySelector('i').className=`bi ${visible?'bi-eye':'bi-eye-slash'}`;}));
});
</script>@endpush
