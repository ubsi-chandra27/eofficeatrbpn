@extends('layouts.admin')

@section('title','Detail Unit Kerja')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-building-fill text-primary"></i>

            Detail Unit Kerja

        </h2>

        <p class="text-muted mb-0">

            Informasi lengkap data unit kerja.

        </p>

    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('admin.unit.kerja.edit',$unitKerja->id) }}"
            class="btn btn-warning text-white">

            <i class="bi bi-pencil-square me-2"></i>

            Edit

        </a>

        <a
            href="{{ route('admin.unit.kerja.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>

</div>

<div class="detail-card fade-up">

    <div class="detail-header">

        <div class="detail-icon bg-primary-subtle text-primary">

            <i class="bi bi-building-fill"></i>

        </div>

        <div>

            <h3>

                {{ $unitKerja->nama }}

            </h3>

            <p class="mb-0 text-muted">

                Detail informasi unit kerja

            </p>

        </div>

    </div>

    <div class="detail-body">

        <div class="row">

            {{-- Kode Unit --}}
            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Kode Unit

                </label>

                <div class="detail-value">

                    {{ $unitKerja->kode ?: 'Belum ditentukan' }}

                </div>

            </div>

            {{-- Nama Unit --}}
            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Nama Unit Kerja

                </label>

                <div class="detail-value">

                    {{ $unitKerja->nama }}

                </div>

            </div>

                        {{-- =========================================== --}}
            {{-- DESKRIPSI --}}
            {{-- =========================================== --}}

            <div class="col-12 mb-4">

                <label class="detail-label">

                    Deskripsi

                </label>

                <div class="detail-value detail-description">

                    {{ $unitKerja->deskripsi ?: 'Tidak ada deskripsi.' }}

                </div>

            </div>

            {{-- =========================================== --}}
            {{-- DIBUAT --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Dibuat Pada

                </label>

                <div class="detail-value">

                    {{ $unitKerja->created_at ? $unitKerja->created_at->format('d F Y H:i') : '-' }}

                </div>

            </div>

            {{-- =========================================== --}}
            {{-- DIUPDATE --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Terakhir Diupdate

                </label>

                <div class="detail-value">

                    {{ $unitKerja->updated_at ? $unitKerja->updated_at->format('d F Y H:i') : '-' }}

                </div>

            </div>

        </div>

    </div>

    <div class="employee-section">
        <div class="employee-heading"><div><h5>Pegawai pada Unit Ini</h5><p>Daftar pegawai yang ditempatkan pada unit kerja tersebut.</p></div><span class="badge bg-primary">{{ $unitKerja->pegawai_count }} Pegawai</span></div>
        <div class="employee-list">
            @forelse($unitKerja->pegawai as $pegawai)
                <a href="{{ route('admin.pegawai.show',$pegawai->id) }}" class="employee-item"><span class="employee-avatar">{{ strtoupper(substr($pegawai->nama,0,1)) }}</span><span><strong>{{ $pegawai->nama }}</strong><small>{{ $pegawai->nip ?: 'NIP belum tersedia' }}</small></span><i class="bi bi-chevron-right"></i></a>
            @empty<div class="text-center text-muted py-4"><i class="bi bi-person-x fs-2 d-block mb-2"></i>Belum ada pegawai pada unit ini.</div>@endforelse
        </div>
    </div>

    <div class="detail-footer">

        <a
            href="{{ route('admin.unit.kerja.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

        <a
            href="{{ route('admin.unit.kerja.edit',$unitKerja->id) }}"
            class="btn btn-warning text-white">

            <i class="bi bi-pencil-square me-2"></i>

            Edit Unit Kerja

        </a>

    </div>

</div>

@endsection

@push('styles')

<style>

.detail-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(15,76,129,.08);
}

.detail-header{
    display:flex;
    align-items:center;
    gap:20px;
    padding:30px;
    border-bottom:1px solid #edf2f7;
}

.detail-icon{
    width:72px;
    height:72px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
}

.detail-header h3{
    margin:0;
    font-size:24px;
    font-weight:700;
    color:#1e293b;
}

.detail-body{
    padding:30px;
}

.detail-label{
    display:block;
    margin-bottom:8px;
    font-size:13px;
    font-weight:700;
    color:#64748b;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.detail-value{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:14px 16px;
    min-height:52px;
    display:flex;
    align-items:center;
    color:#1e293b;
}

.detail-description{
    min-height:120px;
    align-items:flex-start;
    white-space:pre-wrap;
}

.detail-footer{
    border-top:1px solid #edf2f7;
    padding:24px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.detail-footer .btn{
    min-width:180px;
    border-radius:12px;
}
.employee-section{padding:0 30px 30px}.employee-heading{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:12px}.employee-heading h5{margin:0;font-weight:700;color:#1e293b}.employee-heading p{margin:3px 0 0;color:#64748b;font-size:13px}.employee-list{border:1px solid #e2e8f0;border-radius:13px;overflow:hidden}.employee-item{display:flex;align-items:center;gap:12px;padding:12px 15px;color:#334155;text-decoration:none;border-bottom:1px solid #edf2f7}.employee-item:last-child{border-bottom:0}.employee-item:hover{background:#f8fafc}.employee-avatar{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;background:#edf6ff;color:#0f4c81;font-weight:700}.employee-item>span:nth-child(2){flex:1}.employee-item strong,.employee-item small{display:block}.employee-item small{color:#8491a3;font-size:12px}

@media(max-width:768px){

    .detail-header{
        flex-direction:column;
        text-align:center;
    }

    .detail-body{
        padding:20px;
    }

    .detail-footer{
        flex-direction:column;
        gap:15px;
    }

    .detail-footer .btn{
        width:100%;
    }

}

</style>

@endpush
