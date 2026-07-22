@extends('layouts.pegawai')

@section('title','Detail Surat Masuk')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-envelope-open-fill text-primary me-2"></i>

            Detail Surat Masuk

        </h2>

        <p class="text-muted mb-0">

            Informasi lengkap surat yang telah diregistrasi.

        </p>

    </div>

    <a href="{{ route('pegawai.surat-masuk.index') }}"
       class="btn btn-light border">

        <i class="bi bi-arrow-left-circle me-2"></i>

        Kembali

    </a>

</div>



<div class="card shadow-sm border-0 mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-8">

<h4 class="fw-bold mb-2">

{{ $surat->judul_surat }}

</h4>

<p class="text-muted mb-1">

Nomor Surat

<strong>

{{ $surat->nomor_surat }}

</strong>

</p>

<p class="text-muted">

Tanggal Surat :

{{ optional($surat->tanggal_surat)->format('d F Y') }}

</p>

</div>

<div class="col-md-4 text-md-end">

@if($surat->status=='draft')

<span class="badge bg-secondary px-3 py-2">

Draft

</span>

@elseif($surat->status=='diajukan')

<span class="badge bg-warning text-dark px-3 py-2">

Diajukan

</span>

@elseif($surat->status=='diverifikasi')

<span class="badge bg-info px-3 py-2">

Diverifikasi

</span>

@elseif($surat->status=='diteruskan_ke_pimpinan')

<span class="badge bg-success px-3 py-2">

Diteruskan ke Pimpinan

</span>

@else

<span class="badge bg-primary px-3 py-2">

{{ $surat->status_label }}

</span>

@endif

</div>

</div>

</div>

</div>



<div class="card shadow-sm border-0 mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-journal-text me-2"></i>

Informasi Registrasi

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-4">

<label class="text-muted">

Nomor Surat

</label>

<h6>

{{ $surat->nomor_surat }}

</h6>

</div>

<div class="col-md-6 mb-4">

<label class="text-muted">

Tanggal Surat

</label>

<h6>

{{ optional($surat->tanggal_surat)->format('d F Y') }}

</h6>

</div>

<div class="col-md-6 mb-4">

<label class="text-muted">

Nomor Agenda

</label>

<h6>

{{ $surat->nomor_agenda ?: '-' }}

</h6>

</div>

<div class="col-md-6 mb-4">

<label class="text-muted">

Kode Surat

</label>

<h6>

{{ $surat->kode_surat ?: '-' }}

</h6>

</div>

</div>

</div>

</div>

