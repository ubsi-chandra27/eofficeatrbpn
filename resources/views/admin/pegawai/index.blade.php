@extends('layouts.admin')
@section('title','Data Pegawai')
@section('content')
<div class="page-header fade-up">
    <div><h2><i class="bi bi-people-fill text-primary me-2"></i>Data Pegawai</h2><p class="text-muted mb-0">Kelola profil kepegawaian dan akun login secara terintegrasi.</p></div>
    <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai</a>
</div>

<div class="employee-stats mb-4">
@foreach([
    ['Total Pegawai',$statistik['total'],'people','primary'],
    ['Akun Aktif',$statistik['akun_aktif'],'person-check','success'],
    ['Tanpa Akun',$statistik['tanpa_akun'],'person-exclamation','danger'],
    ['Profil Lengkap',$statistik['profil_lengkap'],'clipboard-check','info'],
] as [$label,$value,$icon,$color])
    <div class="employee-stat"><span class="stat-symbol bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></span><div><strong>{{ $value }}</strong><small>{{ $label }}</small></div></div>
@endforeach
</div>

<div class="employee-card fade-up">
    <form method="GET" class="employee-filter">
        <div class="employee-search"><i class="bi bi-search"></i><input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nama, NIP, email, atau nomor HP"></div>
        <select name="jabatan_id" class="form-select"><option value="">Semua Jabatan</option>@foreach($jabatan as $item)<option value="{{ $item->id }}" @selected((string)request('jabatan_id')===(string)$item->id)>{{ $item->nama }}</option>@endforeach</select>
        <select name="unit_kerja_id" class="form-select"><option value="">Semua Unit</option>@foreach($unitKerja as $item)<option value="{{ $item->id }}" @selected((string)request('unit_kerja_id')===(string)$item->id)>{{ $item->nama }}</option>@endforeach</select>
        <select name="status_akun" class="form-select"><option value="">Semua Akun</option><option value="aktif" @selected(request('status_akun')==='aktif')>Akun Aktif</option><option value="belum" @selected(request('status_akun')==='belum')>Tanpa Akun</option></select>
        <button class="btn btn-primary"><i class="bi bi-funnel"></i></button>
        @if(request()->hasAny(['keyword','jabatan_id','unit_kerja_id','status_akun']))<a href="{{ route('admin.pegawai.index') }}" class="btn btn-light border"><i class="bi bi-x-lg"></i></a>@endif
    </form>
    <div class="table-responsive">
        <table class="table employee-table align-middle mb-0">
            <thead><tr><th>No</th><th>Pegawai</th><th>Kontak</th><th>Jabatan &amp; Unit</th><th>Status Akun</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($pegawai as $item)
                <tr>
                    <td>{{ $pegawai->firstItem()+$loop->index }}</td>
                    <td><div class="employee-identity"><span>{{ strtoupper(mb_substr($item->nama,0,1)) }}</span><div><strong>{{ $item->nama }}</strong><small>NIP {{ $item->nip }}</small></div></div></td>
                    <td><strong>{{ $item->email ?: '-' }}</strong><small class="d-block text-muted">{{ $item->no_hp ?: 'Nomor HP belum diisi' }}</small></td>
                    <td><strong>{{ $item->jabatan?->nama ?? 'Jabatan belum diisi' }}</strong><small class="d-block text-muted"><i class="bi bi-building me-1"></i>{{ $item->unitKerja?->nama ?? 'Unit belum diisi' }}</small></td>
                    <td>@if($item->user)<span class="status-chip active"><i class="bi bi-check-circle-fill"></i> Aktif</span><small class="d-block text-muted mt-1">Login: {{ $item->user->nip ?: $item->user->email }}</small>@else<span class="status-chip inactive"><i class="bi bi-exclamation-circle-fill"></i> Belum terhubung</span>@endif</td>
                    <td><div class="employee-actions"><a href="{{ route('admin.pegawai.show',$item) }}" class="btn btn-light text-primary" title="Detail"><i class="bi bi-eye"></i></a><a href="{{ route('admin.pegawai.edit',$item) }}" class="btn btn-light text-warning" title="Edit"><i class="bi bi-pencil-square"></i></a><form method="POST" action="{{ route('admin.pegawai.destroy',$item) }}" onsubmit="return confirm('Hapus pegawai {{ addslashes($item->nama) }} beserta akun loginnya? Data dapat dipulihkan dari Pengaturan.')">@csrf @method('DELETE')<button class="btn btn-light text-danger" title="Hapus"><i class="bi bi-trash"></i></button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="employee-empty"><i class="bi bi-person-x"></i><h5>Data pegawai tidak ditemukan</h5><p>Ubah filter pencarian atau tambahkan pegawai baru.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="employee-footer"><span>{{ $pegawai->firstItem() ?? 0 }}–{{ $pegawai->lastItem() ?? 0 }} dari {{ $pegawai->total() }} pegawai</span>{{ $pegawai->withQueryString()->links() }}</div>
</div>
@endsection
@push('styles')
<style>
.employee-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}.employee-stat{background:#fff;border:1px solid #e7edf4;border-radius:18px;padding:20px;display:flex;align-items:center;gap:15px;box-shadow:0 8px 22px rgba(15,76,129,.05)}.stat-symbol{width:50px;height:50px;border-radius:14px;display:grid;place-items:center;font-size:21px}.employee-stat strong,.employee-stat small{display:block}.employee-stat strong{font-size:25px;color:#172033}.employee-stat small{color:#64748b}.employee-card{background:#fff;border:1px solid #e5ebf2;border-radius:20px;overflow:hidden;box-shadow:0 10px 28px rgba(15,76,129,.06)}.employee-filter{padding:20px;display:grid;grid-template-columns:minmax(260px,1.5fr) repeat(3,minmax(150px,1fr)) auto auto;gap:10px;border-bottom:1px solid #edf1f5}.employee-search{position:relative}.employee-search i{position:absolute;left:15px;top:14px;color:#64748b}.employee-search input,.employee-filter select{width:100%;height:46px;border:1px solid #dbe3ec;border-radius:11px;background:#fff}.employee-search input{padding:0 14px 0 42px}.employee-table thead th{padding:15px 18px;background:#f7f9fc;color:#475569;font-size:12px;text-transform:uppercase;letter-spacing:.5px;border:0}.employee-table td{padding:17px 18px;border-color:#edf1f5}.employee-identity{display:flex;align-items:center;gap:12px}.employee-identity>span{width:43px;height:43px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,#0f4c81,#2780d9);color:#fff;font-weight:700}.employee-identity strong,.employee-identity small{display:block}.employee-identity small{color:#64748b}.status-chip{display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:20px;font-size:12px;font-weight:700}.status-chip.active{background:#dcfce7;color:#15803d}.status-chip.inactive{background:#fee2e2;color:#b91c1c}.employee-actions{display:flex;justify-content:flex-end;gap:7px}.employee-actions form{margin:0}.employee-actions .btn{width:38px;height:38px;padding:0;display:grid;place-items:center;border:1px solid #e7edf4}.employee-footer{padding:18px 20px;display:flex;align-items:center;justify-content:space-between;color:#64748b}.employee-empty{text-align:center;padding:60px 20px;color:#64748b}.employee-empty i{font-size:50px;color:#cbd5e1}@media(max-width:1100px){.employee-stats{grid-template-columns:repeat(2,1fr)}.employee-filter{grid-template-columns:1fr 1fr}}@media(max-width:650px){.employee-stats,.employee-filter{grid-template-columns:1fr}.employee-footer{flex-direction:column;gap:14px}.page-header{align-items:flex-start;gap:15px}}
</style>
@endpush
