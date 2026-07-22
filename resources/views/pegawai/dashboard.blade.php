@extends('layouts.pegawai')
@section('title','Dashboard Pegawai')
@section('content')
<div class="container-fluid employee-page employee-dashboard">
    <section class="employee-welcome"><div><small>RUANG KERJA PEGAWAI</small><h2>Selamat datang, {{ auth()->user()->name }}</h2><p>Prioritaskan disposisi yang belum dibaca dan pantau surat yang masih menunggu proses.</p></div><div class="welcome-date"><i class="bi bi-calendar3"></i><span>{{ now()->translatedFormat('d F Y') }}</span></div></section>

    <div class="metric-grid">
        @foreach([['Disposisi Aktif',$disposisiAktif,'bi-send-check-fill','primary',route('pegawai.disposisi.index')],['Belum Dibaca',$disposisiBelum,'bi-envelope-exclamation-fill','danger',route('pegawai.disposisi.index',['status'=>'Belum Dibaca'])],['Surat Keluar',$suratKeluar,'bi-envelope-arrow-up-fill','success',route('pegawai.surat-keluar.index')],['Menunggu Proses',$menunggu,'bi-hourglass-split','warning',route('pegawai.surat-keluar.index',['status'=>'diajukan'])]] as [$label,$value,$icon,$color,$url])
            <a href="{{ $url }}" class="metric-card"><span class="metric-icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi {{ $icon }}"></i></span><div><strong>{{ $value }}</strong><small>{{ $label }}</small></div><i class="bi bi-chevron-right ms-auto text-muted"></i></a>
        @endforeach
    </div>

    @if($prioritasTinggi || $disposisiBelum)
        <div class="work-alert"><i class="bi bi-exclamation-triangle-fill"></i><div><strong>Ada pekerjaan yang perlu ditindaklanjuti</strong><span>{{ $disposisiBelum }} disposisi belum dibaca dan {{ $prioritasTinggi }} disposisi berprioritas tinggi.</span></div><a href="{{ route('pegawai.disposisi.index') }}" class="btn btn-sm btn-light">Buka Disposisi</a></div>
    @endif

    <section class="dashboard-panel mb-4">
        <div class="panel-heading"><div><h5>Disposisi Terbaru</h5><small>Lima tugas terbaru yang ditujukan kepada Anda</small></div><a href="{{ route('pegawai.disposisi.index') }}">Lihat semua</a></div>
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead><tr><th>Nomor Surat</th><th>Perihal</th><th>Instruksi</th><th>Prioritas</th><th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($disposisiTerbaru as $item)
                        @php($surat = $item->disposisi?->surat)
                        <tr class="{{ $item->status === 'Belum Dibaca' ? 'table-warning' : '' }}">
                            <td><strong>{{ $surat?->nomor_surat ?? '-' }}</strong></td>
                            <td>{{ Str::limit($surat?->perihal ?? 'Surat tidak tersedia', 36) }}</td>
                            <td>{{ Str::limit($item->disposisi?->catatan ?? '-', 42) }}</td>
                            <td><span class="badge bg-{{ $item->disposisi?->prioritas === 'Tinggi' ? 'danger' : ($item->disposisi?->prioritas === 'Sedang' ? 'warning' : 'secondary') }}">{{ $item->disposisi?->prioritas ?? 'Normal' }}</span></td>
                            <td>{{ $item->created_at?->translatedFormat('d M Y') }}</td>
                            <td><span class="status-chip status-{{ str($item->status)->slug() }}">{{ $item->status }}</span></td>
                            <td class="text-end"><a href="{{ route('pegawai.disposisi.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Buka disposisi"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state compact-empty"><i class="bi bi-inbox"></i><h5>Belum ada disposisi</h5><p>Tugas disposisi baru akan tampil di tabel ini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-panel mb-4">
        <div class="panel-heading"><div><h5>Surat Masuk Terbaru</h5><small>Lima surat terakhir yang Anda catat dan ajukan ke Admin</small></div><a href="{{ route('pegawai.surat-masuk.index') }}">Lihat semua</a></div>
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead><tr><th>Nomor Surat</th><th>Perihal</th><th>Asal Surat</th><th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($suratTerbaru as $surat)
                        <tr>
                            <td><strong>{{ $surat->nomor_surat }}</strong></td>
                            <td>{{ Str::limit($surat->perihal ?: '-', 42) }}</td>
                            <td>{{ Str::limit($surat->asal_surat ?: '-', 30) }}</td>
                            <td>{{ $surat->tanggal_surat?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td><span class="badge rounded-pill bg-{{ $surat->status_badge }}">{{ $surat->status_label }}</span>@if($surat->catatan_admin)<small class="d-block text-danger mt-1">{{ Str::limit($surat->catatan_admin, 35) }}</small>@endif</td>
                            <td class="text-end"><a href="{{ route('pegawai.surat-masuk.show', $surat->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat surat"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state compact-empty"><i class="bi bi-envelope-arrow-down"></i><h5>Belum ada surat masuk</h5><p>Surat yang Anda catat akan tampil di tabel ini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-panel">
        <div class="panel-heading"><div><h5>Aktivitas Terbaru</h5><small>Riwayat perubahan surat dan akun Anda</small></div></div>
        <div class="table-responsive">
            <table class="table dashboard-table mb-0">
                <thead><tr><th>Waktu</th><th>Aktivitas</th><th>Nomor Surat</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($aktivitasTerbaru as $item)
                        <tr>
                            <td><strong>{{ $item['jam']->translatedFormat('d M Y') }}</strong><small class="d-block text-muted">{{ $item['jam']->format('H:i') }} WIB</small></td>
                            <td>{{ $item['jenis'] }}</td>
                            <td>{{ $item['nomor'] }}</td>
                            <td>{{ Str::limit($item['keterangan'], 65) }}</td>
                            <td>@if($item['status'])<span class="status-chip">{{ str($item['status'])->replace('_', ' ')->title() }}</span>@else<span class="text-muted">—</span>@endif</td>
                            <td class="text-end">@if($item['url'])<a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-primary" title="Lihat data terkait"><i class="bi bi-box-arrow-up-right"></i></a>@else<span class="text-muted">—</span>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state compact-empty"><i class="bi bi-clock-history"></i><h5>Belum ada aktivitas</h5><p>Perubahan surat dan profil akan tercatat otomatis di tabel ini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="quick-guide"><div><span>PANDUAN KERJA</span><h5>Alur kerja pegawai</h5></div><ol><li><b>1</b><span><strong>Periksa tugas</strong><small>Buka disposisi yang belum dibaca.</small></span></li><li><b>2</b><span><strong>Tindak lanjuti</strong><small>Pelajari surat dan catatan admin.</small></span></li><li><b>3</b><span><strong>Kelola surat</strong><small>Simpan sebagai draft sebelum diajukan.</small></span></li><li><b>4</b><span><strong>Selesaikan</strong><small>Tandai disposisi setelah ditindaklanjuti.</small></span></li></ol></section>
</div>
@endsection
@push('styles')<style>
.employee-welcome{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:25px 28px;margin-bottom:18px;border-radius:18px;background:linear-gradient(135deg,#0f4c81,#1769aa);color:#fff}.employee-welcome small{font-size:10px;letter-spacing:1.2px;color:#bde1ff}.employee-welcome h2{font-size:25px;font-weight:700;margin:5px 0}.employee-welcome p{color:#d7eafd;margin:0;font-size:13px}.welcome-date{display:flex;align-items:center;gap:9px;padding:11px 15px;border-radius:11px;background:rgba(255,255,255,.13);white-space:nowrap}.metric-card{text-decoration:none;color:#334155;transition:.2s}.metric-card:hover{transform:translateY(-2px);box-shadow:0 11px 24px rgba(30,55,85,.09)}.work-alert{display:flex;align-items:center;gap:13px;padding:15px 18px;margin-bottom:18px;background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;color:#9a3412}.work-alert>div{flex:1}.work-alert strong,.work-alert span{display:block}.work-alert span{font-size:11px;margin-top:2px}.dashboard-panel{background:#fff;border:1px solid #e5eaf0;border-radius:17px;padding:22px;box-shadow:0 8px 25px rgba(39,61,89,.04)}.panel-heading{display:flex;justify-content:space-between;align-items:center;padding-bottom:13px;border-bottom:1px solid #edf1f5}.panel-heading h5{font-weight:700;margin:0}.panel-heading small{color:#8491a3}.panel-heading a{font-size:12px}.dashboard-table{min-width:880px}.dashboard-table th{padding:13px 10px;color:#64748b;font-size:10px;letter-spacing:.45px;text-transform:uppercase;white-space:nowrap;border-bottom-color:#dfe6ee}.dashboard-table td{padding:13px 10px;font-size:11px;color:#42526a;vertical-align:middle;border-bottom-color:#edf1f5}.dashboard-table tbody tr:last-child td{border-bottom:0}.dashboard-table .btn{width:32px;height:32px;padding:0;display:inline-grid;place-items:center}.status-chip{display:inline-block;font-size:9px;padding:5px 8px;border-radius:20px;background:#eef2f6;white-space:nowrap}.status-belum-dibaca{background:#fee2e2;color:#b91c1c}.status-selesai{background:#dcfce7;color:#166534}.compact-empty{padding:35px 20px!important}.quick-guide{display:flex;align-items:center;gap:30px;margin-top:18px;padding:20px 23px;background:#fff;border:1px solid #e5eaf0;border-radius:17px}.quick-guide>div>span{font-size:9px;letter-spacing:1px;color:#1769aa;font-weight:700}.quick-guide h5{font-weight:700;margin:4px 0}.quick-guide ol{flex:1;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;list-style:none;margin:0;padding:0}.quick-guide li{display:flex;gap:8px}.quick-guide li>b{width:25px;height:25px;border-radius:8px;background:#1769aa;color:#fff;display:grid;place-items:center;font-size:10px}.quick-guide strong,.quick-guide small{display:block}.quick-guide strong{font-size:11px}.quick-guide small{font-size:9px;color:#8491a3}@media(max-width:900px){.quick-guide{align-items:flex-start;flex-direction:column}.quick-guide ol{grid-template-columns:repeat(2,1fr);width:100%}}@media(max-width:600px){.employee-welcome{align-items:flex-start;flex-direction:column}.welcome-date{width:100%}.work-alert{align-items:flex-start;flex-wrap:wrap}.work-alert .btn{width:100%}.quick-guide ol{grid-template-columns:1fr}}
</style>@endpush
