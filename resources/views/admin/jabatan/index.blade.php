@extends('layouts.admin')
@section('title', 'Data Jabatan')

@section('content')
<div class="page-header fade-up">
    <div><h2><i class="bi bi-briefcase-fill text-primary me-2"></i>Data Jabatan</h2><p class="text-muted mb-0">Kelola jabatan dan pantau penggunaannya oleh pegawai.</p></div>
    <a href="{{ route('admin.jabatan.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Jabatan</a>
</div>

<div class="position-stats mb-4 fade-up">
    @foreach([['Total Jabatan',$totalJabatan,'bi-briefcase-fill','primary'],['Digunakan',$jabatanTerpakai,'bi-people-fill','success'],['Belum Digunakan',$jabatanKosong,'bi-exclamation-circle','warning']] as [$label,$value,$icon,$color])
        <div class="position-stat"><div><h3>{{ $value }}</h3><span>{{ $label }}</span></div><i class="bi {{ $icon }} text-{{ $color }}"></i></div>
    @endforeach
</div>

<div class="position-card fade-up">
    <div class="position-toolbar">
        <div><h5>Daftar Jabatan</h5><small>{{ $jabatan->total() }} data sesuai pencarian</small></div>
        <form method="GET" action="{{ route('admin.jabatan.index') }}" class="position-search">
            <i class="bi bi-search"></i><input name="keyword" value="{{ request('keyword') }}" placeholder="Cari kode, nama, atau deskripsi...">
            @if(request('keyword'))<a href="{{ route('admin.jabatan.index') }}" title="Hapus pencarian"><i class="bi bi-x-lg"></i></a>@endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 position-table">
            <thead><tr><th width="65">No</th><th width="150">Kode</th><th>Nama Jabatan</th><th>Deskripsi</th><th width="135">Pegawai</th><th width="155">Aksi</th></tr></thead>
            <tbody>
            @forelse($jabatan as $item)
                <tr>
                    <td>{{ $jabatan->firstItem() + $loop->index }}</td>
                    <td><span class="code-badge">{{ $item->kode ?: '—' }}</span></td>
                    <td><strong>{{ $item->nama }}</strong></td>
                    <td><span class="description-text">{{ $item->deskripsi ?: 'Belum ada deskripsi' }}</span></td>
                    <td><span class="usage-badge {{ $item->pegawai_count ? 'used' : 'empty' }}"><i class="bi bi-people me-1"></i>{{ $item->pegawai_count }}</span></td>
                    <td><div class="position-actions">
                        <a href="{{ route('admin.jabatan.show', $item->id) }}" class="action-view" title="Detail"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.jabatan.edit', $item->id) }}" class="action-edit" title="Edit"><i class="bi bi-pencil"></i></a>
                        @if($item->pegawai_count === 0)
                            <form method="POST" action="{{ route('admin.jabatan.destroy', $item->id) }}" onsubmit="return confirm('Hapus jabatan {{ addslashes($item->nama) }}?')">@csrf @method('DELETE')<button class="action-delete" title="Hapus"><i class="bi bi-trash"></i></button></form>
                        @else
                            <button class="action-disabled" title="Masih digunakan oleh pegawai" disabled><i class="bi bi-lock"></i></button>
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><strong>{{ request('keyword') ? 'Jabatan tidak ditemukan' : 'Belum ada data jabatan' }}</strong><span>{{ request('keyword') ? 'Coba gunakan kata pencarian lainnya.' : 'Tambahkan jabatan pertama untuk mulai mengelola struktur pegawai.' }}</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="position-footer"><span>Menampilkan {{ $jabatan->firstItem() ?? 0 }}–{{ $jabatan->lastItem() ?? 0 }} dari {{ $jabatan->total() }} jabatan</span>{{ $jabatan->links() }}</div>
</div>
@endsection

@push('styles')<style>
.position-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.position-stat{background:#fff;border:1px solid #e5eaf0;border-radius:16px;padding:20px 22px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 8px 22px rgba(15,76,129,.05)}.position-stat h3{font-size:28px;font-weight:750;margin:0;color:#17233b}.position-stat span{font-size:13px;color:#718096}.position-stat>i{font-size:27px}.position-card{background:#fff;border:1px solid #e5eaf0;border-radius:18px;overflow:hidden;box-shadow:0 9px 26px rgba(15,76,129,.06)}.position-toolbar{padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid #e9eef3}.position-toolbar h5{margin:0;font-weight:700}.position-toolbar small{color:#8491a3}.position-search{position:relative;width:min(390px,100%)}.position-search>i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8}.position-search input{width:100%;height:46px;padding:10px 42px;border:1px solid #dbe2ea;border-radius:11px;outline:none}.position-search input:focus{border-color:#0f4c81;box-shadow:0 0 0 .18rem rgba(15,76,129,.1)}.position-search a{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#8491a3}.position-table{min-width:900px}.position-table th{background:#f8fafc;padding:14px 16px;color:#475569;font-size:12px;text-transform:uppercase;letter-spacing:.3px}.position-table td{padding:15px 16px;border-color:#edf1f5}.code-badge,.usage-badge{display:inline-flex;align-items:center;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:700}.code-badge{background:#edf6ff;color:#0f4c81}.usage-badge.used{background:#dcfce7;color:#166534}.usage-badge.empty{background:#f1f5f9;color:#64748b}.description-text{display:block;max-width:370px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.position-actions{display:flex;gap:7px}.position-actions form{margin:0}.position-actions a,.position-actions button{width:36px;height:36px;border:0;border-radius:9px;display:grid;place-items:center;text-decoration:none}.action-view{background:#e0f2fe;color:#0369a1}.action-edit{background:#fef3c7;color:#b45309}.action-delete{background:#fee2e2;color:#b91c1c}.action-disabled{background:#f1f5f9;color:#94a3b8}.position-footer{padding:17px 24px;display:flex;align-items:center;justify-content:space-between;color:#718096;font-size:13px;border-top:1px solid #edf1f5}.position-footer .pagination{margin:0}.empty-state{text-align:center;padding:58px 20px;color:#8491a3}.empty-state i,.empty-state strong,.empty-state span{display:block}.empty-state i{font-size:45px;margin-bottom:10px}.empty-state strong{color:#475569;margin-bottom:3px}
@media(max-width:768px){.position-stats{grid-template-columns:1fr}.position-toolbar,.position-footer{align-items:stretch;flex-direction:column}.position-search{width:100%}.position-footer{text-align:center}}
</style>@endpush
