@extends('layouts.pegawai')

@section('title','Surat Keluar')

@section('content')

<div class="container-fluid">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header fade-up">

        <div>

            <h2>

                <i class="bi bi-send-fill text-primary me-2"></i>

                Surat Keluar

            </h2>

            <p class="text-muted mb-0">

                Kelola seluruh surat keluar yang telah Anda buat.

            </p>

        </div>

        <a href="{{ route('pegawai.surat-keluar.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle-fill me-2"></i>

            Tambah Surat

        </a>

    </div>


    {{-- =========================
        ALERT
    ========================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif



    {{-- =========================
        STATISTIK
    ========================== --}}
    <div class="dashboard-grid mb-4">

        <div class="stat-card">

            <div>

                <span>Total Surat</span>

                <h3>

                    {{ $surat->total() }}

                </h3>

            </div>

            <div class="stat-icon primary">

                <i class="bi bi-envelope-fill"></i>

            </div>

        </div>



        <div class="stat-card">

            <div>

                <span>Menunggu</span>

                <h3>

                    {{ $surat->where('status','Menunggu')->count() }}

                </h3>

            </div>

            <div class="stat-icon warning">

                <i class="bi bi-hourglass-split"></i>

            </div>

        </div>



        <div class="stat-card">

            <div>

                <span>Diproses</span>

                <h3>

                    {{ $surat->where('status','Diproses')->count() }}

                </h3>

            </div>

            <div class="stat-icon info">

                <i class="bi bi-arrow-repeat"></i>

            </div>

        </div>



        <div class="stat-card">

            <div>

                <span>Selesai</span>

                <h3>

                    {{ $surat->where('status','Selesai')->count() }}

                </h3>

            </div>

            <div class="stat-icon success">

                <i class="bi bi-check-circle-fill"></i>

            </div>

        </div>

    </div>



    {{-- =========================
        TABLE CARD
    ========================== --}}
    <div class="table-card fade-up">

        <div class="table-header">

            <div>

                <h4>

                    <i class="bi bi-list-ul me-2 text-primary"></i>

                    Daftar Surat Keluar

                </h4>

                <small class="text-muted">

                    Seluruh surat keluar yang telah Anda buat.

                </small>

            </div>

            <span class="badge bg-primary">

                {{ $surat->total() }} Surat

            </span>

        </div>



        {{-- =========================
            SEARCH & FILTER
        ========================== --}}
        <div class="table-toolbar">

            <form
                action="{{ route('pegawai.surat-keluar.index') }}"
                method="GET"
                class="table-search">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="keyword"
                    value="{{ request('keyword') }}"
                    placeholder="Cari nomor surat atau perihal...">

            </form>


            <form
                action="{{ route('pegawai.surat-keluar.index') }}"
                method="GET"
                class="d-flex gap-2">

                <input
                    type="hidden"
                    name="keyword"
                    value="{{ request('keyword') }}">

                <select
                    name="status"
                    class="form-select">

                    <option value="">

                        Semua Status

                    </option>

                    <option
                        value="Menunggu"
                        {{ request('status')=='Menunggu' ? 'selected' : '' }}>

                        Menunggu

                    </option>

                    <option
                        value="Diproses"
                        {{ request('status')=='Diproses' ? 'selected' : '' }}>

                        Diproses

                    </option>

                    <option
                        value="Selesai"
                        {{ request('status')=='Selesai' ? 'selected' : '' }}>

                        Selesai

                    </option>

                </select>

                <button
                    class="btn btn-primary">

                    <i class="bi bi-funnel-fill"></i>

                </button>

            </form>

        </div>


        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th width="60">No</th>

                        <th>Nomor Surat</th>

                        <th>Perihal</th>

                        <th>Tujuan Surat</th>

                        <th width="130">Tanggal</th>

                        <th width="120">Status</th>

                        <th width="170">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($surat as $item)

<tr>

    <td>

        {{ $surat->firstItem() + $loop->index }}

    </td>

    <td>

        <strong>

            {{ $item->nomor_surat }}

        </strong>

    </td>

    <td>

        {{ $item->perihal }}

    </td>

    <td>

        {{ $item->tujuan_surat ?? '-' }}

    </td>

    <td>

        {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d M Y') }}

    </td>

    <td>

        @if($item->status == 'Menunggu')

            <span class="badge bg-warning text-dark">

                Menunggu

            </span>

        @elseif($item->status == 'Diproses')

            <span class="badge bg-info text-white">

                Diproses

            </span>

        @elseif($item->status == 'Selesai')

            <span class="badge bg-success">

                Selesai

            </span>

        @elseif($item->status == 'Ditolak')

            <span class="badge bg-danger">

                Ditolak

            </span>

        @else

            <span class="badge bg-secondary">

                {{ $item->status }}

            </span>

        @endif

    </td>

    <td>

        <div class="btn-group" role="group">

            {{-- DETAIL --}}
            <a href="{{ route('pegawai.surat-keluar.show',$item->id) }}"
               class="btn btn-sm btn-outline-primary"
               title="Detail">

                <i class="bi bi-eye-fill"></i>

            </a>


            {{-- EDIT hanya ketika Menunggu --}}
            @if($item->status == 'Menunggu')

                <a href="{{ route('pegawai.surat-keluar.edit',$item->id) }}"
                   class="btn btn-sm btn-outline-warning"
                   title="Edit">

                    <i class="bi bi-pencil-square"></i>

                </a>

            @endif


            {{-- HAPUS hanya ketika Menunggu --}}
            @if($item->status == 'Menunggu')

                <form
                    action="{{ route('pegawai.surat-keluar.destroy',$item->id) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus surat ini?')">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-sm btn-outline-danger"
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

    <td colspan="7">

        <div class="table-empty">

            <i class="bi bi-inbox text-secondary"></i>

            <h5 class="mt-3">

                Belum Ada Surat Keluar

            </h5>

            <p class="text-muted mb-4">

                Silakan membuat surat keluar baru.

            </p>

            <a href="{{ route('pegawai.surat-keluar.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle-fill me-2"></i>

                Tambah Surat

            </a>

        </div>

    </td>

</tr>

@endforelse

</tbody>

</table>

</div>
        {{-- ===========================
            FOOTER TABLE
        ============================ --}}
        <div class="table-footer">

            <div>

                Menampilkan

                <strong>{{ $surat->firstItem() ?? 0 }}</strong>

                -

                <strong>{{ $surat->lastItem() ?? 0 }}</strong>

                dari

                <strong>{{ $surat->total() }}</strong>

                surat

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

/* ============================================
   PAGE HEADER
============================================ */

.page-header{

    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    gap:20px;

}

.page-header h2{

    font-size:30px;
    font-weight:700;
    color:#0f172a;

}

.page-header p{

    color:#64748b;

}


/* ============================================
   DASHBOARD GRID
============================================ */

.dashboard-grid{

    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:22px;
    margin-bottom:30px;

}

.stat-card{

    background:#fff;
    border-radius:22px;
    padding:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 12px 30px rgba(15,23,42,.05);
    border:1px solid #edf2f7;
    transition:.3s;

}

.stat-card:hover{

    transform:translateY(-5px);

}

.stat-card span{

    color:#64748b;
    font-size:14px;

}

.stat-card h3{

    font-size:34px;
    font-weight:700;
    margin-top:6px;

}

.stat-icon{

    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:28px;

}

.primary{

    background:#DBEAFE;
    color:#2563EB;

}

.warning{

    background:#FEF3C7;
    color:#D97706;

}

.info{

    background:#E0F2FE;
    color:#0284C7;

}

.success{

    background:#DCFCE7;
    color:#16A34A;

}


/* ============================================
   TABLE CARD
============================================ */

.table-card{

    background:#fff;
    border-radius:22px;
    overflow:hidden;
    border:1px solid #e5e7eb;
    box-shadow:0 10px 30px rgba(0,0,0,.05);

}

.table-header{

    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:24px 30px;
    border-bottom:1px solid #edf2f7;

}

.table-toolbar{

    padding:20px 30px;
    display:flex;
    justify-content:space-between;
    gap:20px;
    flex-wrap:wrap;

}

.table-search{

    position:relative;
    flex:1;

}

.table-search i{

    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#94A3B8;

}

.table-search input{

    width:100%;
    padding:12px 16px 12px 46px;
    border-radius:14px;
    border:1px solid #dbe2ea;

}

.table{

    margin-bottom:0;

}

.table thead{

    background:#F8FAFC;

}

.table thead th{

    font-weight:700;

}

.table td{

    vertical-align:middle;

}

.table tbody tr:hover{

    background:#F8FAFC;

}


/* ============================================
   BUTTON GROUP
============================================ */

.btn-group{

    display:flex;
    gap:6px;

}

.btn-group .btn{

    width:38px;
    height:38px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:10px;

}


/* ============================================
   EMPTY STATE
============================================ */

.table-empty{

    padding:60px 20px;
    text-align:center;

}

.table-empty i{

    font-size:60px;

}


/* ============================================
   FOOTER
============================================ */

.table-footer{

    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 30px;
    border-top:1px solid #edf2f7;
    background:#fafafa;

}

.pagination{

    margin-bottom:0;

}

.page-link{

    border-radius:10px!important;
    margin:0 2px;

}


/* ============================================
   ANIMATION
============================================ */

.fade-up{

    animation:fadeUp .45s ease;

}

@keyframes fadeUp{

    from{

        opacity:0;
        transform:translateY(20px);

    }

    to{

        opacity:1;
        transform:translateY(0);

    }

}


/* ============================================
   RESPONSIVE
============================================ */

@media(max-width:992px){

.dashboard-grid{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:768px){

.page-header{

flex-direction:column;
align-items:flex-start;

}

.table-toolbar{

flex-direction:column;

}

.table-footer{

flex-direction:column;
gap:15px;
text-align:center;

}

}

@media(max-width:576px){

.dashboard-grid{

grid-template-columns:1fr;

}

}

</style>

@endpush

