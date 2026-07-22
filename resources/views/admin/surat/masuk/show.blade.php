@extends('layouts.admin')

@section('title','Detail Surat Masuk')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-file-earmark-text-fill text-primary"></i>

            Detail Surat Masuk

        </h2>

        <p class="text-muted mb-0">

            Informasi lengkap surat masuk.

        </p>

    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('admin.surat.masuk.edit',$surat->id) }}"
            class="btn btn-warning text-white">

            <i class="bi bi-pencil-square me-2"></i>

            Edit

        </a>

        <a
            href="{{ route('admin.surat.masuk.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>

</div>

<div class="detail-card fade-up">

    <div class="detail-header">

        <div class="detail-icon">

            <i class="bi bi-envelope-paper-fill"></i>

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

            {{-- Tanggal --}}
            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Tanggal Surat

                </label>

                <div class="detail-value">

                    {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}

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
            {{-- ASAL SURAT --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Asal Surat

                </label>

                <div class="detail-value">

                    {{ $surat->asal_surat ?: '-' }}

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

            {{-- =========================================== --}}
            {{-- STATUS --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Status Surat

                </label>

                <div class="detail-value">

                    @switch(strtolower($surat->status))
 
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
 
                                {{ ucfirst($surat->status) }}
 
                            </span>
 
                    @endswitch

                </div>

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
            {{-- FILE LAMPIRAN --}}
            {{-- =========================================== --}}

            <div class="col-md-6 mb-4">

                <label class="detail-label">

                    Lampiran Surat

                </label>

                <div class="detail-value">

                    @if($surat->file_path)

                        <a
                            href="{{ asset('storage/'.$surat->file_path) }}"
                            target="_blank"
                            class="btn btn-outline-primary btn-sm">

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
 
            {{-- =========================================== --}}
            {{-- CATATAN ADMIN --}}
            {{-- =========================================== --}}
 
            <div class="col-12 mb-4">
                <label class="detail-label">
                    Catatan Verifikasi
                </label>
                <div class="detail-value detail-description">
                    {{ $surat->catatan_admin ?: 'Belum ada catatan verifikasi.' }}
                </div>
            </div>
 
        </div>
 
    </div>
 
    {{-- =========================================== --}}
    {{-- PANEL VERIFIKASI --}}
    {{-- =========================================== --}}
 
    @if($surat->status == 'diajukan')
 
        <div class="verify-panel fade-up">
 
            <div class="verify-header">
 
                <h4>
 
                    <i class="bi bi-clipboard-check text-primary me-2"></i>
 
                    Verifikasi Surat
 
                </h4>
 
                <p class="text-muted mb-0">
 
                    Verifikasi surat yang lengkap atau kembalikan kepada pegawai untuk diperbaiki.
 
                </p>
 
            </div>
 
            <form
                action="{{ route('admin.surat.masuk.setujui',$surat->id) }}"
                method="POST"
                class="verify-form">
 
                @csrf
 
                <div class="mb-3">
 
                    <label class="form-label fw-semibold">
 
                        Catatan (opsional)
 
                    </label>
 
                    <textarea
                        name="catatan_admin"
                        rows="3"
                        class="form-control"
                        placeholder="Tambahkan catatan verifikasi...">{{ old('catatan_admin') }}</textarea>
 
                </div>
 
                <div class="d-flex gap-2">
 
                    <button
                        type="submit"
                        class="btn btn-success px-4">
 
                        <i class="bi bi-check-circle-fill me-2"></i>
 
                        Verifikasi Surat
 
                    </button>
 
                </div>
 
            </form>
 
            <form
                action="{{ route('admin.surat.masuk.tolak',$surat->id) }}"
                method="POST"
                class="verify-form mt-3">
 
                @csrf
 
                <div class="mb-3">
 
                    <label class="form-label fw-semibold">
 
                        Catatan Perbaikan
                        <span class="text-danger">*</span>
 
                    </label>
 
                    <textarea
                        name="catatan_admin"
                        rows="3"
                        class="form-control"
                        placeholder="Jelaskan bagian yang perlu diperbaiki...">{{ old('catatan_admin') }}</textarea>
 
                </div>
 
                <div class="d-flex gap-2">
 
                    <button
                        type="submit"
                        class="btn btn-outline-danger px-4"
                        onclick="return confirm('Kembalikan surat ini kepada pegawai?')">
 
                        <i class="bi bi-x-circle-fill me-2"></i>
 
                        Kembalikan Surat
 
                    </button>
 
                </div>
 
            </form>
 
        </div>
 
    @endif

    @if($surat->status == 'diverifikasi')
        <div class="verify-panel fade-up">
            <div class="verify-header">
                <h4><i class="bi bi-send-check text-primary me-2"></i>Teruskan ke Pimpinan</h4>
                <p class="text-muted mb-0">Tahap ini hanya mencatat penyaluran oleh admin; tidak membuat akun atau alur kerja pimpinan.</p>
            </div>
            <form action="{{ route('admin.surat.masuk.teruskan-pimpinan', $surat->id) }}" method="POST" class="verify-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Metode penerusan</label>
                    <select name="metode_penerusan" class="form-select" required>
                        <option value="fisik">Dokumen fisik</option>
                        <option value="email">Email</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan pengantar</label>
                    <textarea name="catatan_pengantar" rows="3" class="form-control" maxlength="2000">{{ old('catatan_pengantar') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Catat surat ini sebagai telah diteruskan ke pimpinan?')">
                    <i class="bi bi-send-fill me-2"></i>Teruskan ke Pimpinan
                </button>
            </form>
        </div>
    @endif
 
    <div class="detail-footer">

        <a
            href="{{ route('admin.surat.masuk.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

        <a
            href="{{ route('admin.surat.masuk.edit',$surat->id) }}"
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

    width:70px;

    height:70px;

    border-radius:18px;

    background:#e8f1ff;

    color:#0F4C81;

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

    font-size:13px;

    font-weight:700;

    color:#64748b;

    margin-bottom:8px;

    text-transform:uppercase;

    letter-spacing:.5px;

}

.detail-value{

    background:#f8fafc;

    border:1px solid #e2e8f0;

    border-radius:12px;

    padding:14px 16px;

    color:#1e293b;

    min-height:52px;

    display:flex;

    align-items:center;

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
 
 .verify-panel{
 
     background:#f8fafc;
 
     border:1px solid #e2e8f0;
 
     border-radius:20px;
 
     padding:28px 30px;
 
     margin-bottom:24px;
 
 }
 
 .verify-header{
 
     margin-bottom:20px;
 
 }
 
 .verify-header h4{
 
     font-weight:700;
 
     color:#1e293b;
 
     margin-bottom:6px;
 
 }
 
 .verify-form label{
 
     color:#334155;
 
 }
 
 .verify-form .form-control{
 
     border-radius:12px;
 
     border:1px solid #d9dee5;
 
 }

.badge{

    border-radius:999px;

    padding:8px 12px;

    font-size:12px;

}

@media(max-width:768px){

    .detail-header{

        flex-direction:column;

        text-align:center;

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
