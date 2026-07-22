@extends('layouts.admin')

@section('title','Data Pegawai')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-people-fill text-primary"></i>

            Data Pegawai

        </h2>

        <p class="text-muted mb-0">

            Kelola seluruh data pegawai dan keterhubungan akun login.

        </p>

    </div>

    <div>

        <a
            href="{{ route('admin.pegawai.create') }}"
            class="btn btn-primary">

            <i class="bi bi-plus-circle me-2"></i>

            Tambah Pegawai

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

                {{ $pegawai->total() }}

            </h3>

            <p>

                Total Pegawai

            </p>

        </div>

        <div class="stat-icon bg-primary-soft">

            <i class="bi bi-people-fill"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>

                {{ $pegawai->whereNotNull('jabatan_id')->count() }}

            </h3>

            <p>

                Memiliki Jabatan

            </p>

        </div>

        <div class="stat-icon bg-success-soft">

            <i class="bi bi-person-badge-fill"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>

                {{ $pegawai->whereNotNull('unit_kerja_id')->count() }}

            </h3>

            <p>

                Memiliki Unit

            </p>

        </div>

        <div class="stat-icon bg-warning-soft">

            <i class="bi bi-building"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>

                {{ date('Y') }}

            </h3>

            <p>

                Tahun Aktif

            </p>

        </div>

        <div class="stat-icon bg-danger-soft">

            <i class="bi bi-calendar-event"></i>

        </div>

    </div>

</div>

{{-- ===================================================== --}}
{{-- TABLE CARD --}}
{{-- ===================================================== --}}

<div class="table-card fade-up">

    <div class="table-header">

        <h4>

            Daftar Pegawai

        </h4>

        <span class="badge bg-primary">

            {{ $pegawai->total() }} Data

        </span>

    </div>

    <div class="table-toolbar">

        <form
            action="{{ route('admin.pegawai.index') }}"
            method="GET"
            class="table-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Cari NIP atau Nama Pegawai...">

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

                        Pegawai

                    </th>

                    <th>

                        Jabatan

                    </th>

                    <th>

                        Unit Kerja

                    </th>

                    <th width="180">

                        Aksi

                    </th>

                </tr>

            </thead>

            <tbody>

                            @forelse($pegawai as $item)

                    <tr>

                        <td>

                            {{ $pegawai->firstItem() + $loop->index }}

                        </td>

                        {{-- ========================= --}}
                        {{-- PEGAWAI --}}
                        {{-- ========================= --}}

                        <td>

                            <div class="table-avatar">

                                <div class="avatar">

                                    {{ strtoupper(substr($item->nama,0,1)) }}

                                </div>

                                <div>

                                    <strong>

                                        {{ $item->nama }}

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        NIP :

                                        {{ $item->nip }}

                                    </small>

                                </div>

                            </div>

                        </td>

                        {{-- ========================= --}}
                        {{-- JABATAN --}}
                        {{-- ========================= --}}

                        <td>

                            @if($item->jabatan)

                                <span class="badge badge-primary">

                                    {{ $item->jabatan->nama }}

                                </span>

                            @else

                                <span class="badge badge-danger">

                                    Belum Ada

                                </span>

                            @endif

                        </td>

                        {{-- ========================= --}}
                        {{-- UNIT KERJA --}}
                        {{-- ========================= --}}

                        <td>

                            @if($item->unitKerja)

                                <span class="badge badge-success">

                                    {{ $item->unitKerja->nama }}

                                </span>

                            @else

                                <span class="badge badge-warning">

                                    Belum Ada

                                </span>

                            @endif

                        </td>

                        {{-- ========================= --}}
                        {{-- AKSI --}}
                        {{-- ========================= --}}

                        <td>

                            <div class="table-action">

                                <a
                                    href="{{ route('admin.pegawai.show',$item->id) }}"
                                    class="btn-view"
                                    title="Detail">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <a
                                    href="{{ route('admin.pegawai.edit',$item->id) }}"
                                    class="btn-edit"
                                    title="Edit">

                                    <i class="bi bi-pencil-square"></i>

                                </a>

                                <form
                                    action="{{ route('admin.pegawai.destroy',$item->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">

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

                        <td colspan="5">

                            <div class="table-empty">

                                <i class="bi bi-people"></i>

                                <h5>

                                    Belum Ada Data Pegawai

                                </h5>

                                <p>

                                    Silakan tambahkan data pegawai terlebih dahulu.

                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

        {{-- ===================================================== --}}
    {{-- FOOTER TABLE --}}
    {{-- ===================================================== --}}

    <div class="table-footer">

        <div class="text-muted">

            Menampilkan

            <strong>

                {{ $pegawai->firstItem() ?? 0 }}

            </strong>

            -

            <strong>

                {{ $pegawai->lastItem() ?? 0 }}

            </strong>

            dari

            <strong>

                {{ $pegawai->total() }}

            </strong>

            data pegawai

        </div>

        <div>

            {{ $pegawai->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection

{{-- ===================================================== --}}
{{-- PAGE STYLE --}}
{{-- ===================================================== --}}

@push('styles')

<style>

.page-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:28px;

}

.page-header h2{

    font-weight:700;

    margin-bottom:6px;

}

.table-toolbar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:20px 24px;

    border-bottom:1px solid #eef2f7;

}

.table-footer{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:22px 24px;

    border-top:1px solid #eef2f7;

    background:#fafbfd;

}

.table-avatar{

    display:flex;

    align-items:center;

    gap:14px;

}

.table-avatar .avatar{

    width:48px;

    height:48px;

    border-radius:50%;

    background:linear-gradient(135deg,#0F4C81,#2f80ed);

    color:#fff;

    display:flex;

    align-items:center;

    justify-content:center;

    font-weight:700;

    font-size:18px;

}

.stat-card{

    display:flex;

    justify-content:space-between;

    align-items:center;

}

.stat-card h3{

    font-size:30px;

    font-weight:700;

    margin-bottom:5px;

}

.stat-card p{

    margin:0;

    color:#64748b;

}

.stat-icon{

    width:60px;

    height:60px;

    border-radius:16px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:24px;

}

.table-action{

    display:flex;

    gap:10px;

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

}

.table-empty{

    text-align:center;

    padding:70px 20px;

}

.table-empty i{

    font-size:70px;

    color:#cbd5e1;

    margin-bottom:15px;

}

@media(max-width:768px){

    .page-header{

        flex-direction:column;

        align-items:flex-start;

        gap:20px;

    }

    .table-toolbar{

        flex-direction:column;

        align-items:stretch;

        gap:16px;

    }

    .table-footer{

        flex-direction:column;

        gap:15px;

        text-align:center;

    }

}

</style>

@endpush
