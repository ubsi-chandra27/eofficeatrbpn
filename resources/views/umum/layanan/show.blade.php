@extends('layouts.umum')
@section('title', $service['title'])

@section('content')
<div class="service-detail-page">
    <a href="{{ route('umum.layanan.index') }}" class="back-link"><i class="bi bi-arrow-left"></i> Kembali ke daftar layanan</a>

    <section class="service-hero">
        <div class="service-hero-icon"><i class="bi {{ $service['icon'] }}"></i></div>
        <div><span>LAYANAN DIGITAL</span><h2>{{ $service['title'] }}</h2><p>{{ $service['description'] }}</p></div>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="detail-card">
                <h4><i class="bi bi-list-check me-2"></i>Alur {{ $service['title'] }}</h4>
                @foreach($service['steps'] as $step)
                    <div class="detail-step"><b>{{ $loop->iteration }}</b><span>{{ $step }}</span></div>
                @endforeach
            </section>
        </div>
        <div class="col-lg-5">
            <aside class="detail-card requirement-card">
                <h4><i class="bi bi-clipboard-check me-2"></i>Siapkan informasi ini</h4>
                <ul>@foreach($service['requirements'] as $requirement)<li><i class="bi bi-check-circle-fill"></i>{{ $requirement }}</li>@endforeach</ul>
                <a href="{{ route('umum.surat.create', ['kategori' => $service['title']]) }}" class="btn btn-primary w-100">{{ $service['action'] }} <i class="bi bi-arrow-right ms-1"></i></a>
            </aside>
        </div>
    </div>

    <div class="service-note"><i class="bi bi-shield-check"></i><div><strong>Pengajuan tercatat pada akun Anda</strong><span>Status dan catatan tindak lanjut dapat dipantau melalui menu Surat Saya.</span></div></div>
</div>
@endsection

@push('styles')
<style>
.service-detail-page{max-width:1050px;margin:auto}.back-link{display:inline-flex;align-items:center;gap:7px;margin-bottom:18px;color:#64748b;font-weight:600}.service-hero{display:flex;align-items:center;gap:20px;background:linear-gradient(135deg,#0f5d9b,#1772ba);color:#fff;padding:30px;border-radius:20px;margin-bottom:22px}.service-hero-icon{width:72px;height:72px;flex:0 0 auto;border-radius:18px;background:rgba(255,255,255,.16);display:grid;place-items:center;font-size:32px}.service-hero span{font-size:11px;letter-spacing:1.4px;opacity:.8}.service-hero h2{font-size:28px;font-weight:750;margin:5px 0}.service-hero p{margin:0;opacity:.88}.detail-card{height:100%;background:#fff;border:1px solid #e4eaf1;border-radius:18px;padding:25px}.detail-card h4{font-size:17px;font-weight:700;margin-bottom:20px}.detail-step{display:flex;align-items:center;gap:13px;padding:13px 0;border-bottom:1px solid #edf1f5}.detail-step:last-child{border-bottom:0}.detail-step b{width:31px;height:31px;display:grid;place-items:center;border-radius:50%;background:#eaf4ff;color:#1264a3}.requirement-card ul{padding:0;list-style:none;margin-bottom:23px}.requirement-card li{display:flex;gap:9px;margin:12px 0;color:#526174}.requirement-card li i{color:#178b59}.service-note{display:flex;align-items:center;gap:13px;background:#edf6ff;color:#15598d;border-radius:14px;padding:16px 19px;margin-top:20px}.service-note>i{font-size:24px}.service-note strong,.service-note span{display:block}.service-note span{font-size:13px;color:#557187}@media(max-width:600px){.service-hero{align-items:flex-start;padding:22px}.service-hero-icon{width:54px;height:54px}.service-hero h2{font-size:22px}}
</style>
@endpush
