@extends('layouts.admin')

@section('title', 'Informasi Instansi')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">
                <i class="fas fa-building me-2"></i>
                Informasi Instansi
            </h3>

            <p class="text-muted mb-0">
                Kelola identitas Kantor ATR/BPN.
            </p>

        </div>

        <a href="{{ route('admin.settings.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left me-2"></i>

            Kembali

        </a>

    </div>

    {{-- Success --}}
    @if(session('success'))

        <div class="alert alert-success shadow-sm">

            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') }}

        </div>

    @endif

    {{-- Error --}}
    @if($errors->any())

        <div class="alert alert-danger shadow-sm">

            <strong>

                <i class="fas fa-exclamation-circle me-2"></i>

                Terjadi Kesalahan

            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="row">

        {{-- FORM --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-edit me-2"></i>

                        Data Instansi

                    </h5>

                </div>

                <div class="card-body">

                    <form action="#" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Nama Instansi

                            </label>

                            <input
                                type="text"
                                name="nama_instansi"
                                class="form-control"
                                value="Kantor ATR/BPN Kabupaten"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Alamat

                            </label>

                            <textarea
                                name="alamat"
                                rows="4"
                                class="form-control">Jl. Contoh No.123</textarea>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Telepon

                                </label>

                                <input
                                    type="text"
                                    name="telepon"
                                    class="form-control"
                                    value="(021) 12345678">

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Email

                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="atrbpn@example.go.id">

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Website

                            </label>

                            <input
                                type="url"
                                name="website"
                                class="form-control"
                                value="https://www.atrbpn.go.id">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Kepala Kantor

                            </label>

                            <input
                                type="text"
                                name="kepala"
                                class="form-control"
                                value="Nama Kepala Kantor">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Upload Logo

                            </label>

                            <input
                                type="file"
                                name="logo"
                                class="form-control">

                            <small class="text-muted">

                                Format JPG, PNG maksimal 2 MB.

                            </small>

                        </div>

                        <hr>

                        <div class="text-end">

                            <button
                                class="btn btn-success">

                                <i class="fas fa-save me-2"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        {{-- PREVIEW --}}
        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-eye me-2"></i>

                        Preview

                    </h5>

                </div>

                <div class="card-body text-center">

                    <img
                        src="{{ asset('images/logo-eoffice.svg') }}"
                        class="img-fluid mb-3"
                        style="max-height:150px;">

                    <h5 class="fw-bold">

                        KEMENTERIAN AGRARIA DAN TATA RUANG BADAN PERTANAHAN NASIONAL

                    </h5>

                    <hr>

                    <p class="mb-1">

                        <i class="fas fa-map-marker-alt text-danger"></i>

                        Jl. Sisingamangaraja No.2, Selong, Kec. Kby. Baru, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12110

                    </p>

                    <p class="mb-1">

                        <i class="fas fa-phone text-success"></i>

                        0811-1068-0000 (Chat Only)

                    </p>

                    <p class="mb-1">

                        <i class="fas fa-envelope text-primary"></i>

                        surat@atrbpn.go.id

                    </p>

                    <p>

                        <i class="fas fa-globe text-info"></i>

                        www.atrbpn.go.id

                    </p>

                </div>

            </div>

            <div class="card shadow-sm border-0 mt-4">

                <div class="card-header bg-info text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-info-circle me-2"></i>

                        Informasi

                    </h5>

                </div>

                <div class="card-body">

                    <ul class="mb-0">

                        <li>Logo akan tampil pada dashboard.</li>

                        <li>Logo akan muncul pada laporan PDF.</li>

                        <li>Nama instansi digunakan pada kop surat.</li>

                        <li>Alamat dicetak pada surat keluar.</li>

                        <li>Email menjadi kontak resmi instansi.</li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
