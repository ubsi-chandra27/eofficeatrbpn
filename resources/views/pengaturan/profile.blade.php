@extends('layouts.admin')

@section('title', 'Profil Administrator')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <div>
            <h3 class="fw-bold text-primary mb-1">
                <i class="fas fa-user-circle me-2"></i>
                Profil Administrator
            </h3>

            <p class="text-muted mb-0">
                Kelola informasi akun administrator.
            </p>
        </div>

        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">

            <strong>
                <i class="fas fa-exclamation-circle me-2"></i>
                Terjadi Kesalahan
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>
    @endif

    <div class="row">

        {{-- FOTO --}}
        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Foto Profil
                    </h5>

                </div>

                <div class="card-body text-center">

                    <div class="rounded-circle shadow mb-3 mx-auto d-grid bg-primary text-white fw-bold"
                         style="width:170px;height:170px;place-items:center;font-size:54px"
                         role="img" aria-label="Inisial {{ auth()->user()->name }}">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <h5 class="fw-bold">
                        {{ auth()->user()->name }}
                    </h5>

                    <span class="badge bg-success">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>

                </div>

            </div>

        </div>

        {{-- FORM --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        Informasi Profil

                    </h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('profile.update') }}"
                          method="POST">

                        @csrf
                        @method('PATCH')

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">
                                    Nama Lengkap
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name', auth()->user()->name) }}"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">
                                    Role
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ ucfirst(auth()->user()->role) }}"
                                    readonly>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">
                                    Bergabung
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ auth()->user()->created_at->format('d F Y') }}"
                                    readonly>

                            </div>

                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">

                            <button
                                class="btn btn-primary">

                                <i class="fas fa-save me-2"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

            {{-- INFORMASI AKUN --}}

            <div class="card shadow-sm border-0 mt-4">

                <div class="card-header bg-secondary text-white">

                    <h5 class="mb-0">

                        Informasi Akun

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>

                            <th width="35%">
                                ID User
                            </th>

                            <td>
                                {{ auth()->user()->id }}
                            </td>

                        </tr>

                        <tr>

                            <th>
                                Nama
                            </th>

                            <td>

                                {{ auth()->user()->name }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Email
                            </th>

                            <td>

                                {{ auth()->user()->email }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Role
                            </th>

                            <td>

                                <span class="badge bg-success">

                                    {{ ucfirst(auth()->user()->role) }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Dibuat

                            </th>

                            <td>

                                {{ auth()->user()->created_at->format('d F Y H:i') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Terakhir Diubah

                            </th>

                            <td>

                                {{ auth()->user()->updated_at->format('d F Y H:i') }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
