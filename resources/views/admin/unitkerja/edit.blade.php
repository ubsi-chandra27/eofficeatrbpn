@extends('layouts.admin')
@section('title','Edit Unit Kerja')
@section('content')
<div class="page-header fade-up"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Unit Kerja</h2><p class="text-muted mb-0">Perbarui identitas unit {{ $unitKerja->nama }}.</p></div><a href="{{ route('admin.unit.kerja.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<form method="POST" action="{{ route('admin.unit.kerja.update',$unitKerja->id) }}" class="unit-form fade-up">@csrf @method('PUT') @include('admin.unitkerja._form',['editing'=>true])</form>
@endsection