{{-- ================= INFORMASI PENGIRIM ================= --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">

            <i class="bi bi-building me-2"></i>

            Informasi Pengirim

        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Asal Surat

                </label>

                <h6>

                    {{ $surat->asal_surat ?: '-' }}

                </h6>

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Tujuan Surat

                </label>

                <h6>

                    {{ $surat->tujuan_surat ?: '-' }}

                </h6>

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Penandatangan

                </label>

                <h6>

                    {{ $surat->penandatangan ?: '-' }}

                </h6>

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Lampiran

                </label>

                <h6>

                    {{ $surat->lampiran ?: '-' }}

                </h6>

            </div>

        </div>

    </div>

</div>



{{-- ================= ISI SURAT ================= --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-info text-white">

        <h5 class="mb-0">

            <i class="bi bi-file-earmark-text me-2"></i>

            Isi Surat

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-4">

            <label class="text-muted">

                Judul Surat

            </label>

            <h5 class="fw-bold">

                {{ $surat->judul_surat ?: '-' }}

            </h5>

        </div>



        <div class="mb-4">

            <label class="text-muted">

                Perihal

            </label>

            <p class="mb-0">

                {{ $surat->perihal ?: '-' }}

            </p>

        </div>



        <div>

            <label class="text-muted">

                Catatan Pegawai

            </label>

            <div class="border rounded-3 bg-light p-3">

                {!! nl2br(e($surat->deskripsi ?: '-')) !!}

            </div>

        </div>

    </div>

</div>

{{-- ================= INFORMASI PENERIMAAN ================= --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-warning">

        <h5 class="mb-0 text-dark">

            <i class="bi bi-inbox-fill me-2"></i>

            Informasi Penerimaan

        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Metode Penerimaan

                </label>

                <h6>

                    {{ $surat->metode ?: '-' }}

                </h6>

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Prioritas

                </label>

                @if($surat->is_priority)

                    <span class="badge bg-danger">

                        <i class="bi bi-exclamation-triangle-fill me-1"></i>

                        Prioritas Tinggi

                    </span>

                @else

                    <span class="badge bg-success">

                        Normal

                    </span>

                @endif

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Tanggal Input

                </label>

                <h6>

                    {{ $surat->created_at->format('d F Y H:i') }}

                </h6>

            </div>

            <div class="col-md-6 mb-4">

                <label class="text-muted">

                    Terakhir Diubah

                </label>

                <h6>

                    {{ $surat->updated_at->format('d F Y H:i') }}

                </h6>

            </div>

        </div>

    </div>

</div>



{{-- ================= LAMPIRAN ================= --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-secondary text-white">

        <h5 class="mb-0">

            <i class="bi bi-paperclip me-2"></i>

            Lampiran Surat

        </h5>

    </div>

    <div class="card-body">

        @if($surat->file_path)

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h6 class="mb-1">

                        File Surat

                    </h6>

                    <small class="text-muted">

                        {{ basename($surat->file_path) }}

                    </small>

                </div>

                <div class="d-flex gap-2 mt-3 mt-md-0">

                    <a href="{{ asset('storage/'.$surat->file_path) }}"
                       target="_blank"
                       class="btn btn-primary">

                        <i class="bi bi-eye-fill me-2"></i>

                        Lihat

                    </a>

                    <a href="{{ asset('storage/'.$surat->file_path) }}"
                       download
                       class="btn btn-success">

                        <i class="bi bi-download me-2"></i>

                        Download

                    </a>

                </div>

            </div>

        @else

            <div class="text-center py-4">

                <i class="bi bi-file-earmark-x display-4 text-muted"></i>

                <p class="text-muted mt-3 mb-0">

                    Tidak ada lampiran surat.

                </p>

            </div>

        @endif

    </div>

</div>



{{-- ================= RIWAYAT SURAT ================= --}}

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">

            <i class="bi bi-clock-history me-2"></i>

            Riwayat Surat

        </h5>

    </div>

    <div class="card-body">

        <div class="timeline">

            <div class="timeline-item">

                <div class="timeline-icon bg-primary">

                    <i class="bi bi-plus-circle-fill"></i>

                </div>

                <div class="timeline-content">

                    <h6>

                        Surat Diregistrasi

                    </h6>

                    <small class="text-muted">

                        {{ $surat->created_at->format('d F Y H:i') }}

                    </small>

                </div>

            </div>



            @if($surat->updated_at != $surat->created_at)

            <div class="timeline-item">

                <div class="timeline-icon bg-warning">

                    <i class="bi bi-pencil-fill"></i>

                </div>

                <div class="timeline-content">

                    <h6>

                        Surat Diperbarui

                    </h6>

                    <small class="text-muted">

                        {{ $surat->updated_at->format('d F Y H:i') }}

                    </small>

                </div>

            </div>

            @endif



            <div class="timeline-item">

                <div class="timeline-icon bg-success">

                    <i class="bi bi-check-circle-fill"></i>

                </div>

                <div class="timeline-content">

                    <h6>

                        Status Saat Ini

                    </h6>

                    <span class="badge bg-primary">

                        {{ $surat->status_label }}

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ================= AKSI ================= --}}

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="d-flex justify-content-between flex-wrap gap-2">

            <a href="{{ route('pegawai.surat-masuk.index') }}"
               class="btn btn-light border">

                <i class="bi bi-arrow-left-circle me-2"></i>

                Kembali

            </a>

            <div class="d-flex gap-2">

                @if(in_array($surat->status, ['draft', 'dikembalikan']))

                    <a href="{{ route('pegawai.surat-masuk.edit',$surat->id) }}"
                       class="btn btn-warning">

                        <i class="bi bi-pencil-square me-2"></i>

                        Edit

                    </a>

                    <form action="{{ route('pegawai.surat-masuk.kirim',$surat->id) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-send-fill me-2"></i>

                            Kirim ke Admin

                        </button>

                    </form>

                @endif

                @if($surat->file_path)

                    <a href="{{ asset('storage/'.$surat->file_path) }}"
                       download
                       class="btn btn-success">

                        <i class="bi bi-download me-2"></i>

                        Download

                    </a>

                @endif

                <button
                    onclick="window.print()"
                    class="btn btn-dark">

                    <i class="bi bi-printer-fill me-2"></i>

                    Cetak

                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

.page-header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;

}

.card{

border-radius:18px;
overflow:hidden;
box-shadow:0 8px 25px rgba(15,76,129,.08);

}

.card-header{

padding:18px 25px;
font-weight:700;

}

.card-body{

padding:30px;

}

.timeline{

position:relative;
padding-left:25px;

}

.timeline::before{

content:'';
position:absolute;
left:12px;
top:0;
bottom:0;
width:2px;
background:#dbe4ef;

}

.timeline-item{

position:relative;
padding-left:35px;
margin-bottom:25px;

}

.timeline-icon{

position:absolute;
left:-2px;
top:0;
width:28px;
height:28px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
color:#fff;
font-size:13px;

}

.timeline-content h6{

margin-bottom:5px;
font-weight:700;

}

label.text-muted{

font-size:13px;
font-weight:600;
display:block;
margin-bottom:5px;

}

h6{

font-weight:600;
color:#1f2937;

}

.badge{

font-size:13px;
padding:8px 14px;
border-radius:20px;

}

.btn{

border-radius:12px;
padding:10px 18px;

}

@media(max-width:768px){

.page-header{

flex-direction:column;
align-items:flex-start;
gap:15px;

}

.card-body{

padding:20px;

}

.d-flex.justify-content-between{

flex-direction:column;

}

.d-flex.gap-2{

width:100%;
flex-direction:column;

}

.d-flex.gap-2 .btn{

width:100%;

}

.d-flex.gap-2 form{

width:100%;

}

.d-flex.gap-2 form button{

width:100%;

}

}

@media print{

.page-header .btn,
button,
form{

display:none!important;

}

body{

background:#fff;

}

.card{

box-shadow:none!important;
border:1px solid #ddd;

}

}

</style>

@endpush
