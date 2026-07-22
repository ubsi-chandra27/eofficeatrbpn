@extends('layouts.admin')

@section('title', 'Tambah Surat Keluar')

@section('content')
<div class="page-header fade-up">
    <div>
        <h2><i class="bi bi-send-fill text-success me-2"></i>Tambah Surat Keluar</h2>
        <p class="text-muted mb-0">Siapkan dan registrasikan surat yang akan dikirim.</p>
    </div>
    <a href="{{ route('admin.surat.keluar.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
</div>

@if($errors->any())
    <div class="alert alert-danger shadow-sm"><strong>Periksa kembali data form.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.surat.keluar.store') }}" method="POST" enctype="multipart/form-data" class="admin-outgoing-form fade-up">
    @csrf
    @include('admin.surat.keluar._form', ['editing' => false])
</form>
@endsection
