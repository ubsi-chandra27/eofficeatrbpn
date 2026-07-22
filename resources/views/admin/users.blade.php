@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Pengguna</h2><p class="text-muted mb-0">Kelola NIP, role, password, dan status akun.</p></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Nama</th><th>Email</th><th>NIP Login</th><th>Role</th><th width="300">Keamanan Akun</th></tr></thead><tbody>
@foreach($users as $user)
<tr><td><b>{{ $user->name }}</b>@if($user->id===auth()->id()) <span class="badge bg-primary">Anda</span>@endif</td><td>{{ $user->email }}</td><td>
@if($user->role !== 'umum')<form action="{{ route('admin.users.updateNip',$user->id) }}" method="POST" class="d-flex gap-1">@csrf @method('PATCH')<input name="nip" value="{{ $user->nip }}" class="form-control form-control-sm" placeholder="Belum ada NIP" required><button class="btn btn-sm btn-outline-primary" title="Simpan NIP"><i class="bi bi-check-lg"></i></button></form>@else<span class="text-muted">—</span>@endif
</td><td>
@if($user->id===auth()->id())<span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>@else
<form action="{{ route('admin.users.updateRole',$user->id) }}" method="POST">@csrf @method('PATCH')<select name="role" class="form-select" onchange="this.form.submit()">@foreach($roles as $role)<option value="{{ $role->name }}" @selected($user->role===$role->name)>{{ ucfirst($role->name) }}</option>@endforeach</select></form>@endif
</td><td><div class="d-flex gap-2 align-items-start">@if($user->id!==auth()->id())
<form action="{{ route('admin.users.resetPassword',$user->id) }}" method="POST" class="d-flex gap-2 flex-grow-1">@csrf @method('PATCH')<div><input type="password" name="password" class="form-control form-control-sm" placeholder="Password baru" required minlength="8"><input type="password" name="password_confirmation" class="form-control form-control-sm mt-1" placeholder="Konfirmasi" required></div><button class="btn btn-warning btn-sm">Reset</button></form>
<form action="{{ route('admin.users.destroy',$user->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan akun ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="bi bi-person-x"></i></button></form>
@else<span class="text-muted small">Kelola password sendiri melalui profil.</span>@endif</div></td></tr>
@endforeach
</tbody></table></div></div>
@endsection
