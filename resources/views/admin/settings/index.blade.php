@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="page-header fade-up">
    <div><h2><i class="bi bi-gear-fill text-primary me-2"></i>Pengaturan Sistem</h2><p class="text-muted mb-0">Atur identitas, persuratan, notifikasi, laporan, dan pemulihan data.</p></div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary"><i class="bi bi-people me-2"></i>Manajemen Pengguna</a>
</div>

@if($errors->any())<div class="alert alert-danger"><strong>Pengaturan belum tersimpan.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="settings-shell fade-up">
    <div class="settings-tabs nav nav-pills" role="tablist">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general" type="button"><i class="bi bi-building"></i>Umum</button>
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#letters" type="button"><i class="bi bi-envelope-paper"></i>Persuratan</button>
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#notifications" type="button"><i class="bi bi-bell"></i>Notifikasi</button>
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reports" type="button"><i class="bi bi-file-earmark-bar-graph"></i>Laporan</button>
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#trash" type="button"><i class="bi bi-trash3"></i>Tempat Sampah <span class="badge bg-danger">{{ collect($trash)->sum(fn($items) => $items->count()) }}</span></button>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-content">
        @csrf @method('PUT')
        <div class="tab-content">
            <div class="tab-pane fade show active" id="general">
                <div class="setting-heading"><h4>Identitas Aplikasi</h4><p>Nama yang tampil pada judul halaman dan sidebar admin.</p></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nama Aplikasi</label><input name="app_name" class="form-control" value="{{ old('app_name', $settings['app_name']) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Subjudul</label><input name="app_subtitle" class="form-control" value="{{ old('app_subtitle', $settings['app_subtitle']) }}" required></div>
                </div>
                <div class="info-box mt-4"><i class="bi bi-shield-check"></i><div><strong>Keamanan akun</strong><span>Role, reset password, dan penonaktifan akun dikelola melalui halaman Manajemen Pengguna. Admin terakhir tetap dilindungi.</span></div></div>
                <div class="setting-heading mt-4"><h4>Informasi Dashboard Umum</h4><p>Konten operasional yang ditampilkan kepada pengguna Umum.</p></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Judul Pengumuman</label><input name="public_announcement_title" class="form-control" maxlength="120" value="{{ old('public_announcement_title', $settings['public_announcement_title']) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Jam Layanan</label><input name="public_service_hours" class="form-control" maxlength="120" value="{{ old('public_service_hours', $settings['public_service_hours']) }}" required></div>
                    <div class="col-12"><label class="form-label">Isi Pengumuman</label><textarea name="public_announcement_message" class="form-control" rows="3" maxlength="500" required>{{ old('public_announcement_message', $settings['public_announcement_message']) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Email Bantuan</label><input type="email" name="public_help_email" class="form-control" value="{{ old('public_help_email', $settings['public_help_email']) }}" placeholder="bantuan@email.com"></div>
                    <div class="col-md-6"><label class="form-label">Telepon Bantuan</label><input name="public_help_phone" class="form-control" maxlength="30" value="{{ old('public_help_phone', $settings['public_help_phone']) }}" placeholder="Nomor layanan"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="letters">
                <div class="setting-heading"><h4>Aturan Persuratan</h4><p>Nilai standar yang digunakan ketika surat baru dibuat.</p></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Status Awal Surat Masuk</label><select name="incoming_default_status" class="form-select"><option value="diajukan" @selected($settings['incoming_default_status']==='diajukan')>Diajukan</option><option value="diverifikasi" @selected($settings['incoming_default_status']==='diverifikasi')>Langsung Diverifikasi</option></select></div>
                    <div class="col-md-6"><label class="form-label">Status Awal Surat Keluar</label><select name="outgoing_default_status" class="form-select"><option value="draft" @selected($settings['outgoing_default_status']==='draft')>Draft</option><option value="diajukan" @selected($settings['outgoing_default_status']==='diajukan')>Diajukan</option></select></div>
                    <div class="col-md-6"><label class="form-label">Maksimal Lampiran</label><div class="input-group"><input type="number" name="max_upload_mb" min="1" max="20" class="form-control" value="{{ old('max_upload_mb', $settings['max_upload_mb']) }}"><span class="input-group-text">MB</span></div></div>
                    <div class="col-md-6"><label class="form-label">Target Disposisi Standar</label><div class="input-group"><input type="number" name="disposition_deadline_days" min="1" max="30" class="form-control" value="{{ old('disposition_deadline_days', $settings['disposition_deadline_days']) }}"><span class="input-group-text">hari</span></div></div>
                </div>
                <div class="workflow mt-4"><span>Surat masuk</span><b>Diajukan</b><i class="bi bi-arrow-right"></i><b>Diverifikasi</b><i class="bi bi-arrow-right"></i><b>Diteruskan</b><i class="bi bi-arrow-right"></i><b>Disposisi</b><i class="bi bi-arrow-right"></i><b>Selesai</b></div>
            </div>

            <div class="tab-pane fade" id="notifications">
                <div class="setting-heading"><h4>Notifikasi Internal</h4><p>Tentukan kejadian yang perlu ditampilkan sebagai pemberitahuan.</p></div>
                @foreach(['notify_new_letter'=>['Surat baru','Beritahu admin ketika pegawai mengajukan surat.'],'notify_disposition'=>['Disposisi baru','Beritahu pegawai ketika menerima disposisi.'],'notify_deadline'=>['Pengingat tenggat','Tampilkan pengingat disposisi yang mendekati batas waktu.']] as $key=>$item)
                    <label class="setting-switch"><div><strong>{{ $item[0] }}</strong><span>{{ $item[1] }}</span></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" @checked(old($key, (bool)$settings[$key]))></div></label>
                @endforeach
            </div>

            <div class="tab-pane fade" id="reports">
                <div class="setting-heading"><h4>Identitas Laporan</h4><p>Informasi standar untuk laporan yang dicetak admin.</p></div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Judul/Kop Laporan</label><input name="report_header" class="form-control" value="{{ old('report_header', $settings['report_header']) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Nama Penandatangan</label><input name="report_signer_name" class="form-control" value="{{ old('report_signer_name', $settings['report_signer_name']) }}"></div>
                    <div class="col-md-6"><label class="form-label">Jabatan Penandatangan</label><input name="report_signer_title" class="form-control" value="{{ old('report_signer_title', $settings['report_signer_title']) }}"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="trash">
                <div class="setting-heading"><h4>Tempat Sampah</h4><p>Pulihkan data yang dihapus sementara. Penghapusan permanen sengaja tidak disediakan untuk melindungi histori.</p></div>
                @forelse($trash as $type => $items)
                    @if($items->isNotEmpty())
                        <h6 class="trash-title">{{ ['users'=>'Akun Pengguna','pegawai'=>'Pegawai','surats'=>'Surat','disposisi'=>'Disposisi'][$type] }} <span>{{ $items->count() }}</span></h6>
                        @foreach($items as $item)
                            <div class="trash-row"><div><strong>{{ $item->name ?? $item->nama ?? $item->nomor_surat ?? ('Data #'.$item->id) }}</strong><small>Dihapus {{ optional($item->deleted_at)->format('d M Y H:i') }}</small></div><button form="restore-{{ $type }}-{{ $item->id }}" class="btn btn-sm btn-outline-success" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan</button></div>
                        @endforeach
                    @endif
                @empty
                @endforelse
                @if(collect($trash)->every(fn($items) => $items->isEmpty()))<div class="empty-trash"><i class="bi bi-trash3"></i><strong>Tempat sampah kosong</strong><span>Tidak ada data yang perlu dipulihkan.</span></div>@endif
            </div>
        </div>

        <div class="settings-footer"><span><i class="bi bi-info-circle me-1"></i>Perubahan dicatat dalam log aktivitas.</span><button class="btn btn-primary" type="submit"><i class="bi bi-save me-2"></i>Simpan Pengaturan</button></div>
    </form>
</div>

@foreach($trash as $type => $items)@foreach($items as $item)<form id="restore-{{ $type }}-{{ $item->id }}" method="POST" action="{{ route('admin.settings.trash.restore', [$type, $item->id]) }}" class="d-none">@csrf @method('PATCH')</form>@endforeach @endforeach
@endsection

@push('styles')
<style>
.settings-shell{display:grid;grid-template-columns:245px minmax(0,1fr);background:#fff;border:1px solid #e4eaf1;border-radius:20px;box-shadow:0 10px 28px rgba(15,76,129,.07);overflow:hidden}.settings-tabs{padding:20px;background:#f8fafc;border-right:1px solid #e7edf3;display:flex;flex-direction:column;gap:7px;align-items:stretch}.settings-tabs .nav-link{text-align:left;color:#536174;padding:12px 14px;border-radius:11px;font-weight:600;display:flex;align-items:center;gap:10px}.settings-tabs .nav-link.active{background:#0f4c81;color:#fff}.settings-tabs .badge{margin-left:auto}.settings-content{min-width:0}.tab-pane{padding:30px;min-height:410px}.setting-heading{padding-bottom:18px;margin-bottom:22px;border-bottom:1px solid #edf1f5}.setting-heading h4{margin:0 0 5px;font-weight:700;color:#17233b}.setting-heading p{margin:0;color:#718096}.settings-content .form-label{font-weight:650;color:#334155}.settings-content .form-control,.settings-content .form-select,.settings-content .input-group-text{min-height:48px;border-color:#dbe2ea}.info-box{display:flex;gap:13px;padding:16px;background:#edf6ff;border-radius:13px;color:#174b75}.info-box i{font-size:22px}.info-box strong,.info-box span,.setting-switch strong,.setting-switch span{display:block}.info-box span,.setting-switch span{font-size:13px;margin-top:3px}.workflow{display:flex;align-items:center;flex-wrap:wrap;gap:8px;padding:15px;border:1px dashed #b8c8d8;border-radius:12px}.workflow span{font-weight:700;margin-right:5px}.workflow b{padding:6px 10px;background:#edf6ff;color:#0f4c81;border-radius:8px;font-size:12px}.setting-switch{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:17px 0;border-bottom:1px solid #edf1f5}.setting-switch .form-check-input{width:44px;height:23px}.trash-title{display:flex;justify-content:space-between;margin:20px 0 8px;color:#334155}.trash-title span{background:#eef2f6;padding:3px 9px;border-radius:20px}.trash-row{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border:1px solid #e8edf2;border-radius:10px;margin-bottom:8px}.trash-row small{display:block;color:#8190a3;margin-top:2px}.empty-trash{text-align:center;padding:55px;color:#8290a3}.empty-trash i,.empty-trash strong,.empty-trash span{display:block}.empty-trash i{font-size:46px;margin-bottom:10px}.settings-footer{padding:18px 30px;border-top:1px solid #e7edf3;background:#fbfcfd;display:flex;align-items:center;justify-content:space-between;gap:15px}.settings-footer span{color:#718096;font-size:13px}.settings-footer .btn{min-width:190px}
@media(max-width:900px){.settings-shell{grid-template-columns:1fr}.settings-tabs{border-right:0;border-bottom:1px solid #e7edf3;flex-direction:row;overflow-x:auto}.settings-tabs .nav-link{white-space:nowrap}.settings-footer{align-items:stretch;flex-direction:column}.settings-footer .btn{width:100%}}
</style>
@endpush
