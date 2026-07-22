@extends('layouts.umum')
@section('title','Perbaiki Pengajuan')
@section('content')
<div class="container-fluid"><div class="public-page-header d-flex justify-content-between align-items-center"><div><h3><i class="bi bi-pencil-square text-primary me-2"></i>Perbaiki Pengajuan</h3><p>Perbarui informasi sesuai catatan admin, lalu kirim kembali.</p></div><a href="{{ route('umum.surat.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form action="{{ route('umum.surat.update',$surat->id) }}" method="POST" enctype="multipart/form-data" class="public-letter-form">@csrf @method('PUT') @include('umum.surat._form',['editing'=>true])</form>
</div>
@endsection
