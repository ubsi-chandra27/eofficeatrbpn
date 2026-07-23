@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="user-management-page">
    <div class="page-header fade-up">
        <div>
            <h2><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Pengguna</h2>
            <p class="text-muted mb-0">Kelola identitas login, NIP pegawai, role, dan keamanan akun.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Akun', $statistik['total'], 'people', 'primary'],
            ['Administrator', $statistik['admin'], 'shield-check', 'danger'],
            ['Pegawai', $statistik['pegawai'], 'person-badge', 'success'],
            ['Pengguna Umum', $statistik['umum'], 'person', 'info'],
        ] as [$label, $value, $icon, $color])
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm user-stat-card">
                    <div><small>{{ $label }}</small><strong>{{ $value }}</strong></div>
                    <span class="bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></span>
                </div>
            </div>
        @endforeach
    </div>

    @if($statistik['profil_belum_terhubung'])
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Ada <strong>{{ $statistik['profil_belum_terhubung'] }}</strong> akun ber-role Pegawai yang belum terhubung dengan Data Pegawai.
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white p-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="user-filters">
                <div class="user-search">
                    <i class="bi bi-search"></i>
                    <input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nama, email, atau NIP...">
                </div>
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <select name="profil" class="form-select">
                    <option value="">Semua Profil</option>
                    <option value="terhubung" @selected(request('profil') === 'terhubung')>Profil Terhubung</option>
                    <option value="belum" @selected(request('profil') === 'belum')>Belum Terhubung</option>
                </select>
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                @if(request()->hasAny(['keyword', 'role', 'profil']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 user-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>NIP Pegawai</th>
                        <th>Profil Kepegawaian</th>
                        <th>Role</th>
                        <th>Keamanan Akun</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    @php($nipEfektif = $user->nip ?: $user->pegawai?->nip)
                    <tr>
                        <td>
                            <div class="user-identity">
                                <span class="user-avatar">
                                    @if($user->profile_photo_path)
                                        <img src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="Foto {{ $user->name }}">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </span>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->id === auth()->id()) <span class="badge bg-primary ms-1">Anda</span> @endif
                                    <small>{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role !== 'umum' || $user->pegawai)
                                <form action="{{ route('admin.users.updateNip', $user) }}" method="POST" class="nip-form">
                                    @csrf @method('PATCH')
                                    <input name="nip" value="{{ old('nip', $nipEfektif) }}" class="form-control form-control-sm" placeholder="Belum ada NIP" required>
                                    <button class="btn btn-sm btn-outline-primary" title="Simpan NIP"><i class="bi bi-check-lg"></i></button>
                                </form>
                                @if(!$user->nip && $user->pegawai?->nip)
                                    <small class="text-success"><i class="bi bi-link-45deg"></i> Diambil dari profil pegawai</small>
                                @endif
                            @else
                                <span class="text-muted">Tidak diperlukan</span>
                            @endif
                        </td>
                        <td>
                            @if($user->pegawai)
                                <strong>{{ $user->pegawai->nama }}</strong>
                                <small class="d-block text-muted">{{ $user->pegawai->jabatan?->nama ?? 'Jabatan belum diisi' }}</small>
                                <small class="d-block text-muted">{{ $user->pegawai->unitKerja?->nama ?? 'Unit belum diisi' }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Belum Terhubung</span>
                            @endif
                        </td>
                        <td>
                            @if($user->id === auth()->id())
                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                            @else
                                <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="role" class="form-select form-select-sm role-select" onchange="this.form.submit()">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" @selected($user->role === $role->name)>{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td>
                            @if($user->id !== auth()->id())
                                <details class="password-panel">
                                    <summary><i class="bi bi-key me-1"></i>Reset Password</summary>
                                    <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Password baru" required minlength="8">
                                        <input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Konfirmasi password" required>
                                        <button class="btn btn-warning btn-sm">Simpan Password</button>
                                    </form>
                                </details>
                            @else
                                <span class="text-muted small">Kelola melalui profil Anda.</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" title="Nonaktifkan akun"><i class="bi bi-person-x"></i></button>
                                </form>
                            @else
                                <i class="bi bi-shield-lock text-muted" title="Akun aktif tidak dapat dinonaktifkan sendiri"></i>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-people fs-1 d-block mb-2"></i>Tidak ada pengguna sesuai filter.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun</small>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-stat-card{padding:20px;display:flex;align-items:center;justify-content:space-between}.user-stat-card small{display:block;color:#64748b}.user-stat-card strong{display:block;font-size:27px;color:#17233b}.user-stat-card>span{width:48px;height:48px;border-radius:13px;display:grid;place-items:center;font-size:21px}
.user-filters{display:grid;grid-template-columns:minmax(260px,1fr) 180px 190px auto auto;gap:10px;align-items:center}.user-search{position:relative}.user-search i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8}.user-search input{width:100%;height:45px;padding:9px 12px 9px 40px;border:1px solid #dbe2ea;border-radius:10px}.user-filters .form-select,.user-filters .btn{height:45px}
.user-table{min-width:1180px}.user-table th{white-space:nowrap;background:#f8fafc;color:#475569}.user-identity{display:flex;align-items:center;gap:11px}.user-avatar{width:42px;height:42px;border-radius:50%;display:grid;place-items:center;background:#e8f1fa;color:#0f4c81;font-weight:800;overflow:hidden}.user-avatar img{width:100%;height:100%;object-fit:cover}.user-identity small{display:block;color:#64748b}.nip-form{display:flex;gap:6px;min-width:190px}.role-select{min-width:125px}
.password-panel summary{cursor:pointer;color:#0f4c81;font-weight:600;font-size:13px}.password-panel form{display:grid;gap:6px;margin-top:8px;min-width:190px}.password-panel .btn{justify-self:start}
.card-footer .pagination{margin:0}
@media(max-width:991px){.user-filters{grid-template-columns:1fr 1fr}.user-search{grid-column:1/-1}}@media(max-width:575px){.user-filters{grid-template-columns:1fr}.user-search{grid-column:auto}.user-filters .btn{width:100%}.card-footer{gap:12px;flex-direction:column}}
</style>
@endpush
