@extends('layouts.admin')

@section('title','Data Disposisi')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-send-check-fill text-primary"></i>

            Data Disposisi

        </h2>

        <p class="text-muted mb-0">

            Kelola seluruh data disposisi surat.

        </p>

    </div>

    <a
        href="{{ route('admin.disposisi.create') }}"
        class="btn btn-primary">

        <i class="bi bi-plus-circle me-2"></i>

        Tambah Disposisi

    </a>

</div>

{{-- Statistik --}}

<div class="stats-grid fade-up">

    <div class="stat-card">

        <div>

            <h3>

                {{ $disposisi->total() }}

            </h3>

            <p>Total Disposisi</p>

        </div>

        <div class="stat-icon bg-primary-soft">

            <i class="bi bi-send-check-fill"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>

                {{ $statistik['Belum Dibaca'] ?? 0 }}

            </h3>

            <p>Belum Dibaca</p>

        </div>

        <div class="stat-icon bg-warning-soft">

            <i class="bi bi-hourglass-split"></i>

        </div>

    </div>

    <div class="stat-card">

        <div>

            <h3>

                {{ $statistik['Selesai'] ?? 0 }}

            </h3>

            <p>Selesai</p>

        </div>

        <div class="stat-icon bg-success-soft">

            <i class="bi bi-check-circle-fill"></i>

        </div>

    </div>

</div>

<div class="content-card fade-up">

    <div class="card-header">

        <div>

            <h5>

                Daftar Disposisi

            </h5>

        </div>

        <form
            action="{{ route('admin.disposisi.index') }}"
            method="GET"
            class="search-box">

            <i class="bi bi-search"></i>

            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Cari disposisi...">

        </form>

    </div>

    <div class="table-responsive">

        <table class="table custom-table">

            <thead>

                <tr>

                    <th>No</th>

                    <th>No Surat</th>

                    <th>Pengirim</th>

                    <th>Penerima</th>

                    <th>Status</th>

                    <th width="180">Aksi</th>

                </tr>

            </thead>

            <tbody>

@forelse($disposisi as $item)

<tr>

    <td>
        {{ $loop->iteration + $disposisi->firstItem() - 1 }}
    </td>

    <td>
        <strong>
            {{ $item->surat->nomor_surat ?? '-' }}
        </strong>
    </td>

    <td>
        {{ $item->pengirim->name ?? '-' }}
    </td>

    <td>

        @forelse($item->tujuans as $tujuan)

            <span class="badge bg-primary mb-1">

                {{ $tujuan->pegawai->nama ?? '-' }}

            </span><br>

        @empty

            -

        @endforelse

    </td>

    <td>

        @forelse($item->tujuans as $tujuan)

            @if($tujuan->status == 'Belum Dibaca')

                <span class="badge bg-warning text-dark mb-1">

                    Belum Dibaca

                </span>

            @elseif($tujuan->status == 'Sudah Dibaca')

                <span class="badge bg-info mb-1">

                    Sudah Dibaca

                </span>

            @elseif($tujuan->status == 'Selesai')

                <span class="badge bg-success mb-1">

                    Selesai

                </span>

            @else

                <span class="badge bg-secondary mb-1">

                    {{ $tujuan->status }}

                </span>

            @endif

            <br>

        @empty

            -

        @endforelse

    </td>

    <td>

        <div class="table-actions">

            <a
                href="{{ route('admin.disposisi.show',$item->id) }}"
                class="btn btn-info btn-sm">

                <i class="bi bi-eye-fill"></i>

            </a>

            <a
                href="{{ route('admin.disposisi.edit',$item->id) }}"
                class="btn btn-warning btn-sm text-white">

                <i class="bi bi-pencil-square"></i>

            </a>

            <form
                action="{{ route('admin.disposisi.destroy',$item->id) }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus disposisi ini?')">

                @csrf
                @method('DELETE')

                <button
                    class="btn btn-danger btn-sm">

                    <i class="bi bi-trash-fill"></i>

                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>

    <td colspan="6" class="text-center py-5">

        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>

        Belum ada data disposisi.

    </td>

</tr>

@endforelse

</tbody>

        </table>

    </div>

        <div class="card-footer">

        {{ $disposisi->links() }}

    </div>

</div>

@endsection

@push('styles')

<style>

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.stat-card{
    background:#fff;
    border-radius:18px;
    padding:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(15,76,129,.08);
}

.stat-card h3{
    margin:0;
    font-size:30px;
    font-weight:700;
    color:#0F4C81;
}

.stat-card p{
    margin:6px 0 0;
    color:#64748b;
}

.stat-icon{
    width:65px;
    height:65px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
}

.bg-primary-soft{
    background:#e7f1ff;
    color:#0F4C81;
}

.bg-success-soft{
    background:#dcfce7;
    color:#16a34a;
}

.bg-warning-soft{
    background:#fff4d6;
    color:#d97706;
}

.content-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(15,76,129,.08);
}

.content-card .card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:24px 30px;
    border-bottom:1px solid #edf2f7;
}

.content-card .card-header h5{
    margin:0;
    font-weight:700;
}

.search-box{
    position:relative;
}

.search-box i{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
}

.search-box input{
    width:260px;
    padding:11px 14px 11px 42px;
    border-radius:12px;
    border:1px solid #dbe2ea;
    outline:none;
}

.search-box input:focus{
    border-color:#0F4C81;
    box-shadow:0 0 0 .2rem rgba(15,76,129,.10);
}

.custom-table{
    margin:0;
}

.custom-table thead th{
    background:#f8fafc;
    color:#475569;
    font-weight:700;
    border-bottom:1px solid #e2e8f0;
    padding:16px;
}

.custom-table tbody td{
    padding:16px;
    vertical-align:middle;
}

.custom-table tbody tr:hover{
    background:#fafcff;
}

.table-actions{
    display:flex;
    gap:8px;
}

.table-actions form{
    display:inline;
}

.table-actions .btn{
    width:38px;
    height:38px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.badge{
    border-radius:999px;
    padding:8px 12px;
    font-size:12px;
    font-weight:600;
}

.card-footer{
    padding:20px 30px;
    border-top:1px solid #edf2f7;
    background:#fff;
}

@media(max-width:768px){

    .content-card .card-header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .search-box,
    .search-box input{
        width:100%;
    }

    .table-actions{
        flex-wrap:wrap;
    }

}

</style>

@endpush
