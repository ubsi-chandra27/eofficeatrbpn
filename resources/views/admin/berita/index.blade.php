@extends('layouts.admin')
@section('title', 'Manajemen Berita & Pengumuman')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-newspaper text-primary me-2"></i>Berita & Pengumuman</h2><p class="text-muted mb-0">Kelola konten berita dan pengumuman yang ditampilkan di dashboard umum.</p></div><a href="{{ route('admin.berita.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Berita</a></div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal Publikasi</th><th>Penulis</th><th width="180">Aksi</th></tr></thead>
            <tbody>
            @forelse($berita as $item)
                <tr>
                    <td><strong>{{ $item->judul }}</strong><br><small class="text-muted">{{ $item->excerpt }}</small></td>
                    <td><span class="badge bg-{{ $item->kategori==='berita'?'info':'warning' }}">{{ ucfirst($item->kategori) }}</span></td>
                    <td>
                        @if($item->is_published && $item->published_at && $item->published_at <= now())
                            <span class="badge bg-success">Dipublikasikan</span>
                        @elseif($item->is_published)
                            <span class="badge bg-secondary">Dijadwalkan</span>
                        @else
                            <span class="badge bg-danger">Draft</span>
                        @endif
                    </td>
                    <td>{{ $item->published_at ? $item->published_at->format('d M Y H:i') : '-' }}</td>
                    <td>{{ $item->author?->name ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.berita.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.berita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="text-center py-4 text-muted">Belum ada berita atau pengumuman.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0">{{ $berita->links() }}</div>
</div>
@endsection
