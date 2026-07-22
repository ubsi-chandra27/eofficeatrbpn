@extends('layouts.pegawai')

@section('title','Detail Disposisi')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">

                <i class="bi bi-envelope-open-fill me-2"></i>

                Detail Disposisi

            </h3>

            <p class="text-muted mb-0">

                Informasi lengkap disposisi yang Anda terima.

            </p>

        </div>

        <a href="{{ route('pegawai.disposisi.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>

    {{-- Alert --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-file-earmark-text-fill me-2"></i>

                Informasi Surat

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Nomor Surat

                    </label>

                    <div>

                        {{ $disposisi->disposisi->surat->nomor_surat ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Tanggal Surat

                    </label>

                    <div>

                        {{ optional($disposisi->disposisi->surat->tanggal_surat)->format('d F Y') }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Judul Surat

                    </label>

                    <div>

                        {{ $disposisi->disposisi->surat->judul_surat ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Perihal

                    </label>

                    <div>

                        {{ $disposisi->disposisi->surat->perihal ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Pengirim Disposisi

                    </label>

                    <div>

                        {{ $disposisi->disposisi->pengirim->name ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Prioritas

                    </label>

                    <div>

                        @if($disposisi->disposisi->prioritas=='Tinggi')

                            <span class="badge bg-danger">

                                Tinggi

                            </span>

                        @elseif($disposisi->disposisi->prioritas=='Sedang')

                            <span class="badge bg-warning text-dark">

                                Sedang

                            </span>

                        @else

                            <span class="badge bg-success">

                                Rendah

                            </span>

                        @endif

                    </div>

                </div>
                                <div class="col-12 mb-4">

                    <label class="fw-semibold text-muted">

                        Catatan Disposisi

                    </label>

                    <div class="border rounded bg-light p-3">

                        {!! nl2br(e($disposisi->disposisi->catatan ?? '-')) !!}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Status Disposisi

                    </label>

                    <div>

                        @if($disposisi->status == 'Belum Dibaca')

                            <span class="badge bg-warning text-dark">

                                <i class="bi bi-envelope me-1"></i>

                                Belum Dibaca

                            </span>

                        @elseif($disposisi->status == 'Sudah Dibaca')

                            <span class="badge bg-info">

                                <i class="bi bi-eye-fill me-1"></i>

                                Sudah Dibaca

                            </span>

                        @elseif($disposisi->status == 'Selesai')

                            <span class="badge bg-success">

                                <i class="bi bi-check-circle-fill me-1"></i>

                                Selesai

                            </span>

                        @else

                            <span class="badge bg-secondary">

                                {{ $disposisi->status }}

                            </span>

                        @endif

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Tanggal Disposisi

                    </label>

                    <div>

                        {{ optional($disposisi->disposisi->tanggal_disposisi)->format('d F Y') }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-semibold text-muted">

                        Dibaca Pada

                    </label>

                    <div>

                        @if($disposisi->dibaca_pada)

                            {{ \Carbon\Carbon::parse($disposisi->dibaca_pada)->format('d F Y H:i') }}

                        @else

                            <span class="text-muted">

                                Belum dibaca

                            </span>

                        @endif

                    </div>

                </div>

                <div class="col-md-6 mb-4">

                    <label class="fw-semibold text-muted">

                        Diselesaikan Pada

                    </label>

                    <div>

                        @if($disposisi->selesai_pada)

                            {{ \Carbon\Carbon::parse($disposisi->selesai_pada)->format('d F Y H:i') }}

                        @else

                            <span class="text-muted">

                                Belum diselesaikan

                            </span>

                        @endif

                    </div>

                </div>

                <div class="col-12">

                    <label class="fw-semibold text-muted">

                        Lampiran Surat

                    </label>

                    <div>

                        @if($disposisi->disposisi->surat->file_path)

                            <a href="{{ asset('storage/'.$disposisi->disposisi->surat->file_path) }}"
                               target="_blank"
                               class="btn btn-outline-primary">

                                <i class="bi bi-file-earmark-pdf-fill me-1"></i>

                                Lihat / Download Surat

                            </a>

                        @else

                            <span class="text-muted">

                                Tidak ada lampiran.

                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

                <div class="card-footer bg-light">

            <div class="d-flex justify-content-between flex-wrap gap-2">

                <a href="{{ route('pegawai.disposisi.index') }}"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left-circle me-1"></i>

                    Kembali

                </a>

                <div class="d-flex gap-2">

                    @if($disposisi->status == 'Belum Dibaca')

                        <form
                            action="{{ route('pegawai.disposisi.dibaca',$disposisi->id) }}"
                            method="POST">

                            @csrf

                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-info">

                                <i class="bi bi-book-half me-1"></i>

                                Tandai Sudah Dibaca

                            </button>

                        </form>

                    @endif

                    @if($disposisi->status != 'Selesai')

                        <form
                            action="{{ route('pegawai.disposisi.selesai',$disposisi->id) }}"
                            method="POST">

                            @csrf

                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-success"
                                onclick="return confirm('Apakah disposisi ini sudah selesai dikerjakan?')">

                                <i class="bi bi-check-circle-fill me-1"></i>

                                Selesaikan

                            </button>

                        </form>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
