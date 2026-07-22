@extends('layouts.admin')

@section('title','Tambah Pegawai')

@section('content')

{{-- ===================================================== --}}
{{-- PAGE HEADER --}}
{{-- ===================================================== --}}

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-person-plus-fill text-primary"></i>

            Tambah Pegawai

        </h2>

        <p class="text-muted mb-0">

            Tambahkan data pegawai baru beserta akun loginnya ke sistem.

        </p>

    </div>

    <div>

        <a
            href="{{ route('admin.pegawai.index') }}"
            class="btn btn-light border">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>

</div>

{{-- ===================================================== --}}
{{-- FORM CARD --}}
{{-- ===================================================== --}}

<div class="form-card fade-up">

    <div class="card-header border-0 bg-white">

        <h4 class="mb-0">

            Informasi Pegawai

        </h4>

    </div>

    <div class="card-body">

        <form
            action="{{ route('admin.pegawai.store') }}"
            method="POST">

            @csrf

            <div class="row">

                {{-- ======================== --}}
                {{-- NIP --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        NIP

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="nip"
                        value="{{ old('nip') }}"
                        class="form-control @error('nip') is-invalid @enderror"
                        placeholder="Masukkan NIP">

                    @error('nip')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- ======================== --}}
                {{-- NAMA --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Nama Pegawai

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        class="form-control @error('nama') is-invalid @enderror"
                        placeholder="Masukkan Nama Pegawai">

                    @error('nama')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                                {{-- ======================== --}}
                {{-- EMAIL --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Email

                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="contoh@email.com">

                    @error('email')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- ======================== --}}
                {{-- NO HP --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Nomor HP

                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="{{ old('no_hp') }}"
                        class="form-control @error('no_hp') is-invalid @enderror"
                        placeholder="08xxxxxxxxxx">

                    @error('no_hp')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Password Login <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Minimal 8 karakter.</small>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                {{-- ======================== --}}
                {{-- JABATAN --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Jabatan

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="jabatan_id"
                        class="form-select @error('jabatan_id') is-invalid @enderror">

                        <option value="">

                            -- Pilih Jabatan --

                        </option>

                        @foreach($jabatan as $row)

                            <option
                                value="{{ $row->id }}"
                                @selected(old('jabatan_id') == $row->id)>

                                {{ $row->nama }}

                            </option>

                        @endforeach

                    </select>

                    @error('jabatan_id')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- ======================== --}}
                {{-- UNIT KERJA --}}
                {{-- ======================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Unit Kerja

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="unit_kerja_id"
                        class="form-select @error('unit_kerja_id') is-invalid @enderror">

                        <option value="">

                            -- Pilih Unit Kerja --

                        </option>

                        @foreach($unitKerja as $row)

                            <option
                                value="{{ $row->id }}"
                                @selected(old('unit_kerja_id') == $row->id)>

                                {{ $row->nama }}

                            </option>

                        @endforeach

                    </select>

                    @error('unit_kerja_id')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- ======================== --}}
                {{-- ALAMAT --}}
                {{-- ======================== --}}

                <div class="col-12 mb-4">

                    <label class="form-label">

                        Alamat

                    </label>

                    <textarea
                        name="alamat"
                        rows="4"
                        class="form-control @error('alamat') is-invalid @enderror"
                        placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>

                    @error('alamat')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                                {{-- ===================================================== --}}
                {{-- FORM ACTION --}}
                {{-- ===================================================== --}}

                <div class="col-12">

                    <hr>

                    <div class="form-action">

                        <a
                            href="{{ route('admin.pegawai.index') }}"
                            class="btn btn-light border">

                            <i class="bi bi-arrow-left-circle me-2"></i>

                            Kembali

                        </a>

                        <div class="d-flex gap-2">

                            <button
                                type="reset"
                                class="btn btn-warning">

                                <i class="bi bi-arrow-clockwise me-2"></i>

                                Reset

                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-save2-fill me-2"></i>

                                Simpan Pegawai

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection

{{-- ===================================================== --}}
{{-- PAGE STYLE --}}
{{-- ===================================================== --}}

@push('styles')

<style>

.form-card{

    background:#fff;

    border-radius:20px;

    box-shadow:0 12px 30px rgba(0,0,0,.06);

    overflow:hidden;

}

.form-card .card-header{

    padding:24px 30px;

    border-bottom:1px solid #eef2f7;

}

.form-card .card-body{

    padding:30px;

}

.form-label{

    font-weight:600;

    margin-bottom:8px;

}

.form-control,

.form-select{

    height:50px;

    border-radius:12px;

}

textarea.form-control{

    min-height:120px;

    resize:vertical;

}

.form-control:focus,

.form-select:focus{

    border-color:#0F4C81;

    box-shadow:0 0 0 .2rem rgba(15,76,129,.12);

}

.form-action{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-top:15px;

}

@media(max-width:768px){

    .form-action{

        flex-direction:column;

        gap:15px;

        align-items:stretch;

    }

    .form-action .btn,

    .form-action .d-flex{

        width:100%;

    }

    .form-action .d-flex{

        display:grid !important;

        grid-template-columns:1fr 1fr;

    }

}

</style>

@endpush
