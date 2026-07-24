@extends('layouts.umum')
@section('title','Profil Instansi')
@section('content')
<div class="public-info-detail public-detail-shell">
    <a href="{{ route('umum.dashboard') }}" class="detail-back"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>

    <section class="detail-hero blue detail-hero-split">
        <div>
            <span class="section-label">PROFIL INSTANSI</span>
            <h1>{{ \App\Models\Setting::getValue('app_name','E-Office') }}</h1>
            <p>{{ \App\Models\Setting::getValue('app_subtitle','Administrasi Digital') }} adalah ruang layanan persuratan digital yang membantu masyarakat mengirim pengajuan, melacak pemeriksaan admin, dan menyimpan histori proses secara tertib.</p>
        </div>
        <div class="detail-visual-card">
            <img src="{{ asset('images/logo-eoffice.svg') }}" alt="Logo E-Office">
            <strong>Administrasi Digital</strong>
            <span>Pelayanan surat lebih rapi, transparan, dan mudah dipantau.</span>
        </div>
    </section>

    <section class="detail-content-grid">
        <article class="detail-panel">
            <i class="bi bi-send-check"></i>
            <h3>Pengajuan lebih terarah</h3>
            <p>Pengguna dapat membuat pengajuan, melengkapi informasi, mengunggah lampiran, dan memantau proses dari menu Surat Saya.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-person-lock"></i>
            <h3>Akses data terkendali</h3>
            <p>Setiap akun umum hanya dapat melihat pengajuan miliknya sendiri, sedangkan petugas mengakses data sesuai kewenangan.</p>
        </article>
        <article class="detail-panel">
            <i class="bi bi-activity"></i>
            <h3>Proses mudah dipantau</h3>
            <p>Status, catatan admin, dan aktivitas terbaru tersimpan agar pengguna mengetahui posisi pengajuan secara jelas.</p>
        </article>
    </section>

    <section class="process-strip">
        <article><b>01</b><span>Lengkapi pengajuan</span></article>
        <article><b>02</b><span>Admin memverifikasi</span></article>
        <article><b>03</b><span>Pengajuan diproses</span></article>
        <article><b>04</b><span>Status diperbarui</span></article>
    </section>

    @include('umum.partials.info-navigation')
</div>
@endsection
