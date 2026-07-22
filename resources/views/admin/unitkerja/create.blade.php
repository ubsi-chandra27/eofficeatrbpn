@extends('layouts.admin')
@section('title','Tambah Unit Kerja')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-building-add text-primary me-2"></i>Tambah Unit Kerja</h2><p class="text-muted mb-0">Tambahkan unit untuk struktur penempatan pegawai.</p></div><a href="{{ route('admin.unit.kerja.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.unit.kerja.store') }}" class="unit-form fade-up">@csrf @include('admin.unitkerja._form',['editing'=>false])</form>
@endsection
