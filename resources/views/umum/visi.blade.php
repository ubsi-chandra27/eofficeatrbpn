@extends('layouts.umum')
@section('title','Visi')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero green detail-hero-split">
        <div>
            <span class="section-label">VISI</span>
            <h1>Arah pelayanan organisasi</h1>
            <p>Terwujudnya penataan ruang dan pengelolaan pertanahan yang terpercaya dan berstandar dunia dalam melayani masyarakat untuk mendukung Indonesia maju yang berdaulat, mandiri, dan berkepribadian berlandaskan gotong royong.</p>
        </div>
        <div class="detail-visual-card icon-only">
            <i class="bi bi-eye-fill"></i>
            <strong>Terpercaya dan berstandar dunia</strong>
            <span>Visi menjadi arah besar dalam menjaga kualitas pelayanan kepada masyarakat.</span>
        </div>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel"><i class="bi bi-shield-check"></i><h3>Terpercaya</h3><p>Pelayanan diarahkan agar setiap proses administrasi dapat dipantau dan dipertanggungjawabkan.</p></article>
        <article class="detail-panel"><i class="bi bi-globe2"></i><h3>Berstandar dunia</h3><p>Pengelolaan dokumen dan pelayanan dikembangkan agar lebih tertib, konsisten, dan mudah digunakan.</p></article>
        <article class="detail-panel"><i class="bi bi-people-fill"></i><h3>Melayani masyarakat</h3><p>Setiap fitur diarahkan untuk membantu masyarakat memperoleh informasi dan layanan administrasi secara jelas.</p></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
