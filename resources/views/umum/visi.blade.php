@extends('layouts.umum')
@section('title','Visi')
@section('content')
<div class="public-info-detail">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero green">
        <span class="section-label">VISI</span>
        <h1>Arah pelayanan organisasi</h1>
        <p>Terwujudnya penataan ruang dan pengelolaan pertanahan yang terpercaya dan berstandar dunia dalam melayani masyarakat untuk mendukung Indonesia maju yang berdaulat, mandiri, dan berkepribadian berlandaskan gotong royong.</p>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel">
            <i class="bi bi-eye-fill"></i>
            <h3>Terpercaya</h3>
            <p>Pelayanan diarahkan agar setiap proses administrasi dapat dipantau dan dipertanggungjawabkan.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-globe2"></i>
            <h3>Berstandar dunia</h3>
            <p>Pengelolaan dokumen dan pelayanan dikembangkan agar lebih tertib, konsisten, dan mudah digunakan.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-people-fill"></i>
            <h3>Melayani masyarakat</h3>
            <p>Setiap fitur diarahkan untuk membantu masyarakat memperoleh informasi dan layanan administrasi secara jelas.</p>
        </article>
    </section>
</div>
@endsection
