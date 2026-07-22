@extends($layout)

@section('title', 'Profil Saya')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold text-primary mb-1">
                <i class="fas fa-user-circle me-2"></i>
                Profil Saya
            </h3>

            <p class="text-muted mb-0">
                Kelola informasi akun dan keamanan login Anda.
            </p>
        </div>

    </div>

    {{-- Alert --}}
    @if(session('success') || session('status'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') ?: (session('status') === 'password-updated' ? 'Password berhasil diperbarui.' : 'Profil berhasil diperbarui.') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>
    @endif

    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D6EFD&color=fff&size=200"
                        class="rounded-circle mb-3"
                        width="130">

                    <h5 class="fw-bold">

                        {{ $user->name }}

                    </h5>

                    <span class="badge bg-primary">

                        {{ ucfirst($user->role) }}

                    </span>

                    <hr>

                    <p class="mb-1">

                        <strong>Email</strong>

                    </p>

                    <small class="text-muted">

                        {{ $user->email }}

                    </small>

                </div>

            </div>

        </div>

        {{-- Content --}}
        <div class="col-lg-9">

            {{-- Informasi Profil --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-user-edit me-2"></i>

                        Informasi Profil

                    </h5>

                </div>

                <div class="card-body">

                    @include('profile.partials.update-profile-information-form')

                </div>

            </div>

            {{-- Password --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-warning">

                    <h5 class="mb-0 text-dark">

                        <i class="fas fa-lock me-2"></i>

                        Ganti Password

                    </h5>

                </div>

                <div class="card-body">

                    @include('profile.partials.update-password-form')

                </div>

            </div>

            {{-- Hapus Akun --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-danger text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-trash-alt me-2"></i>

                        Hapus Akun

                    </h5>

                </div>

                <div class="card-body">

                    @include('profile.partials.delete-user-form')

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
