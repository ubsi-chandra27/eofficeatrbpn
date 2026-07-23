@extends('layouts.pegawai')
@section('title', 'Disposisi Terkirim')

@section('content')
<div class="container-fluid employee-page">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-send-check-fill text-primary me-2"></i>Disposisi Terkirim</h2>
            <p class="text-muted mb-0">Pantau instruksi disposisi yang Anda kirim kepada pegawai lain.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('pegawai.disposisi.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Disposisi Masuk</a>
            <a href="{{ route('pegawai.disposisi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Buat Disposisi</a>
        </div>
    </div>

    <div class="metric-grid">
        @foreach([
            ['Total Terkirim', $stats['total'], 'bi-send', 'primary'],
            ['Belum Dibaca', $stats['belum'], 'bi-envelope-exclamation', 'danger'],
            ['Sudah Dibaca', $stats['dibaca'], 'bi-envelope-open', 'warning'],
            ['Selesai', $stats['selesai'], 'bi-check-circle', 'success'],
        ] as [$label, $value, $icon, $color])
            <div class="metric-card">
                <span class="metric-icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi {{ $icon }}"></i></span>
                <div><strong>{{ $value }}</strong><small>{{ $label }}</small></div>
            </div>
        @endforeach
    </div>

    <div class="data-card">
        <form method="GET" class="filter-bar">
            <div class="search-control">
                <i class="bi bi-search"></i>
                <input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nomor surat, perihal, instruksi, atau penerima">
            </div>
            <select name="status">
                <option value="">Semua status</option>
                @foreach(['Belum Dibaca', 'Sudah Dibaca', 'Selesai'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="prioritas">
                <option value="">Semua prioritas</option>
                @foreach(['Tinggi', 'Sedang', 'Rendah'] as $prioritas)
                    <option value="{{ $prioritas }}" @selected(request('prioritas') === $prioritas)>{{ $prioritas }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            @if(request()->hasAny(['keyword', 'status', 'prioritas']))
                <a href="{{ route('pegawai.disposisi.terkirim') }}" class="btn btn-outline-secondary" aria-label="Hapus filter"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table compact-table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Surat</th>
                        <th>Instruksi</th>
                        <th>Penerima & Status</th>
                        <th>Prioritas</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dikirim as $item)
                        @php($editable = $item->is_editable)
                        <tr>
                            <td>{{ $dikirim->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="cell-title">{{ $item->surat?->nomor_surat ?? '-' }}</span>
                                <span class="cell-meta">{{ $item->surat?->perihal ?? 'Surat tidak tersedia' }}</span>
                            </td>
                            <td><span title="{{ $item->catatan }}">{{ Str::limit($item->catatan, 70) }}</span></td>
                            <td>
                                @foreach($item->tujuans as $tujuan)
                                    <span class="recipient-status">
                                        <strong>{{ $tujuan->pegawai?->nama ?? '-' }}</strong>
                                        <small class="badge rounded-pill bg-{{ $tujuan->status === 'Selesai' ? 'success' : ($tujuan->status === 'Belum Dibaca' ? 'danger' : 'warning') }}">{{ $tujuan->status }}</small>
                                    </span>
                                @endforeach
                            </td>
                            <td><span class="badge bg-{{ $item->prioritas === 'Tinggi' ? 'danger' : ($item->prioritas === 'Sedang' ? 'warning' : 'secondary') }}">{{ $item->prioritas }}</span></td>
                            <td>{{ $item->tanggal_disposisi?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <div class="row-actions justify-content-end">
                                    <a href="{{ route('pegawai.disposisi.sent.show', $item) }}" class="btn btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    @if($editable)
                                        <a href="{{ route('pegawai.disposisi.edit', $item) }}" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route('pegawai.disposisi.destroy', $item) }}" onsubmit="return confirm('Hapus disposisi terkirim ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @else
                                        <span class="btn btn-light text-muted" title="Sudah dibaca dan terkunci"><i class="bi bi-lock"></i></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-send"></i>
                                    <h5>Belum ada disposisi terkirim</h5>
                                    <p>Disposisi yang Anda kirim kepada pegawai lain akan tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="data-footer">
            <span>{{ $dikirim->firstItem() ?? 0 }}&ndash;{{ $dikirim->lastItem() ?? 0 }} dari {{ $dikirim->total() }} disposisi</span>
            {{ $dikirim->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .recipient-status{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .recipient-status+.recipient-status{margin-top:6px}
    .recipient-status strong{font-size:14px;color:#1f2937}
    .recipient-status small{font-size:12px}
</style>
@endpush
