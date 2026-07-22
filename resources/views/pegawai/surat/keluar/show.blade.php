@extends('layouts.pegawai')

@section('title','Detail Surat Keluar')

@section('content')


<style>

.page-title{
    font-size:26px;
    font-weight:700;
    color:#0d6efd;
}

.card-custom{
    border:none;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.card-header-custom{
    background:#0d6efd;
    color:#fff;
    padding:18px 25px;
}

.card-header-custom h5{
    margin:0;
    font-weight:700;
}

.info-label{
    font-size:13px;
    font-weight:700;
    color:#6c757d;
    margin-bottom:5px;
    text-transform:uppercase;
}

.info-value{
    font-size:15px;
    color:#212529;
    font-weight:500;
}

.info-box{
    background:#f8f9fa;
    border-radius:10px;
    padding:15px;
    margin-bottom:20px;
}

.badge-status{
    font-size:14px;
    padding:8px 14px;
}

</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="page-title">

                <i class="bi bi-envelope-paper-fill"></i>

                Detail Surat Keluar

            </h2>

            <small class="text-muted">

                Informasi lengkap surat keluar.

            </small>

        </div>

        <div>


        </div>

    </div>

    <div class="card card-custom">

        <div class="card-header-custom">

            <h5>

                <i class="bi bi-file-earmark-text-fill"></i>

                INFORMASI SURAT KELUAR

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Nomor Surat

                        </div>

                        <div class="info-value">

                            {{ $surat->nomor_surat }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Tanggal Surat

                        </div>

                        <div class="info-value">

                            {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Kode Surat

                        </div>

                        <div class="info-value">

                            {{ $surat->kode_surat ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Nomor Agenda

                        </div>

                        <div class="info-value">

                            {{ $surat->nomor_agenda ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Tujuan Surat

                        </div>

                        <div class="info-value">

                            {{ $surat->tujuan_surat ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Metode Pengiriman

                        </div>

                        <div class="info-value">

                            {{ $surat->metode ?? '-' }}

                        </div>

                    </div>

                </div>

                                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Judul Surat

                        </div>

                        <div class="info-value">

                            {{ $surat->judul_surat ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Perihal

                        </div>

                        <div class="info-value">

                            {{ $surat->perihal ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Status Surat

                        </div>

                        <div class="info-value">

                            @php

                                $badge = 'secondary';

                                switch(strtolower($surat->status)){

                                    case 'menunggu':
                                    case 'diajukan':
                                        $badge='warning';
                                        break;

                                    case 'diverifikasi':
                                        $badge='primary';
                                        break;

                                    case 'diteruskan_ke_pimpinan':
                                        $badge='success';
                                        break;

                                    case 'dikembalikan':
                                        $badge='danger';
                                        break;

                                    default:
                                        $badge='secondary';
                                        break;

                                }

                            @endphp

                            <span class="badge bg-{{ $badge }} badge-status">

                                {{ $surat->status_label }}

                            </span>

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Prioritas

                        </div>

                        <div class="info-value">

                            @if($surat->is_priority)

                                <span class="badge bg-danger">

                                    Prioritas

                                </span>

                            @else

                                <span class="badge bg-success">

                                    Normal

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">

                            Dibuat Oleh

                        </div>

                        <div class="info-value">

                            {{ $surat->user->name ?? '-' }}

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Deskripsi Surat

                        </div>

                        <div class="info-value">

                            {!! nl2br(e($surat->deskripsi ?? '-')) !!}

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Lampiran Surat

                        </div>

                        <div class="info-value">

                            @if($surat->file_path)

                                <a href="{{ asset('storage/'.$surat->file_path) }}"
                                   target="_blank"
                                   class="btn btn-success">

                                    <i class="bi bi-file-earmark-pdf-fill"></i>

                                    Lihat Lampiran

                                </a>

                            @else

                                <span class="text-muted">

                                    Tidak ada lampiran

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Catatan Admin

                        </div>

                        <div class="info-value">

                            {!! nl2br(e($surat->catatan_admin ?? '-')) !!}

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Tanggal Dibuat

                        </div>

                        <div class="info-value">

                            {{ $surat->created_at->translatedFormat('d F Y H:i') }}

                        </div>

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="info-box">

                        <div class="info-label">

                            Terakhir Diubah

                        </div>

                        <div class="info-value">

                            {{ $surat->updated_at->translatedFormat('d F Y H:i') }}

                        </div>

                    </div>

                </div>

            </div>

                        <hr class="my-4">

            {{-- Tujuan Disposisi --}}
            <div class="mb-4">

                <h5 class="fw-bold text-primary mb-3">

                    <i class="bi bi-diagram-3-fill"></i>

                    Tujuan Disposisi

                </h5>

                @if(isset($disposisiTujuan) && $disposisiTujuan->count())

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">

                            <thead class="table-primary">

                                <tr>

                                    <th width="5%">No</th>

                                    <th>Nama Pegawai</th>

                                    <th>Jabatan</th>

                                    <th>Unit Kerja</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($disposisiTujuan as $item)

                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $item->pegawai->nama ?? '-' }}</td>

                                        <td>{{ $item->pegawai->jabatan->nama ?? '-' }}</td>

                                        <td>{{ $item->pegawai->unitKerja->nama ?? '-' }}</td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="alert alert-light border">

                        Belum ada tujuan disposisi.

                    </div>

                @endif

            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="mb-4">

                <h5 class="fw-bold text-primary mb-3">

                    <i class="bi bi-clock-history"></i>

                    Riwayat Aktivitas

                </h5>

                @if(isset($surat->logs) && $surat->logs->count())

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="table-secondary">

                                <tr>

                                    <th width="25%">Tanggal</th>

                                    <th>Aktivitas</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($surat->logs as $log)

                                    <tr>

                                        <td>

                                            {{ $log->created_at->translatedFormat('d F Y H:i') }}

                                        </td>

                                        <td>

                                            {{ $log->aktivitas ?? $log->keterangan ?? '-' }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="alert alert-light border">

                        Belum ada riwayat aktivitas.

                    </div>

                @endif

            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('pegawai.surat-keluar.index') }}"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left-circle"></i>

                    Kembali

                </a>

                @if(in_array($surat->status, ['draft', 'dikembalikan', 'Menunggu']))
                    <a href="{{ route('pegawai.surat-keluar.edit', $surat->id) }}"
                       class="btn btn-warning">
                        <i class="bi bi-pencil-square"></i>
                        Edit
                    </a>

                    <form action="{{ route('pegawai.surat-keluar.kirim', $surat->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Kirim surat ini ke admin?')">
                            <i class="bi bi-send-fill"></i>
                            Kirim ke Admin
                        </button>
                    </form>

                    <form action="{{ route('pegawai.surat-keluar.destroy', $surat->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus surat ini?')">
                            <i class="bi bi-trash"></i>
                            Hapus
                        </button>
                    </form>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection
