@extends('layouts.admin')

@section('title','Detail Surat Keluar')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-envelope-paper-fill text-success"></i>

            Detail Surat Keluar

        </h2>

        <p class="text-muted mb-0">

            Informasi lengkap surat keluar.

        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap">

        <a href="{{ route('admin.surat.keluar.edit',$surat->id) }}"
           class="btn btn-warning text-white">

            <i class="bi bi-pencil-square me-2"></i>

            Edit

        </a>

        @if($surat->status === 'diajukan')
            <form action="{{ route('admin.surat.keluar.setujui', $surat->id) }}" method="POST" onsubmit="return confirm('Setujui dan verifikasi surat keluar ini?')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Setujui
                </button>
            </form>

            <form action="{{ route('admin.surat.keluar.tolak', $surat->id) }}" method="POST" onsubmit="const note = prompt('Masukkan catatan penolakan surat keluar:'); if (note === null || note.trim() === '') { alert('Catatan penolakan wajib diisi.'); return false; } this.querySelector('[name=catatan_admin]').value = note.trim(); return true;">
                @csrf
                <input type="hidden" name="catatan_admin">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-2"></i>
                    Tolak
                </button>
            </form>
        @endif

        <a href="{{ route('admin.surat.keluar.index') }}"
           class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>

</div>

<div class="detail-card fade-up">

    <div class="detail-header">

        <div class="detail-icon bg-success-subtle text-success">

            <i class="bi bi-send-fill"></i>

        </div>

        <div>

            <h3>

                {{ $surat->nomor_surat }}

            </h3>

            <p class="mb-0 text-muted">

                {{ $surat->perihal }}

            </p>

        </div>

    </div>

    <div class="detail-body">

        <div class="row">

            {{-- Nomor Surat --}}
            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Nomor Surat

                </label>

                <div class="detail-value">

                    {{ $surat->nomor_surat }}

                </div>

            </div>

            {{-- Tanggal Surat --}}
            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Tanggal Surat

                </label>

                <div class="detail-value">

                    {{ $surat->tanggal_surat?->translatedFormat('d F Y') ?? '-' }}

                </div>

            </div>

            {{-- Perihal --}}
            <div class="col-12 mb-4">

                <label class="detail-label">

                    Perihal

                </label>

                <div class="detail-value">

                    {{ $surat->perihal }}

                </div>

            </div>

                        {{-- =========================================== --}}
            {{-- TUJUAN SURAT --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Tujuan Surat

                </label>

                <div class="detail-value">

                    {{ $surat->tujuan_surat ?: '-' }}

                </div>

            </div>

            {{-- =========================================== --}}
            {{-- NOMOR AGENDA --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Nomor Agenda

                </label>

                <div class="detail-value">

                    {{ $surat->nomor_agenda ?: '-' }}

                </div>

            </div>

            {{-- =========================================== --}}
            {{-- METODE PENGIRIMAN --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Metode Pengiriman

                </label>

                <div class="detail-value">

                    {{ $surat->metode ?: '-' }}

                </div>

            </div>

            <div class="col-md-6 mb-4">
                <label class="detail-label">Penandatangan</label>
                <div class="detail-value">{{ $surat->penandatangan ?: '-' }}</div>
            </div>

            <div class="col-md-6 mb-4">
                <label class="detail-label">Status Surat</label>
                <div class="detail-value">
                    <span class="badge bg-{{ $surat->status_badge }}">{{ $surat->status_label }}</span>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <label class="detail-label">Tanggal Keluar</label>
                <div class="detail-value">{{ $surat->tanggal_keluar?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}</div>
            </div>

            <div class="col-md-6 mb-4">
                <label class="detail-label">Tanggal Kirim</label>
                <div class="detail-value">{{ $surat->tanggal_kirim?->translatedFormat('d F Y') ?? 'Belum dikirim' }}</div>
            </div>

            <div class="col-12 mb-4">
                <label class="detail-label">Catatan Admin</label>
                <div class="detail-value detail-description">{{ $surat->catatan_admin ?: 'Belum ada catatan admin.' }}</div>
            </div>

            {{-- =========================================== --}}
            {{-- PRIORITAS --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Prioritas

                </label>

                <div class="detail-value">

                    @if($surat->is_priority)

                        <span class="badge bg-danger">

                            <i class="bi bi-star-fill me-1"></i>

                            Prioritas Tinggi

                        </span>

                    @else

                        <span class="badge bg-secondary">

                            Normal

                        </span>

                    @endif

                </div>

            </div>

            {{-- =========================================== --}}
            {{-- LAMPIRAN --}}
            {{-- =========================================== --}}

            <div class="col-12 mb-4">

                <label class="detail-label">

                    Lampiran Surat

                </label>

                <div class="detail-value">

                    @if($surat->file_path)

                        <a
                            href="{{ route('surat.lampiran', $surat) }}"
                            target="_blank"
                            class="btn btn-outline-success">

                            <i class="bi bi-file-earmark-pdf-fill me-2"></i>

                            Lihat Lampiran

                        </a>

                    @else

                        <span class="text-muted">

                            Tidak ada lampiran

                        </span>

                    @endif

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

                    {{ $surat->deskripsi ?: 'Tidak ada deskripsi.' }}

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

                    {{ $surat->created_at ? $surat->created_at->format('d F Y H:i') : '-' }}

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

                    {{ $surat->updated_at ? $surat->updated_at->format('d F Y H:i') : '-' }}

                </div>

            </div>

        </div>

    </div>

    <div class="detail-footer">

        <a
            href="{{ route('admin.surat.keluar.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

        <a
            href="{{ route('admin.surat.keluar.edit',$surat->id) }}"
            class="btn btn-warning text-white">

            <i class="bi bi-pencil-square me-2"></i>

            Edit Surat

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
    font-size:22px;
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
    align-items:flex-start;
    min-height:120px;
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
    min-width:170px;
    border-radius:12px;
}

.badge{
    border-radius:999px;
    padding:8px 12px;
    font-size:12px;
}

@media (max-width:768px){

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
