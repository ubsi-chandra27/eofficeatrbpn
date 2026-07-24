@extends('layouts.pegawai')

@section('title', 'Surat Keluar')

@section('content')
<div class="container-fluid employee-page">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-envelope-arrow-up-fill text-primary me-2"></i>Surat Keluar</h2>
            <p class="text-muted mb-0">Buat surat keluar, pantau verifikasi Admin, dan kelola perbaikannya.</p>
        </div>
        <a href="{{ route('pegawai.surat-keluar.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Surat Keluar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="metric-grid outgoing-metric-grid">
        @foreach([
            ['Total', $stats['total'], 'bi-files', 'primary'],
            ['Draft', $stats['draft'], 'bi-file-earmark', 'secondary'],
            ['Menunggu Admin', $stats['menunggu'], 'bi-hourglass-split', 'warning'],
            ['Perlu Perbaikan', $stats['perbaikan'], 'bi-exclamation-triangle', 'danger'],
            ['Selesai', $stats['selesai'], 'bi-check-circle', 'success'],
        ] as [$label, $value, $icon, $color])
            <div class="metric-card">
                <span class="metric-icon bg-{{ $color }}-subtle text-{{ $color }}">
                    <i class="bi {{ $icon }}"></i>
                </span>
                <div>
                    <strong>{{ $value }}</strong>
                    <small>{{ $label }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <div class="data-card">
        <form method="GET" class="filter-bar outgoing-filter">
            <div class="search-control">
                <i class="bi bi-search"></i>
                <input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nomor, kode, perihal, tujuan, atau penandatangan">
            </div>
            <select name="status">
                <option value="">Semua status</option>
                @foreach([
                    'draft' => 'Draft',
                    'diajukan' => 'Menunggu Admin',
                    'diproses' => 'Sedang Diproses',
                    'perbaikan' => 'Perlu Perbaikan',
                    'selesai' => 'Selesai',
                    'terkirim' => 'Terkirim',
                    'diarsipkan' => 'Diarsipkan',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            @if(request()->hasAny(['keyword', 'status']))
                <a href="{{ route('pegawai.surat-keluar.index') }}" class="btn btn-outline-secondary" title="Hapus filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table compact-table outgoing-table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor &amp; Kode</th>
                        <th>Perihal</th>
                        <th>Tujuan</th>
                        <th>Penandatangan</th>
                        <th>Tanggal</th>
                        <th>Lampiran</th>
                        <th>Status &amp; Catatan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surat as $item)
                        @php
                            $canEdit = in_array($item->status, ['draft', 'dikembalikan', 'Menunggu'], true);
                            $stage = match ($item->status) {
                                'draft' => 'Belum dikirim ke Admin',
                                'menunggu', 'diajukan' => 'Menunggu verifikasi Admin',
                                'diverifikasi' => 'Diverifikasi Admin',
                                'diproses' => 'Sedang diproses',
                                'diteruskan_ke_pimpinan' => 'Diteruskan ke pimpinan',
                                'dikembalikan', 'ditolak' => 'Perlu diperbaiki',
                                'terkirim', 'selesai', 'diarsipkan' => 'Proses selesai',
                                default => $item->status_label,
                            };
                            $fileExtension = $item->file_path ? strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) : null;
                        @endphp
                        <tr class="{{ in_array($item->status, ['dikembalikan', 'ditolak'], true) ? 'needs-revision' : '' }}">
                            <td>{{ $surat->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="cell-title">{{ $item->nomor_surat }}</span>
                                <span class="cell-meta">{{ $item->kode_surat ?: 'Tanpa kode' }}</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $item->perihal }}</span>
                                <span class="cell-meta">{{ Str::limit($item->deskripsi ?: 'Tidak ada deskripsi.', 70) }}</span>
                            </td>
                            <td>{{ $item->tujuan_surat ?: '-' }}</td>
                            <td>
                                <span class="cell-title">{{ $item->nama_pimpinan ?: '-' }}</span>
                                <span class="cell-meta">{{ $item->jabatanPimpinan?->nama ?? 'Jabatan belum tersedia' }}</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $item->tanggal_surat?->translatedFormat('d M Y') ?? '-' }}</span>
                                <span class="cell-meta">Diubah {{ $item->updated_at?->diffForHumans() ?? '-' }}</span>
                            </td>
                            <td>
                                @if($fileExtension)
                                    <a href="{{ route('surat.lampiran', $item) }}" class="attachment-chip" title="Buka lampiran">
                                        <i class="bi bi-paperclip"></i>{{ $fileExtension }}
                                    </a>
                                @else
                                    <span class="cell-meta">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                                <span class="cell-meta">{{ $stage }}</span>
                                @if($item->catatan_admin)
                                    <span class="note-chip" title="{{ $item->catatan_admin }}">{{ Str::limit($item->catatan_admin, 48) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions justify-content-end">
                                    <a href="{{ route('pegawai.surat-keluar.show', $item) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($canEdit)
                                        <a href="{{ route('pegawai.surat-keluar.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('pegawai.surat-keluar.kirim', $item) }}" onsubmit="return confirm('Kirim surat keluar ke Admin untuk diverifikasi?')">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-outline-success" title="Kirim ke Admin">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('pegawai.surat-keluar.destroy', $item) }}" onsubmit="return confirm('Pindahkan surat keluar ini ke sampah?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="btn btn-light text-muted" title="Surat sedang diproses dan terkunci">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-send"></i>
                                    <h5>{{ request()->hasAny(['keyword', 'status']) ? 'Surat keluar tidak ditemukan' : 'Belum ada surat keluar' }}</h5>
                                    <p>{{ request()->hasAny(['keyword', 'status']) ? 'Ubah atau hapus filter untuk melihat data lainnya.' : 'Buat surat keluar baru agar data tampil di tabel ini.' }}</p>
                                    @if(request()->hasAny(['keyword', 'status']))
                                        <a href="{{ route('pegawai.surat-keluar.index') }}" class="btn btn-outline-primary btn-sm">Hapus Filter</a>
                                    @else
                                        <a href="{{ route('pegawai.surat-keluar.create') }}" class="btn btn-primary btn-sm">Tambah Surat Keluar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-footer">
            <span>{{ $surat->firstItem() ?? 0 }}&ndash;{{ $surat->lastItem() ?? 0 }} dari {{ $surat->total() }} surat</span>
            {{ $surat->links() }}
        </div>
    </div>
</div>
@endsection
