@extends('layouts.admin')

@section('title','Surat Masuk')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-envelope-arrow-down-fill text-primary"></i>

            Surat Masuk

        </h2>

        <p class="text-muted mb-0">

            Kelola seluruh surat masuk yang diterima.

        </p>

    </div>

    <div>

        <a
            href="{{ route('admin.surat.masuk.create') }}"
            class="btn btn-primary">

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

            <h3>

                {{ $surat->total() }}

            </h3>

            <p>

                Total Surat

            </p>

        </div>

        <div class="stat-icon bg-primary-soft">

            <i class="bi bi-envelope-fill"></i>

        </div>

    </div>

    <div class="stat-card">
 
        <div>
 
            <h3>
 
                {{ $menunggu }}
 
            </h3>
 
            <p>
 
                Menunggu Verifikasi
 
            </p>
 
        </div>
 
        <div class="stat-icon bg-warning-soft">
 
            <i class="bi bi-hourglass-split"></i>
 
        </div>
 
    </div>
 
    <div class="stat-card">
 
        <div>
 
            <h3>
 
                {{ $disetujui }}
 
            </h3>
 
            <p>
 
                Diverifikasi
 
            </p>
 
        </div>
 
        <div class="stat-icon bg-success-soft">
 
            <i class="bi bi-check-circle-fill"></i>
 
        </div>
 
    </div>
 
    <div class="stat-card">
 
        <div>
 
            <h3>
 
                {{ $ditolak }}
 
            </h3>
 
            <p>
 
                Dikembalikan
 
            </p>
 
        </div>
 
        <div class="stat-icon bg-danger-soft">
 
            <i class="bi bi-x-circle-fill"></i>
 
        </div>
 
    </div>

</div>

{{-- ===================================================== --}}
{{-- TABLE CARD --}}
{{-- ===================================================== --}}

<div class="table-card fade-up">

    <div class="table-header">

        <h4>

            Daftar Surat Masuk

        </h4>

        <span class="badge bg-primary">

            {{ $surat->total() }} Surat

        </span>

    </div>

    <div class="table-toolbar">

        <form
            action="{{ route('admin.surat.masuk.index') }}"
            method="GET"
            class="table-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Cari Nomor Surat atau Perihal...">

        </form>

    </div>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th width="60">

                        No

                    </th>

                    <th>

                        Surat

                    </th>

                    <th>

                        Asal Surat

                    </th>

                    <th>

                        Tanggal

                    </th>

                    <th>

                        Status

                    </th>

                    <th width="180">

                        Aksi

                    </th>

                </tr>

            </thead>

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

                                <div class="avatar bg-primary">

                                    <i class="bi bi-envelope-fill text-white"></i>

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
                        {{-- ASAL SURAT --}}
                        {{-- ======================================= --}}

                        <td>

                            <strong>

                                {{ $item->asal_surat ?? '-' }}

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

                        {{-- ======================================= --}}
                        {{-- STATUS --}}
                        {{-- ======================================= --}}

                        <td>
 
                            @switch($item->status)
 
                                @case('menunggu')
 
                                    <span class="badge bg-warning text-dark">
 
                                        <i class="bi bi-hourglass-split me-1"></i>
 
                                        Menunggu
 
                                    </span>
 
                                @break
 
                                @case('diverifikasi')
 
                                    <span class="badge bg-success">
 
                                        <i class="bi bi-check-circle-fill me-1"></i>
 
                                        Diverifikasi
 
                                    </span>
 
                                @break
 
                                @case('dikembalikan')
 
                                    <span class="badge bg-danger">
 
                                        <i class="bi bi-x-circle-fill me-1"></i>
 
                                        Dikembalikan
 
                                    </span>
 
                                @break
 
                                @case('diproses')
 
                                    <span class="badge bg-info">
 
                                        <i class="bi bi-arrow-repeat me-1"></i>
 
                                        Diproses
 
                                    </span>
 
                                @break
 
                                @case('selesai')
 
                                    <span class="badge bg-primary">
 
                                        <i class="bi bi-flag-fill me-1"></i>
 
                                        Selesai
 
                                    </span>
 
                                @break
 
                                @default
 
                                    <span class="badge bg-secondary">
 
                                        {{ $item->status }}
 
                                    </span>
 
                            @endswitch
 
                        </td>

                        {{-- ======================================= --}}
                        {{-- AKSI --}}
                        {{-- ======================================= --}}

                        <td>

                            <div class="table-action">

                                <a
                                    href="{{ route('admin.surat.masuk.show',$item->id) }}"
                                    class="btn-view"
                                    title="Detail">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <a
                                    href="{{ route('admin.surat.masuk.edit',$item->id) }}"
                                    class="btn-edit"
                                    title="Edit">
 
                                    <i class="bi bi-pencil-square"></i>
 
                                </a>
 
                                @if($item->status == 'diajukan')
 
                                    <a
                                        href="{{ route('admin.surat.masuk.show',$item->id) }}"
                                        class="btn-approve"
                                        title="Setujui / Tolak">
 
                                        <i class="bi bi-clipboard-check"></i>
 
                                    </a>
 
                                @endif

                                <form
                                    action="{{ route('admin.surat.masuk.destroy',$item->id) }}"
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

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="table-empty">

                                <i class="bi bi-inbox"></i>

                                <h5>

                                    Belum Ada Surat Masuk

                                </h5>

                                <p>

                                    Data surat masuk belum tersedia.

                                </p>

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

            <strong>

                {{ $surat->firstItem() ?? 0 }}

            </strong>

            -

            <strong>

                {{ $surat->lastItem() ?? 0 }}

            </strong>

            dari

            <strong>

                {{ $surat->total() }}

            </strong>

            surat masuk

        </div>

        <div>

            {{ $surat->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection

{{-- ===================================================== --}}
{{-- PAGE STYLE --}}
{{-- ===================================================== --}}

@push('styles')

<style>

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

    font-size:20px;

    flex-shrink:0;

}

.table-avatar strong{

    color:#1e293b;

    font-size:15px;

}

.table-avatar small{

    color:#64748b;

}

.table-action{

    display:flex;

    align-items:center;

    justify-content:center;

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

    transition:.25s ease;

    cursor:pointer;

}

.btn-view{

    background:#e0f2fe;

    color:#0284c7;

}

.btn-view:hover{

    background:#0284c7;

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

 .btn-approve{
 
     background:#dcfce7;
 
     color:#16a34a;
 
 }
 
 .btn-approve:hover{
 
     background:#16a34a;
 
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

    padding:20px 24px;

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

    margin-bottom:15px;

}

.table-empty h5{

    font-weight:700;

    color:#334155;

}

.table-empty p{

    color:#64748b;

}

.badge{

    font-size:12px;

    padding:8px 12px;

    border-radius:30px;

}

@media(max-width:768px){

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



