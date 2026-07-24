@extends('layouts.umum')
@section('title', 'Berita & Pengumuman')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="fw-bold mb-1">Berita & Pengumuman</h2><p class="text-muted mb-0">Informasi terbaru dari {{ \App\Models\Setting::getValue('app_name', 'E-Office') }}</p></div>
        <a href="{{ route('umum.dashboard') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4"><a href="{{ route('umum.berita.index', ['kategori' => '']) }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100 {{ !$kategori ? 'border-primary' : '' }}"><div class="card-body text-center"><h5 class="mb-0">Semua</h5></div></div></a></div>
        <div class="col-md-4"><a href="{{ route('umum.berita.index', ['kategori' => 'berita']) }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100 {{ $kategori==='berita' ? 'border-primary' : '' }}"><div class="card-body text-center"><h5 class="mb-0">Berita</h5></div></div></a></div>
        <div class="col-md-4"><a href="{{ route('umum.berita.index', ['kategori' => 'pengumuman']) }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100 {{ $kategori==='pengumuman' ? 'border-primary' : '' }}"><div class="card-body text-center"><h5 class="mb-0">Pengumuman</h5></div></div></a></div>
    </div>
    @if($berita->isEmpty())
        <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-3"></i><h5>Belum ada berita</h5><p>Konten akan ditampilkan di sini setelah dipublikasikan oleh Admin.</p></div>
    @else
        <div class="row g-4">
            @foreach($berita as $item)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex gap-2 mb-2"><span class="badge bg-{{ $item->kategori==='berita'?'info':'warning' }}">{{ ucfirst($item->kategori) }}</span><span class="text-muted small">{{ $item->published_at->format('d M Y') }}</span></div>
                            <h5 class="card-title">{{ $item->judul }}</h5>
                            <p class="card-text text-muted flex-grow-1">{{ $item->excerpt }}</p>
                            <a href="{{ route('umum.berita.show', $item->id) }}" class="btn btn-outline-primary btn-sm mt-2">Baca selengkapnya <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $berita->links() }}</div>
    @endif
</div>
@endsection
