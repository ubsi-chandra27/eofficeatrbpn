@extends('layouts.admin')

@section('title','Surat Keluar')

@section('content')

<div class="outgoing-index-page">

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-envelope-arrow-up-fill text-success"></i>

            Surat Keluar

        </h2>

        <p class="text-muted mb-0">

            Kelola seluruh surat keluar pada sistem administrasi digital.

        </p>

    </div>

    <div>

        <a
            href="{{ route('admin.surat.keluar.create') }}"
            class="btn btn-success">

            <i class="bi bi-plus-circle me-2"></i>

            Tambah Surat

        </a>

    </div>

</div>

{{-- ===================================================== --}}
{{-- STATISTIK --}}
{{-- ===================================================== --}}

<div class="dashboard-grid mb-4">

    <div class="stat-card">

        <div>

            <h3>{{ isset($stats) ? $stats->sum() : 0 }}</h3>

            <p>Total Surat</p>

        </div>

        <div class="stat-icon bg-success-soft">

            <i class="bi bi-envelope-paper-fill"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>{{ $stats['draft'] ?? 0 }}</h3>

            <p>Draft</p>

        </div>

        <div class="stat-icon bg-warning-soft">

            <i class="bi bi-file-earmark-text-fill"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>{{ $stats['terkirim'] ?? 0 }}</h3>

            <p>Terkirim</p>

        </div>

        <div class="stat-icon bg-info-soft">

            <i class="bi bi-send-fill"></i>

        </div>

    </div>

    <div class="stat-card"><div><h3>{{ $stats['diarsipkan'] ?? 0 }}</h3><p>Diarsipkan</p></div>
        <div class="stat-icon bg-primary-soft">

            <i class="bi bi-archive-fill"></i>

        </div>

    </div>

</div>

{{-- ===================================================== --}}
{{-- TABLE --}}
{{-- ===================================================== --}}

