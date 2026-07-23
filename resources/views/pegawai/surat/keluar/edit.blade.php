@extends('layouts.pegawai')
@section('title','Edit Surat Keluar')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Surat Keluar</h2><p class="text-muted mb-0">Perbarui draft atau surat yang dikembalikan Admin.</p></div><a href="{{ route('pegawai.surat-keluar.show',$surat) }}" class="btn btn-light border">Kembali</a></div>
<form method="POST" action="{{ route('pegawai.surat-keluar.update',$surat) }}" enctype="multipart/form-data" class="outgoing-form">@csrf @method('PUT') @include('pegawai.surat.keluar._form')</form>
@endsection
