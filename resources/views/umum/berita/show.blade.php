@extends('layouts.umum')
@section('title', $berita->judul)
@section('content')
<div class="container py-4">
    <a href="{{ route('umum.berita.index') }}" class="btn btn-outline-secondary rounded-pill mb-3"><i class="bi bi-arrow-left me-1"></i>Kembali ke Berita</a>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex gap-2 mb-3">
                <span class="badge bg-{{ $berita->kategori==='berita'?'info':'warning' }}">{{ ucfirst($berita->kategori) }}</span>
                <span class="text-muted small">{{ $berita->published_at->format('d M Y H:i') }}</span>
            </div>
            <h1 class="fw-bold mb-3">{{ $berita->judul }}</h1>
            <p class="text-muted mb-4">Penulis: <strong>{{ $berita->author?->name ?? '-' }}</strong></p>
            <hr>
            <div class="mt-3">{!! nl2br(e($berita->isi)) !!}</div>
            @if($berita->file_path)
                <div class="mt-4">
                    <h6>Lampiran</h6>
                    <a href="{{ asset('storage/'.$berita->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Unduh Lampiran</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
