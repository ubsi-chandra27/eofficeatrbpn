@extends('layouts.umum')

@section('title', 'Profil Saya')

@section('content')
<div class="container py-3">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:24px">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div><h4 class="mb-1">{{ auth()->user()->name }}</h4><p class="text-muted mb-0">Akun masyarakat umum</p></div>
            </div>
            <dl class="row mb-0"><dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ auth()->user()->name }}</dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ auth()->user()->email }}</dd></dl>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i> Ubah Profil</a>
        </div>
    </div>
</div>
@endsection
