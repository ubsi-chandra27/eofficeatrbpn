@extends('layouts.admin')

@section('title', 'Tentang Aplikasi')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">
                <i class="fas fa-info-circle me-2"></i>
                Tentang Aplikasi
            </h3>

            <p class="text-muted mb-0">
                Informasi mengenai Sistem Persuratan ATR/BPN.
            </p>

        </div>

        <a href="{{ route('admin.settings.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left me-2"></i>

            Kembali

        </a>

    </div>

    <div class="row">

        {{-- Informasi Aplikasi --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        <i class="fas fa-desktop me-2"></i>
                        Informasi Aplikasi
                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th width="35%">Nama Aplikasi</th>
                            <td>Sistem Persuratan ATR/BPN</td>
                        </tr>

                        <tr>
                            <th>Versi</th>
                            <td>1.0.0</td>
                        </tr>

                        <tr>
                            <th>Framework</th>
                            <td>Laravel 13</td>
                        </tr>

                        <tr>
                            <th>Bahasa Pemrograman</th>
                            <td>PHP 8.3</td>
                        </tr>

                        <tr>
                            <th>Database</th>
                            <td>MySQL</td>
                        </tr>

                        <tr>
                            <th>Frontend</th>
                            <td>Bootstrap 5, jQuery, Font Awesome</td>
                        </tr>

                        <tr>
                            <th>Developer</th>
                            <td>Tim Pengembang ATR/BPN</td>
                        </tr>

                        <tr>
                            <th>Tahun</th>
                            <td>2026</td>
                        </tr>

                    </table>

                </div>

            </div>

            {{-- Deskripsi --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Deskripsi Sistem
                    </h5>

                </div>

                <div class="card-body">

                    <p align="justify">

                        Sistem Persuratan ATR/BPN merupakan aplikasi yang
                        digunakan untuk membantu proses administrasi surat
                        masuk, surat keluar, disposisi, manajemen pegawai,
                        jabatan, unit kerja, serta penyusunan laporan secara
                        digital.

                    </p>

                    <p align="justify">

                        Sistem ini dikembangkan agar proses pengelolaan surat
                        menjadi lebih cepat, aman, terdokumentasi dengan baik,
                        dan memudahkan pencarian arsip surat.

                    </p>

                </div>

            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Logo --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body text-center">

                    <img src="{{ asset('images/logo-eoffice.svg') }}"
                         class="img-fluid mb-3"
                         style="max-height:180px;">

                    <h4 class="fw-bold">

                        ATR / BPN

                    </h4>

                    <p class="text-muted">

                        Sistem Persuratan Digital

                    </p>

                </div>

            </div>

            {{-- Statistik --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-info text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-chart-bar me-2"></i>

                        Statistik

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>

                            <td>Versi</td>

                            <td class="text-end">
                                <span class="badge bg-primary">
                                    1.0.0
                                </span>
                            </td>

                        </tr>

                        <tr>

                            <td>Status</td>

                            <td class="text-end">

                                <span class="badge bg-success">

                                    Aktif

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <td>Lisensi</td>

                            <td class="text-end">

                                Internal

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            {{-- Fitur --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-warning">

                    <h5 class="mb-0 text-dark">

                        <i class="fas fa-star me-2"></i>

                        Fitur Utama

                    </h5>

                </div>

                <div class="card-body">

                    <ul class="mb-0">

                        <li>Dashboard Administrator</li>

                        <li>Surat Masuk</li>

                        <li>Surat Keluar</li>

                        <li>Disposisi Surat</li>

                        <li>Master Pegawai</li>

                        <li>Master Jabatan</li>

                        <li>Master Unit Kerja</li>

                        <li>Pencarian Surat</li>

                        <li>Laporan Persuratan</li>

                        <li>Pengaturan Sistem</li>

                        <li>Backup Database</li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

    {{-- Footer --}}
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-body text-center">

            <h5 class="fw-bold">

                Sistem Persuratan ATR/BPN

            </h5>

            <p class="mb-1">

                © {{ date('Y') }} Kementerian Agraria dan Tata Ruang / Badan Pertanahan Nasional

            </p>

            <small class="text-muted">

                Dikembangkan menggunakan Laravel Framework.

            </small>

        </div>

    </div>

</div>

@endsection
