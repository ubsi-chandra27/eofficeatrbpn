@extends('layouts.pegawai')
@section('title', 'Disposisi')

@section('content')
<div class="container-fluid employee-page">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-send-check-fill text-primary me-2"></i>Disposisi Saya</h2>
            <p class="text-muted mb-0">Baca instruksi, prioritaskan tugas penting, dan tandai pekerjaan yang selesai.</p>
        </div>
    </div>

    <div class="metric-grid">
        @foreach([['Total', $stats['total'], 'bi-files', 'primary'], ['Belum Dibaca', $stats['belum'], 'bi-envelope-exclamation', 'danger'], ['Sudah Dibaca', $stats['dibaca'], 'bi-envelope-open', 'warning'], ['Selesai', $stats['selesai'], 'bi-check-circle', 'success']] as [$label, $value, $icon, $color])
            <div class="metric-card">
                <span class="metric-icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi {{ $icon }}"></i></span>
                <div><strong>{{ $value }}</strong><small>{{ $label }}</small></div>
            </div>
        @endforeach
    </div>

    <div class="data-card">
        <form method="GET" class="filter-bar">
            <div class="search-control"><i class="bi bi-search"></i><input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nomor surat atau perihal"></div>
            <select name="status">
                <option value="">Semua status</option>
                @foreach(['Belum Dibaca', 'Sudah Dibaca', 'Selesai'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            @if(request()->hasAny(['keyword', 'status']))
                <a href="{{ route('pegawai.disposisi.index') }}" class="btn btn-outline-secondary" aria-label="Hapus filter"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table compact-table mb-0">
                <thead><tr><th>No</th><th>Surat</th><th>Instruksi</th><th>Pengirim</th><th>Prioritas</th><th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($disposisi as $item)
                        @php
                            $induk = $item->disposisi;
                            $surat = $induk?->surat;
                        @endphp
                        <tr class="{{ $item->status === 'Belum Dibaca' ? 'table-warning' : '' }}">
                            <td>{{ $disposisi->firstItem() + $loop->index }}</td>
                            <td><span class="cell-title">{{ $surat?->nomor_surat ?? '-' }}</span><span class="cell-meta">{{ $surat?->perihal ?? 'Surat tidak tersedia' }}</span></td>
                            <td><span title="{{ $induk?->catatan }}">{{ Str::limit($induk?->catatan, 55) }}</span></td>
                            <td>{{ $induk?->pengirim?->name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $induk?->prioritas === 'Tinggi' ? 'danger' : ($induk?->prioritas === 'Sedang' ? 'warning' : 'secondary') }}">{{ $induk?->prioritas ?? '-' }}</span></td>
                            <td>{{ $induk?->tanggal_disposisi?->format('d M Y') }}</td>
                            <td><span class="badge rounded-pill bg-{{ $item->status === 'Selesai' ? 'success' : ($item->status === 'Belum Dibaca' ? 'danger' : 'warning') }}">{{ $item->status }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('pegawai.disposisi.show', $item->id) }}" class="btn btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('pegawai.disposisi.cetak', $item->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak"><i class="bi bi-printer"></i></a>
                                    @if($item->status !== 'Selesai')
                                        <form action="{{ route('pegawai.disposisi.selesai', $item->id) }}" method="POST" onsubmit="return confirm('Tandai disposisi selesai?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-outline-success" title="Selesai"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox"></i><h5>Belum ada disposisi</h5><p>Disposisi yang ditujukan kepada Anda akan tampil di sini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="data-footer"><span>{{ $disposisi->firstItem() ?? 0 }}&ndash;{{ $disposisi->lastItem() ?? 0 }} dari {{ $disposisi->total() }} disposisi</span>{{ $disposisi->links() }}</div>
    </div>
</div>
@endsection
