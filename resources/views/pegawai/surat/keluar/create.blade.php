@extends('layouts.pegawai')
@section('title','Tambah Surat Keluar')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-send-plus-fill text-primary me-2"></i>Tambah Surat Keluar</h2><p class="text-muted mb-0">Buat surat keluar dan ajukan kepada Admin.</p></div><a href="{{ route('pegawai.surat-keluar.index') }}" class="btn btn-light border">Kembali</a></div>
<form method="POST" action="{{ route('pegawai.surat-keluar.store') }}" enctype="multipart/form-data" class="outgoing-form">@csrf @include('pegawai.surat.keluar._form')</form>
@endsection
