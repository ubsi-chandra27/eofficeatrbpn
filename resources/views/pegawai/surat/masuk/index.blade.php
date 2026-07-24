@extends('layouts.pegawai')

@section('title', 'Surat Masuk')

@section('content')
<div class="container-fluid employee-page">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-envelope-arrow-down-fill text-primary me-2"></i>Surat Masuk</h2>
            <p class="text-muted mb-0">Catat surat, pantau approval Admin, dan kelola perbaikannya.</p>
        </div>
        <a href="{{ route('pegawai.surat-masuk.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Catat Surat Masuk
        </a>
    </div>

    <div class="metric-grid incoming-metric-grid">
        @foreach([
            ['Total', $stats['total'], 'bi-files', 'primary'],
            ['Draft', $stats['draft'], 'bi-file-earmark', 'secondary'],
            ['Menunggu Verifikasi', $stats['menunggu_verifikasi'], 'bi-hourglass-split', 'warning'],
            ['Disetujui Admin', $stats['disetujui'], 'bi-shield-check', 'success'],
            ['Perlu Perbaikan', $stats['perbaikan'], 'bi-exclamation-triangle', 'danger'],
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
        <form method="GET" class="filter-bar incoming-filter">
            <div class="search-control">
                <i class="bi bi-search"></i>
                <input
                    name="keyword"
                    value="{{ request('keyword') }}"
                    placeholder="Cari nomor, agenda, perihal, asal, atau tujuan surat">
            </div>

            <select name="status">
                <option value="">Semua status</option>
                @foreach([
                    'draft' => 'Draft / Belum Dikirim',
                    'diajukan' => 'Menunggu Verifikasi Admin',
                    'menunggu' => 'Menunggu',
                    'diverifikasi' => 'Disetujui Admin',
                    'dikembalikan' => 'Perlu Perbaikan',
                    'ditolak' => 'Ditolak',
                    'diproses' => 'Sedang Diproses',
                    'diteruskan_ke_pimpinan' => 'Diteruskan ke Pimpinan',
                    'selesai' => 'Selesai',
                    'diarsipkan' => 'Diarsipkan',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>

            @if(request()->hasAny(['keyword', 'status']))
                <a href="{{ route('pegawai.surat-masuk.index') }}" class="btn btn-outline-secondary" title="Hapus filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table compact-table incoming-table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Agenda</th>
                        <th>Nomor Surat</th>
                        <th>Perihal</th>
                        <th>Asal & Tujuan</th>
                        <th>Tanggal</th>
                        <th>Lampiran</th>
                        <th>Status Verifikasi</th>
                        <th>Catatan Admin</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratMasuk as $surat)
                        <tr>
                            <td>{{ $suratMasuk->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="cell-title">{{ $surat->nomor_agenda ?: '-' }}</span>
                                <span class="cell-meta">{{ $surat->metode ?: 'Sistem' }}</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $surat->nomor_surat }}</span>
                                @if($surat->is_priority)
                                    <span class="priority-chip"><i class="bi bi-star-fill"></i>Prioritas</span>
                                @endif
                            </td>
                            <td>
                                <span class="cell-title">{{ $surat->perihal }}</span>
                                <span class="cell-meta">{{ Str::limit($surat->deskripsi ?: 'Tidak ada deskripsi.', 70) }}</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $surat->asal_surat ?: '-' }}</span>
                                <span class="cell-meta"><i class="bi bi-arrow-right me-1"></i>{{ $surat->tujuan_surat ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="cell-title">{{ $surat->tanggal_surat?->translatedFormat('d M Y') ?? '-' }}</span>
                                <span class="cell-meta">Diubah {{ $surat->updated_at?->diffForHumans() ?? '-' }}</span>
                            </td>
                            <td>
                                @if($surat->file_path)
                                    <a href="{{ route('surat.lampiran', $surat) }}" class="attachment-chip" title="Buka lampiran">
                                        <i class="bi bi-paperclip"></i>{{ strtoupper(pathinfo($surat->file_path, PATHINFO_EXTENSION)) }}
                                    </a>
                                @else
                                    <span class="cell-meta">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $surat->status_badge }}">{{ $surat->status_label }}</span>
                                <span class="cell-meta {{ in_array($surat->status, ['dikembalikan', 'ditolak'], true) ? 'text-danger' : 'text-muted' }}">
                                    @switch($surat->status)
                                        @case('draft')
                                            Belum dikirim ke Admin
                                            @break
                                        @case('diajukan')
                                        @case('menunggu')
                                            Menunggu approval Admin
                                            @break
                                        @case('diverifikasi')
                                            Telah disetujui Admin
                                            @break
                                        @case('dikembalikan')
                                            Dikembalikan untuk diperbaiki
                                            @break
                                        @case('ditolak')
                                            Ditolak oleh Admin
                                            @break
                                        @case('diproses')
                                            Sudah diverifikasi dan diproses
                                            @break
                                        @case('diteruskan_ke_pimpinan')
                                            Sudah diteruskan oleh Admin
                                            @break
                                        @case('selesai')
                                            Proses telah selesai
                                            @break
                                        @case('diarsipkan')
                                            Masuk arsip
                                            @break
                                        @default
                                            {{ $surat->status_label }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                @if($surat->catatan_admin)
                                    <span class="note-chip" title="{{ $surat->catatan_admin }}">
                                        {{ Str::limit($surat->catatan_admin, 55) }}
                                    </span>
                                @else
                                    <span class="cell-meta">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions justify-content-end">
                                    <a href="{{ route('pegawai.surat-masuk.show', $surat) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(in_array($surat->status, ['draft', 'dikembalikan'], true))
                                        <a href="{{ route('pegawai.surat-masuk.edit', $surat) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('pegawai.surat-masuk.kirim', $surat) }}" onsubmit="return confirm('Kirim surat ke Admin untuk diverifikasi?')">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-outline-success" title="Kirim ke Admin">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('pegawai.surat-masuk.destroy', $surat) }}" onsubmit="return confirm('Pindahkan surat ini ke sampah?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h5>{{ request()->hasAny(['keyword', 'status']) ? 'Surat masuk tidak ditemukan' : 'Belum ada surat masuk' }}</h5>
                                    <p>{{ request()->hasAny(['keyword', 'status']) ? 'Ubah atau hapus filter untuk melihat data lainnya.' : 'Catat surat baru agar data tampil di tabel ini.' }}</p>
                                    @if(request()->hasAny(['keyword', 'status']))
                                        <a href="{{ route('pegawai.surat-masuk.index') }}" class="btn btn-outline-primary btn-sm">Hapus Filter</a>
                                    @else
                                        <a href="{{ route('pegawai.surat-masuk.create') }}" class="btn btn-primary btn-sm">Catat Surat Masuk</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-footer">
            <span>{{ $suratMasuk->firstItem() ?? 0 }}&ndash;{{ $suratMasuk->lastItem() ?? 0 }} dari {{ $suratMasuk->total() }} surat</span>
            {{ $suratMasuk->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .incoming-metric-grid{grid-template-columns:repeat(5,minmax(0,1fr))}
    .incoming-filter{grid-template-columns:minmax(280px,1fr) 250px auto auto}
    .incoming-table{min-width:1320px}
    .attachment-chip,.priority-chip,.note-chip{display:inline-flex;align-items:center;gap:5px;border-radius:9px;font-size:12px;font-weight:700}
    .attachment-chip{padding:6px 9px;background:#eaf3fb;color:#0f4c81;text-decoration:none}
    .priority-chip{margin-top:5px;padding:4px 7px;background:#fee2e2;color:#b91c1c}
    .note-chip{max-width:220px;padding:6px 9px;background:#fff1f2;color:#be123c}
    @media(max-width:1200px){.incoming-metric-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:1000px){.incoming-filter{grid-template-columns:1fr 1fr}.incoming-filter .search-control{grid-column:1/-1}}
    @media(max-width:700px){.incoming-metric-grid,.incoming-filter{grid-template-columns:1fr}.incoming-filter .search-control{grid-column:auto}}
</style>
@endpush
