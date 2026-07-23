@extends('layouts.pegawai')
@section('title','Buat Disposisi')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-send-plus-fill text-primary me-2"></i>Buat Disposisi</h2><p class="text-muted mb-0">Teruskan instruksi terkait surat kepada pegawai lain.</p></div><a href="{{ route('pegawai.disposisi.index') }}" class="btn btn-light border">Kembali</a></div>
<form method="POST" action="{{ route('pegawai.disposisi.store') }}" class="disposition-form">@csrf @include('pegawai.disposisi._form')</form>
@endsection
