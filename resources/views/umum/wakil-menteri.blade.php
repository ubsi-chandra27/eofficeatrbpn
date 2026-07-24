@extends('layouts.umum')
@section('title', 'Informasi Wakil Menteri')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="profile-detail-card">
        <div class="profile-detail-photo">
            <img src="{{ asset('images/wakil-menteri.jpg') }}" alt="Foto Wakil Menteri">
        </div>
        <div class="profile-detail-copy">
            <span class="section-label">WAKIL PIMPINAN ORGANISASI</span>
            <h1>Wakil Menteri</h1>
            <p>Wakil Menteri membantu pelaksanaan tugas Menteri, mendukung koordinasi kebijakan, dan memperkuat pengawasan pelaksanaan program organisasi.</p>
            <div class="info-pill-grid">
                <span><i class="bi bi-person-check"></i>Mendukung pimpinan</span>
                <span><i class="bi bi-clipboard-check"></i>Pengawasan program</span>
                <span><i class="bi bi-graph-up-arrow"></i>Peningkatan layanan</span>
            </div>
            <div class="detail-note">
                <i class="bi bi-info-circle"></i>
                <span>Informasi profil ditampilkan berdasarkan data yang tersedia pada sistem.</span>
            </div>
        </div>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel"><i class="bi bi-briefcase"></i><h3>Membantu pelaksanaan tugas</h3><p>Memberikan dukungan strategis terhadap kebijakan dan kegiatan organisasi.</p></article>
        <article class="detail-panel"><i class="bi bi-arrow-repeat"></i><h3>Memperkuat koordinasi</h3><p>Mendorong komunikasi lintas unit agar agenda pelayanan berjalan efektif.</p></article>
        <article class="detail-panel"><i class="bi bi-check2-circle"></i><h3>Mengawal tindak lanjut</h3><p>Mendukung pemantauan program agar setiap layanan dapat diselesaikan dengan baik.</p></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
