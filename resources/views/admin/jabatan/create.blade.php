@extends('layouts.admin')
@section('title', 'Tambah Jabatan')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-briefcase-fill text-primary me-2"></i>Tambah Jabatan</h2><p class="text-muted mb-0">Tambahkan jabatan untuk struktur dan penempatan pegawai.</p></div><a href="{{ route('admin.jabatan.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.jabatan.store') }}" class="position-form fade-up">@csrf @include('admin.jabatan._form', ['editing'=>false])</form>
@endsection
