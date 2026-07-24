@extends('layouts.admin')
@section('title', 'Detail Berita')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-newspaper text-primary me-2"></i>{{ $berita->judul }}</h2><p class="text-muted mb-0">Detail konten {{ $berita->kategori }}.</p></div><div><a href="{{ route('admin.berita.edit', $berita->id) }}" class="btn btn-warning text-white me-2"><i class="bi bi-pencil me-1"></i>Edit</a><a href="{{ route('admin.berita.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div></div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex gap-2 mb-3">
            <span class="badge bg-{{ $berita->kategori==='berita'?'info':'warning' }}">{{ ucfirst($berita->kategori) }}</span>
            @if($berita->is_published && $berita->published_at && $berita->published_at <= now())<span class="badge bg-success">Dipublikasikan</span>@elseif($berita->is_published)<span class="badge bg-secondary">Dijadwalkan</span>@else<span class="badge bg-danger">Draft</span>@endif
        </div>
        <p class="text-muted mb-1">Penulis: <strong>{{ $berita->author?->name ?? '-' }}</strong> | Tanggal: {{ $berita->published_at ? $berita->published_at->format('d M Y H:i') : '-' }}</p>
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
@endsection
