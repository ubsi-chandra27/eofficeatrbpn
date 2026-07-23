@extends('layouts.admin')
@section('title','Edit Pegawai')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Pegawai</h2><p class="text-muted mb-0">Perbarui profil dan akun login {{ $pegawai->nama }}.</p></div><a href="{{ route('admin.pegawai.show',$pegawai) }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.pegawai.update',$pegawai) }}" class="employee-form fade-up">@csrf @method('PUT') @include('admin.pegawai._form')</form>
@endsection
@include('admin.pegawai._styles')
