@extends('layouts.umum')
@section('title','Pengajuan Layanan')
@section('content')
<div class="container-fluid"><div class="public-page-header d-flex justify-content-between align-items-center"><div><h3><i class="bi bi-send-plus-fill text-primary me-2"></i>Buat Pengajuan</h3><p>Sampaikan permohonan, dokumen, atau pengaduan kepada bagian administrasi.</p></div><a href="{{ route('umum.surat.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form action="{{ route('umum.surat.store') }}" method="POST" enctype="multipart/form-data" class="public-letter-form">@csrf @include('umum.surat._form',['editing'=>false])</form>
</div>
@endsection
