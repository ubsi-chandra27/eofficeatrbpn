@extends('layouts.admin')

@section('title', 'Edit Surat Keluar')

@section('content')
<div class="page-header fade-up">
    <div>
        <h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Surat Keluar</h2>
        <p class="text-muted mb-0">Perbarui informasi dan tahapan surat {{ $surat->nomor_surat }}.</p>
    </div>
    <a href="{{ route('admin.surat.keluar.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
</div>

@if($errors->any())
    <div class="alert alert-danger shadow-sm"><strong>Periksa kembali data form.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.surat.keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data" class="admin-outgoing-form fade-up">
    @csrf
    @method('PUT')
    @include('admin.surat.keluar._form', ['editing' => true])
</form>
@endsection
