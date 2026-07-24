@extends('layouts.umum')
@section('title','Makna Logo Kementerian')
@section('content')
<div class="public-info-detail">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero blue">
        <span class="section-label">IDENTITAS ORGANISASI</span>
        <h1>Makna lambang kementerian</h1>
        <p>Setiap unsur pada lambang menggambarkan hubungan antara ruang, tanah, kesejahteraan, pelayanan, dan komitmen pengelolaan agraria yang berintegritas.</p>
    </section>

    <section class="logo-detail-grid">
        <article><i class="bi bi-flower1"></i><h3>Empat Butir Padi</h3><p>Kemakmuran dan kesejahteraan melalui tujuan pertanahan: kemakmuran, keadilan, keberlanjutan, dan harmoni sosial.</p></article>
        <article><i class="bi bi-globe2"></i><h3>Lingkaran Bumi</h3><p>Bumi sebagai sumber penghidupan manusia yang berhubungan langsung dengan tanah, air, dan sumber daya alam.</p></article>
        <article><i class="bi bi-bullseye"></i><h3>Sumbu</h3><p>Poros keseimbangan yang mencerminkan landasan konstitusional pengelolaan agraria dan pertanahan.</p></article>
        <article><i class="bi bi-buildings"></i><h3>Bangunan dan Pohon</h3><p>Kekuatan, keteguhan, keberlanjutan, dan konsistensi dalam melayani serta menuntaskan kewajiban.</p></article>
        <article><i class="bi bi-water"></i><h3>Gelombang Hijau dan Biru</h3><p>Lingkungan yang terjaga serta keterkaitan tugas penataan ruang dengan pemanfaatan tanah dan air.</p></article>
        <article><i class="bi bi-palette-fill"></i><h3>Makna Warna</h3><p>Hijau untuk lingkungan, kuning untuk kemakmuran, biru untuk kebijakan, merah untuk semangat, dan putih untuk kepercayaan.</p></article>
    </section>
</div>
@endsection