<div class="table-card fade-up">

    <div class="table-header">

        <h4>

            Daftar Surat Keluar

        </h4>

        <span class="badge bg-success">

            {{ $surat->total() }} Surat

        </span>

    </div>

    <div class="table-toolbar">

        <form
            action="{{ route('admin.surat.keluar.index') }}"
            method="GET"
            class="outgoing-filters">

            <div class="outgoing-search">
                <i class="bi bi-search"></i>
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Cari nomor surat, perihal, atau tujuan...">
            </div>

            <select name="status" class="form-select status-filter" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['draft' => 'Draft', 'diajukan' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'diteruskan_ke_pimpinan' => 'Diteruskan ke Pimpinan', 'terkirim' => 'Terkirim', 'diarsipkan' => 'Diarsipkan'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-success">Filter</button>

        </form>

    </div>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th width="60">No</th>

                    <th>Surat</th>

                    <th>Tujuan</th>

                    <th>Tanggal</th>
                    <th>Status</th>

                    <th width="180">Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse($surat as $item)

                    <tr>

                        <td>

                            {{ $surat->firstItem() + $loop->index }}

                        </td>

                        {{-- ======================================= --}}
                        {{-- SURAT --}}
                        {{-- ======================================= --}}

                        <td>

                            <div class="table-avatar">

                                <div class="avatar bg-success">

                                    <i class="bi bi-envelope-paper-fill text-white"></i>

                                </div>

                                <div>

                                    <strong>

                                        {{ $item->nomor_surat }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $item->perihal }}

                                    </small>

                                    @if($item->is_priority)

                                        <br>

                                        <span class="badge bg-danger mt-1">

                                            <i class="bi bi-star-fill me-1"></i>

                                            Prioritas

                                        </span>

                                    @endif

                                </div>

                            </div>

                        </td>

                        {{-- ======================================= --}}
                        {{-- TUJUAN SURAT --}}
                        {{-- ======================================= --}}

                        <td>

                            <strong>

                                {{ $item->tujuan_surat ?? '-' }}

                            </strong>

                        </td>

                        {{-- ======================================= --}}
                        {{-- TANGGAL --}}
                        {{-- ======================================= --}}

                        <td>

                            @if($item->tanggal_surat)

                                {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d M Y') }}

                            @else

                                -

                            @endif

                        </td>

                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                        </td>


                        {{-- ======================================= --}}
                        {{-- AKSI --}}
                        {{-- ======================================= --}}

                        <td>

                            <div class="table-action">

                                <a
                                    href="{{ route('admin.surat.keluar.show',$item->id) }}"
                                    class="btn-view"
                                    title="Detail">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                @if($item->status === 'draft')
                                <a
                                    href="{{ route('admin.surat.keluar.edit',$item->id) }}"
                                    class="btn-edit"
                                    title="Edit">

                                    <i class="bi bi-pencil-square"></i>

                                </a>

                                <form
                                    action="{{ route('admin.surat.keluar.destroy',$item->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus surat ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn-delete"
                                        title="Hapus">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>
                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="table-empty">

                                <i class="bi bi-envelope-x-fill"></i>

                                <h5>

                                    Belum Ada Surat Keluar

                                </h5>

                                <p>

                                    Data surat keluar masih kosong.

                                </p>

                                <a
                                    href="{{ route('admin.surat.keluar.create') }}"
                                    class="btn btn-success mt-3">

                                    <i class="bi bi-plus-circle me-2"></i>

                                    Tambah Surat Keluar

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

        {{-- ===================================================== --}}
    {{-- TABLE FOOTER --}}
    {{-- ===================================================== --}}

    <div class="table-footer">

        <div class="text-muted">

            Menampilkan

            <strong>{{ $surat->firstItem() ?? 0 }}</strong>

            -

            <strong>{{ $surat->lastItem() ?? 0 }}</strong>

            dari

            <strong>{{ $surat->total() }}</strong>

            surat keluar

        </div>

        <div>

            {{ $surat->withQueryString()->links() }}

        </div>

    </div>

</div>

</div>

@endsection

@push('styles')

<style>

.outgoing-index-page{width:100%;max-width:100%;min-width:0}
.outgoing-index-page .page-header{gap:20px}.outgoing-index-page .page-header>div:first-child{min-width:0}.outgoing-index-page .page-header .btn{white-space:nowrap}
.outgoing-index-page .table-card{width:100%;max-width:100%;min-width:0;overflow:hidden}.outgoing-index-page .table-toolbar{padding:18px 22px}.outgoing-filters{display:grid;grid-template-columns:minmax(240px,1fr) 230px auto;align-items:center;gap:10px;width:100%;min-width:0}.outgoing-search{position:relative;min-width:0}.outgoing-search i{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#8290a3}.outgoing-search input{width:100%;height:48px;padding:10px 14px 10px 43px;border:1px solid #dbe2ea;border-radius:11px;outline:none}.outgoing-search input:focus{border-color:#198754;box-shadow:0 0 0 .18rem rgba(25,135,84,.12)}.outgoing-filters .status-filter{width:100%;height:48px;min-height:48px}.outgoing-filters .choices{width:230px;margin:0}.outgoing-filters .choices[data-type*=select-one] .choices__inner{height:48px!important;min-height:48px!important}.outgoing-filters .btn{height:48px;padding-inline:20px;border-radius:11px}
.outgoing-index-page .table-responsive{max-width:100%;overflow-x:auto}.outgoing-index-page table{min-width:850px}

.table-avatar{

    display:flex;

    align-items:center;

    gap:14px;

}

.table-avatar .avatar{

    width:50px;

    height:50px;

    border-radius:14px;

    display:flex;

    align-items:center;

    justify-content:center;

    flex-shrink:0;

    font-size:20px;

}

.table-avatar strong{

    display:block;

    color:#1f2937;

    font-size:15px;

    font-weight:600;

}

.table-avatar small{

    color:#6b7280;

}

.table-action{

    display:flex;

    justify-content:center;

    align-items:center;

    gap:8px;

}

.table-action a,

.table-action button{

    width:38px;

    height:38px;

    border:none;

    border-radius:10px;

    display:flex;

    align-items:center;

    justify-content:center;

    transition:.25s;

    text-decoration:none;

}

.btn-view{

    background:#dbeafe;

    color:#2563eb;

}

.btn-view:hover{

    background:#2563eb;

    color:#fff;

}

.btn-edit{

    background:#fef3c7;

    color:#d97706;

}

.btn-edit:hover{

    background:#d97706;

    color:#fff;

}

.btn-delete{

    background:#fee2e2;

    color:#dc2626;

}

.btn-delete:hover{

    background:#dc2626;

    color:#fff;

}

.table-footer{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:18px 24px;

    border-top:1px solid #edf2f7;

    background:#fafbfd;

}

.table-empty{

    padding:70px 20px;

    text-align:center;

}

.table-empty i{

    font-size:70px;

    color:#cbd5e1;

    display:block;

    margin-bottom:15px;

}

.table-empty h5{

    font-weight:700;

    color:#334155;

    margin-bottom:8px;

}

.table-empty p{

    color:#64748b;

    margin-bottom:20px;

}

.badge{

    border-radius:999px;

    padding:8px 12px;

    font-size:12px;

}

@media(max-width:768px){

    .outgoing-index-page .page-header{align-items:flex-start;flex-direction:column}.outgoing-index-page .page-header .btn{width:100%}.outgoing-filters{grid-template-columns:1fr}.outgoing-filters .choices{width:100%}.outgoing-filters .btn{width:100%}

    .table-footer{

        flex-direction:column;

        gap:15px;

        text-align:center;

    }

    .table-avatar{

        align-items:flex-start;

    }

    .table-action{

        justify-content:flex-start;

    }

}


</style>

@endpush

