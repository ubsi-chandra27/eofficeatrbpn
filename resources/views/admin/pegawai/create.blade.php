@extends('layouts.admin')
@section('title','Tambah Pegawai')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Pegawai</h2><p class="text-muted mb-0">Tambahkan profil pegawai sekaligus akun login yang terhubung.</p></div><a href="{{ route('admin.pegawai.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.pegawai.store') }}" class="employee-form fade-up">@csrf @include('admin.pegawai._form')</form>
@endsection
@include('admin.pegawai._styles')
