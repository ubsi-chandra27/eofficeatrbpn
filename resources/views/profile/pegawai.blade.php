@extends('layouts.pegawai')
@section('title', 'Profil Pegawai')

@section('content')
<div class="employee-profile-page">
    <header class="profile-heading">
        <div>
            <span class="profile-eyebrow">PENGATURAN AKUN</span>
            <h2><i class="bi bi-person-circle text-primary me-2"></i>Profil Saya</h2>
            <p>Kelola identitas, informasi kontak, dan keamanan akun Anda.</p>
        </div>
        <span class="role-badge"><i class="bi bi-patch-check-fill"></i>Pegawai</span>
    </header>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ ['password-updated' => 'Password berhasil diperbarui.', 'photo-updated' => 'Foto profil berhasil diperbarui.', 'photo-deleted' => 'Foto profil berhasil dihapus.', 'profile-updated' => 'Profil berhasil diperbarui.'][session('status')] ?? 'Perubahan berhasil disimpan.' }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->userDeletion->any())<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->userDeletion->first() }}</div>@endif
    @unless($pegawai)<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Data kepegawaian belum terhubung dengan akun ini. Hubungi Admin untuk menghubungkan NIP, jabatan, dan unit kerja.</div>@endunless

    <div class="row g-4 align-items-start">
        <aside class="col-lg-4">
            <section class="card border-0 shadow-sm profile-summary">
                <div class="card-body p-4 text-start">
                    <div class="employee-avatar">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="Foto profil {{ $user->name }}">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="photo-actions mb-3">
                        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="employeePhotoForm">
                            @csrf @method('PATCH')
                            <label for="employeeProfilePhoto" class="btn btn-sm btn-outline-primary"><i class="bi bi-camera me-1"></i>{{ $user->profile_photo_path ? 'Ganti Foto' : 'Tambah Foto' }}</label>
                            <input id="employeeProfilePhoto" type="file" name="photo" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="document.getElementById('employeePhotoForm').submit()">
                        </form>
                        @if($user->profile_photo_path)
                            <form action="{{ route('profile.photo.destroy') }}" method="POST" onsubmit="return confirm('Hapus foto profil?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button></form>
                        @endif
                    </div>
                    @error('photo')<div class="alert alert-danger small py-2">{{ $message }}</div>@enderror
                    <h4>{{ $user->name }}</h4>
                    <p class="position-name">{{ $pegawai?->jabatan?->nama ?? 'Jabatan belum ditentukan' }}</p>
                    <span class="account-state"><i class="bi bi-shield-check"></i>Akun aktif</span>

                    <div class="identity-list">
                        <div><span>NIP</span><strong>{{ $user->nip ?? $pegawai?->nip ?? '-' }}</strong></div>
                        <div><span>Unit Kerja</span><strong>{{ $pegawai?->unitKerja?->nama ?? '-' }}</strong></div>
                        <div><span>Email</span><strong>{{ $user->email }}</strong></div>
                        <div><span>Telepon</span><strong>{{ $user->phone ?: ($pegawai?->no_hp ?: '-') }}</strong></div>
                    </div>
                </div>
            </section>

            <section class="profile-stats mt-3">
                <div><strong>{{ $statistik['surat'] }}</strong><span>Surat Dibuat</span></div>
                <div><strong>{{ $statistik['disposisi_aktif'] }}</strong><span>Disposisi Aktif</span></div>
                <div><strong>{{ $statistik['disposisi_selesai'] }}</strong><span>Selesai</span></div>
            </section>

            <div class="employee-info mt-3"><i class="bi bi-info-circle-fill"></i><div><strong>Data kepegawaian</strong><span>NIP, jabatan, dan unit kerja dikelola Admin agar tetap sesuai master data.</span></div></div>
        </aside>

        <div class="col-lg-8">
            <section class="card border-0 shadow-sm profile-card mb-4">
                <div class="card-header bg-white p-4">
                    <h5><i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Pegawai</h5>
                    <p>Perbarui identitas akun dan informasi kontak Anda.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $user->name) }}" maxlength="255" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">NIP</label><div class="locked-field"><input value="{{ $user->nip ?? $pegawai?->nip }}" class="form-control" readonly><i class="bi bi-lock"></i></div><small class="field-note">Hubungi Admin untuk mengubah NIP.</small></div>
                            <div class="col-md-6"><label class="form-label">Nomor Telepon/WhatsApp</label><input type="tel" name="phone" value="{{ old('phone', $user->phone ?: $pegawai?->no_hp) }}" maxlength="25" class="form-control @error('phone') is-invalid @enderror" placeholder="0812 3456 7890">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Jabatan</label><div class="locked-field"><input value="{{ $pegawai?->jabatan?->nama }}" class="form-control" placeholder="Belum ditentukan" readonly><i class="bi bi-lock"></i></div></div>
                            <div class="col-md-6"><label class="form-label">Unit Kerja</label><div class="locked-field"><input value="{{ $pegawai?->unitKerja?->nama }}" class="form-control" placeholder="Belum ditentukan" readonly><i class="bi bi-lock"></i></div></div>
                            <div class="col-12"><label class="form-label">Alamat</label><textarea name="address" rows="3" maxlength="1000" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat domisili atau korespondensi">{{ old('address', $user->address ?: $pegawai?->alamat) }}</textarea>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        </div>
                        <div class="form-actions"><button class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button></div>
                    </form>
                </div>
            </section>

            <section class="card border-0 shadow-sm profile-card mb-4">
                <div class="card-header bg-white p-4"><h5><i class="bi bi-key-fill text-warning me-2"></i>Keamanan Login</h5><p>Gunakan password kuat yang tidak digunakan pada layanan lain.</p></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password.update') }}">@csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label">Password Saat Ini</label><input type="password" name="current_password" autocomplete="current-password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>@error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Password Baru</label><input type="password" name="password" autocomplete="new-password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>@error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" autocomplete="new-password" class="form-control" required></div>
                        </div>
                        <div class="form-actions"><button class="btn btn-warning"><i class="bi bi-key me-2"></i>Ubah Password</button></div>
                    </form>
                </div>
            </section>

            <section class="card border-danger-subtle shadow-sm profile-card danger-card">
                <div class="card-header bg-danger-subtle p-4"><h5 class="text-danger"><i class="bi bi-person-x-fill me-2"></i>Nonaktifkan Akun</h5><p class="text-danger-emphasis">Histori surat dan disposisi tetap tersimpan untuk kebutuhan administrasi.</p></div>
                <div class="card-body p-4"><form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan akun pegawai?')">@csrf @method('DELETE')<label class="form-label">Konfirmasi dengan password</label><div class="row g-2"><div class="col-md"><input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Masukkan password saat ini" required>@error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-auto"><button class="btn btn-outline-danger h-100"><i class="bi bi-person-x me-2"></i>Nonaktifkan Akun</button></div></div></form></div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.employee-profile-page{max-width:1395px;margin:auto;text-align:left}.profile-heading{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:22px;text-align:left}.profile-eyebrow{display:block;color:#1769aa;font-size:10px;font-weight:700;letter-spacing:1.3px;margin-bottom:5px}.profile-heading h2{font-size:26px;font-weight:750;margin:0}.profile-heading p{color:#718096;margin:5px 0 0}.role-badge{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:30px;background:#0d6efd;color:#fff;font-size:12px;font-weight:700}.profile-summary,.profile-card{border-radius:18px;overflow:hidden}.profile-summary .card-body{text-align:center!important}.employee-avatar{width:112px;height:112px;border-radius:50%;margin:0 auto 17px;background:linear-gradient(145deg,#1769aa,#0f4c81);border:4px solid #fff;box-shadow:0 10px 25px rgba(15,76,129,.22);color:#fff;display:grid;place-items:center;font-size:40px;font-weight:700;overflow:hidden}.employee-avatar img{width:100%;height:100%;object-fit:cover}.photo-actions{display:flex;justify-content:center;gap:7px}.photo-actions form{margin:0}.photo-actions .btn{border-radius:9px}.profile-summary h4{font-size:19px;font-weight:750;margin-bottom:4px;text-align:center}.position-name{font-size:13px;color:#718096;margin-bottom:12px;text-align:center}.account-state{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border-radius:20px;background:#ecfdf5;color:#166534;font-size:11px;font-weight:700}.identity-list{margin-top:22px;padding-top:10px;border-top:1px solid #edf1f5;text-align:left}.identity-list>div{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #edf1f5}.identity-list>div:last-child{border:0}.identity-list span{color:#8491a3;font-size:11px}.identity-list strong{max-width:65%;color:#334155;font-size:11px;text-align:right;overflow-wrap:anywhere}.profile-stats{display:grid;grid-template-columns:repeat(3,1fr);background:#fff;border:1px solid #e5eaf0;border-radius:14px;overflow:hidden}.profile-stats>div{text-align:center;padding:15px 5px;border-right:1px solid #e5eaf0}.profile-stats>div:last-child{border:0}.profile-stats strong,.profile-stats span{display:block}.profile-stats strong{font-size:21px;color:#0f4c81}.profile-stats span{font-size:9px;color:#8491a3}.employee-info{display:flex;gap:11px;padding:15px;border:1px solid #cfe3f5;border-radius:13px;background:#edf6ff;color:#174b75;text-align:left}.employee-info>i{font-size:18px}.employee-info strong,.employee-info span{display:block}.employee-info strong{font-size:11px}.employee-info span{font-size:10px;line-height:1.55;margin-top:2px}.profile-card{text-align:left}.profile-card .card-header{border-bottom:1px solid #edf1f5;text-align:left}.profile-card .card-header h5{font-size:16px;font-weight:750;margin:0}.profile-card .card-header p{font-size:11px;color:#8491a3;margin:4px 0 0}.profile-card .card-body,.profile-card form{text-align:left}.profile-card .form-label{font-size:12px;font-weight:650;color:#334155}.profile-card .form-control{min-height:47px;border-radius:11px;border-color:#dbe2ea;font-size:13px;text-align:left}.profile-card .form-control:focus{border-color:#72a9d5;box-shadow:0 0 0 3px rgba(23,105,170,.1)}.profile-card .form-control[readonly]{background:#f5f7fa;color:#64748b;padding-right:38px}.profile-card textarea.form-control{min-height:92px}.locked-field{position:relative}.locked-field i{position:absolute;right:14px;top:15px;color:#9aa8b8;font-size:12px}.field-note{display:block;color:#8491a3;font-size:9px;margin-top:5px}.form-actions{display:flex;justify-content:flex-end;padding-top:20px;margin-top:20px;border-top:1px solid #edf1f5}.form-actions .btn,.danger-card .btn{border-radius:10px;padding:10px 20px;font-size:12px;font-weight:700}.alert{border-radius:12px;font-size:12px;text-align:left}@media(max-width:767px){.profile-heading{align-items:flex-start;flex-direction:column}.profile-card .card-header,.profile-card .card-body{padding:19px!important}.form-actions .btn,.danger-card .btn{width:100%}.identity-list strong{max-width:58%}}
</style>
@endpush
