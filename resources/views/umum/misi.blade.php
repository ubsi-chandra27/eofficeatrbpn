@extends('layouts.umum')
@section('title','Misi')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero orange detail-hero-split">
        <div>
            <span class="section-label">MISI</span>
            <h1>Komitmen pelayanan</h1>
            <p>Misi organisasi menjadi dasar penyelenggaraan layanan pertanahan dan penataan ruang yang produktif, berkelanjutan, berkeadilan, dan berstandar dunia.</p>
        </div>
        <div class="detail-visual-card icon-only">
            <i class="bi bi-list-check"></i>
            <strong>Pelayanan yang produktif</strong>
            <span>Misi menjadi panduan kerja agar pelayanan terus tertib dan tepat sasaran.</span>
        </div>
    </section>

    <section class="mission-detail-list">
        <article>
            <b>01</b>
            <div>
                <h3>Penataan ruang dan pengelolaan pertanahan</h3>
                <p>Menyelenggarakan penataan ruang dan pengelolaan pertanahan yang produktif, berkelanjutan, dan berkeadilan.</p>
            </div>
        </article>
        <article>
            <b>02</b>
            <div>
                <h3>Pelayanan berstandar dunia</h3>
                <p>Menyelenggarakan pelayanan pertanahan dan penataan ruang yang berstandar dunia.</p>
            </div>
        </article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
