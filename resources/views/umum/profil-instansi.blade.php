@extends('layouts.umum')
@section('title','Profil Instansi')
@section('content')
<div class="public-info-detail">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero blue">
        <span class="section-label">PROFIL INSTANSI</span>
        <h1>{{ \App\Models\Setting::getValue('app_name','E-Office') }}</h1>
        <p>{{ \App\Models\Setting::getValue('app_subtitle','Administrasi Digital') }} adalah ruang layanan persuratan digital untuk membantu masyarakat mengirim pengajuan, melacak pemeriksaan admin, dan melihat perkembangan proses secara tertib.</p>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel">
            <i class="bi bi-building-check"></i>
            <h3>Pelayanan digital yang mudah dilacak</h3>
            <p>Sistem ini membantu proses administrasi surat menjadi lebih rapi, transparan, dan mudah dipantau oleh pemilik akun.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-shield-lock"></i>
            <h3>Data tetap terproteksi</h3>
            <p>Setiap akun umum hanya dapat melihat pengajuan dan histori miliknya sendiri, sedangkan petugas mengakses data sesuai kewenangan.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-clock-history"></i>
            <h3>Histori proses tersimpan</h3>
            <p>Status, catatan admin, lampiran, dan perubahan proses dapat dipantau melalui menu Surat Saya dan detail pengajuan.</p>
        </article>
    </section>
</div>
@endsection
