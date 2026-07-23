@extends('layouts.admin')
@section('title','Detail Pegawai')
@section('content')
<div class="page-header fade-up">
    <div><h2><i class="bi bi-person-vcard-fill text-primary me-2"></i>Detail Pegawai</h2><p class="text-muted mb-0">Profil kepegawaian, akun login, dan ringkasan aktivitas.</p></div>
    <div class="d-flex gap-2"><a href="{{ route('admin.pegawai.edit',$pegawai) }}" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a><a href="{{ route('admin.pegawai.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="employee-profile-card fade-up">
            <div class="profile-cover"></div>
            <div class="profile-content">
                <span class="profile-avatar">{{ strtoupper(mb_substr($pegawai->nama,0,1)) }}</span>
                <h3>{{ $pegawai->nama }}</h3><p>NIP {{ $pegawai->nip }}</p>
                <span class="account-badge {{ $pegawai->user ? 'active' : 'inactive' }}"><i class="bi bi-{{ $pegawai->user ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i>{{ $pegawai->user ? 'Akun Login Aktif' : 'Akun Belum Terhubung' }}</span>
                <div class="profile-placement"><div><small>Jabatan</small><strong>{{ $pegawai->jabatan?->nama ?? 'Belum ditentukan' }}</strong></div><div><small>Unit Kerja</small><strong>{{ $pegawai->unitKerja?->nama ?? 'Belum ditentukan' }}</strong></div></div>
            </div>
        </div>
        <div class="activity-summary mt-4">
            <div><i class="bi bi-envelope-paper text-primary"></i><strong>{{ $jumlahSurat }}</strong><small>Surat Dibuat</small></div>
            <div><i class="bi bi-send-check text-success"></i><strong>{{ $pegawai->disposisi_tujuans_count }}</strong><small>Disposisi Diterima</small></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="detail-card fade-up">
            <div class="detail-header"><div><h4>Informasi Pegawai</h4><p>Data identitas dan kontak yang tersimpan.</p></div></div>
            <div class="detail-grid">
                @foreach([
                    ['NIP',$pegawai->nip,'person-vcard'],
                    ['Nama Lengkap',$pegawai->nama,'person'],
                    ['Email',$pegawai->email ?: '-','envelope'],
                    ['Nomor HP',$pegawai->no_hp ?: '-','telephone'],
                    ['Jabatan',$pegawai->jabatan?->nama ?? 'Belum ditentukan','award'],
                    ['Unit Kerja',$pegawai->unitKerja?->nama ?? 'Belum ditentukan','building'],
                    ['Identitas Login',$pegawai->user?->nip ?: $pegawai->user?->email ?: 'Belum tersedia','key'],
                    ['Role Akun',$pegawai->user ? ucfirst($pegawai->user->role) : 'Belum tersedia','shield-check'],
                ] as [$label,$value,$icon])
                <div class="detail-item"><span><i class="bi bi-{{ $icon }}"></i></span><div><small>{{ $label }}</small><strong>{{ $value }}</strong></div></div>
                @endforeach
            </div>
            <div class="address-box"><small>Alamat</small><p>{{ $pegawai->alamat ?: 'Alamat belum diisi.' }}</p></div>
            <div class="detail-actions">
                <form method="POST" action="{{ route('admin.pegawai.destroy',$pegawai) }}" onsubmit="return confirm('Hapus profil dan akun login pegawai ini? Data dapat dipulihkan melalui Pengaturan.')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-2"></i>Hapus Pegawai</button></form>
                <a href="{{ route('admin.pegawai.edit',$pegawai) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-2"></i>Edit Data</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
.employee-profile-card,.detail-card,.activity-summary{background:#fff;border:1px solid #e5ebf2;border-radius:20px;overflow:hidden;box-shadow:0 9px 26px rgba(15,76,129,.06)}.profile-cover{height:110px;background:linear-gradient(135deg,#0f4c81,#2780d9)}.profile-content{text-align:center;padding:0 24px 25px}.profile-avatar{width:94px;height:94px;margin:-48px auto 14px;display:grid;place-items:center;border:6px solid #fff;border-radius:50%;background:#edf5fc;color:#0f4c81;font-size:36px;font-weight:800;box-shadow:0 8px 20px rgba(15,76,129,.15)}.profile-content h3{font-size:21px;margin:0}.profile-content>p{color:#64748b}.account-badge{display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:20px;font-size:12px;font-weight:700}.account-badge.active{background:#dcfce7;color:#15803d}.account-badge.inactive{background:#fee2e2;color:#b91c1c}.profile-placement{display:grid;gap:12px;text-align:left;margin-top:20px;padding-top:18px;border-top:1px solid #edf1f5}.profile-placement small,.profile-placement strong{display:block}.profile-placement small{color:#64748b;font-size:12px}.activity-summary{display:grid;grid-template-columns:1fr 1fr;padding:20px}.activity-summary>div{text-align:center}.activity-summary>div+div{border-left:1px solid #e5ebf2}.activity-summary i,.activity-summary strong,.activity-summary small{display:block}.activity-summary i{font-size:22px}.activity-summary strong{font-size:22px}.activity-summary small{color:#64748b}.detail-header{padding:22px 25px;border-bottom:1px solid #edf1f5}.detail-header h4{margin:0}.detail-header p{color:#64748b;margin:3px 0 0}.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;padding:24px}.detail-item{display:flex;align-items:center;gap:12px;padding:14px;border:1px solid #e7edf4;border-radius:13px;background:#fbfcfe}.detail-item>span{width:38px;height:38px;display:grid;place-items:center;border-radius:10px;background:#eaf3fb;color:#0f4c81}.detail-item small,.detail-item strong{display:block}.detail-item small{font-size:11px;text-transform:uppercase;color:#64748b}.address-box{margin:0 24px 24px;padding:17px;border-radius:13px;background:#f7f9fc}.address-box small{color:#64748b;text-transform:uppercase;font-weight:700}.address-box p{margin:5px 0 0;white-space:pre-wrap}.detail-actions{display:flex;justify-content:space-between;padding:20px 24px;border-top:1px solid #edf1f5}.detail-actions form{margin:0}@media(max-width:650px){.detail-grid{grid-template-columns:1fr}.detail-actions{flex-direction:column-reverse;gap:10px}.detail-actions .btn,.detail-actions form{width:100%}}
</style>
@endpush
