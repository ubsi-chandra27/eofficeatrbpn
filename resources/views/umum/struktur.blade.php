@extends('layouts.umum')
@section('title', 'Struktur Organisasi')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero blue">
        <span class="section-label">STRUKTUR ORGANISASI</span>
        <h1>Susunan unit dan hubungan koordinasi</h1>
        <p>Bagan organisasi membantu pengguna memahami susunan unit kerja, alur koordinasi, dan posisi unsur pimpinan dalam pelaksanaan pelayanan.</p>
    </section>

    <section class="large-image-panel">
        <div class="large-image-heading">
            <div>
                <h3>Bagan Organisasi</h3>
                <p>Klik gambar untuk membuka ukuran penuh di tab baru.</p>
            </div>
            <a href="{{ asset('images/struktur-organisasi.png') }}" target="_blank" class="detail-link">Buka gambar penuh <i class="bi bi-box-arrow-up-right"></i></a>
        </div>
        <a href="{{ asset('images/struktur-organisasi.png') }}" target="_blank" class="structure-image-link">
            <img src="{{ asset('images/struktur-organisasi.png') }}" alt="Bagan Struktur Organisasi">
        </a>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel"><i class="bi bi-diagram-3-fill"></i><h3>Hubungan koordinasi</h3><p>Menunjukkan keterkaitan unit dan garis koordinasi dalam penyelenggaraan tugas.</p></article>
        <article class="detail-panel"><i class="bi bi-building"></i><h3>Susunan unit</h3><p>Membantu pengguna mengenali bagian organisasi yang menangani layanan dan administrasi.</p></article>
        <article class="detail-panel"><i class="bi bi-person-workspace"></i><h3>Peran pimpinan</h3><p>Memperlihatkan posisi unsur pimpinan dan unit pendukung dalam struktur kerja.</p></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
