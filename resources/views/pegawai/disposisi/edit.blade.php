@extends('layouts.pegawai')
@section('title','Edit Disposisi')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Disposisi</h2><p class="text-muted mb-0">Perbarui disposisi selama belum dibaca penerima.</p></div><a href="{{ route('pegawai.disposisi.sent.show',$disposisi) }}" class="btn btn-light border">Kembali</a></div>
<form method="POST" action="{{ route('pegawai.disposisi.update',$disposisi) }}" class="disposition-form">@csrf @method('PUT') @include('pegawai.disposisi._form')</form>
@endsection
