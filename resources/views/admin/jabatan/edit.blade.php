@extends('layouts.admin')
@section('title', 'Edit Jabatan')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Jabatan</h2><p class="text-muted mb-0">Perbarui identitas jabatan {{ $jabatan->nama }}.</p></div><a href="{{ route('admin.jabatan.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.jabatan.update',$jabatan->id) }}" class="position-form fade-up">@csrf @method('PUT') @include('admin.jabatan._form', ['editing'=>true])</form>
@endsection
