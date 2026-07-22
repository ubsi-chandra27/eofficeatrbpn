@extends('layouts.admin')

@section('title', 'Tambah Disposisi')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                <i class="bi bi-send-plus text-primary me-2"></i>

                Tambah Disposisi

            </h2>

            <p class="text-muted mb-0">

                Tambahkan disposisi baru untuk surat masuk.

            </p>

        </div>

        <a href="{{ route('admin.disposisi.index') }}"
           class="btn btn-outline-secondary rounded-pill px-4">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>

    {{-- Error --}}
    @if ($errors->any())

        <div class="alert alert-warning shadow-sm border-0 rounded-4 mb-4">

            <div class="d-flex">

                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>

                <div>

                    <h5 class="fw-bold">

                        Terdapat kesalahan input.

                    </h5>

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif

    {{-- Card --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 pt-4">

            <h4 class="fw-bold mb-0">

                Form Disposisi

            </h4>

        </div>

        <div class="card-body p-4">

            <form
                action="{{ route('admin.disposisi.store') }}"
                method="POST">

                @csrf

                <div class="row">

                    {{-- Surat Masuk --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">

                            Surat Masuk

                        </label>

                        <select
                            name="surat_id"
                            class="form-select @error('surat_id') is-invalid @enderror"
                            required>

                            <option value="">

                                -- Pilih Surat --

                            </option>

                            @foreach($surat as $item)

                                <option
                                    value="{{ $item->id }}"
                                    {{ old('surat_id') == $item->id ? 'selected' : '' }}>

                                    {{ $item->nomor_surat }}
                                    -
                                    {{ $item->perihal }}

                                </option>

                            @endforeach

                        </select>

                        @if($surat->isEmpty())
                            <div class="alert alert-info mt-2 mb-0 py-2">
                                Belum ada surat masuk yang siap didisposisikan. Verifikasi surat terlebih dahulu.
                            </div>
                        @endif

                        @error('surat_id')
 
                            <div class="invalid-feedback">
 
                                {{ $message }}
 
                            </div>
 
                        @enderror
 
                        <small class="text-muted">
 
                            Hanya surat yang telah diverifikasi atau diteruskan ke pimpinan yang dapat dipilih.
 
                        </small>
 
                    </div>

                    {{-- Tujuan Disposisi --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">

                            Tujuan Disposisi

                        </label>

                        <select
                            name="pegawai_id[]"
                            multiple
                            class="form-select @error('pegawai_id') is-invalid @enderror"
                            required>

                            @foreach($pegawai as $item)

                                <option
                                    value="{{ $item->id }}"
                                    {{ in_array($item->id, old('pegawai_id', [])) ? 'selected' : '' }}>

                                    {{ $item->nama }}{{ $item->jabatan ? ' — '.$item->jabatan->nama : '' }}{{ $item->unitKerja ? ' ('.$item->unitKerja->nama.')' : '' }}

                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted">

                            Cari nama atau jabatan, lalu pilih satu atau beberapa pegawai penerima.

                        </small>

                        @error('pegawai_id')

                            <div class="text-danger mt-2">

                                {{ $message }}

                            </div>

                        @enderror

                        @if($pegawai->isEmpty())
                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                Belum ada profil pegawai yang terhubung dengan akun login.
                            </div>
                        @endif

                    </div>

                                        {{-- Prioritas --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">

                            Prioritas

                        </label>

                        <select
                            name="prioritas"
                            class="form-select @error('prioritas') is-invalid @enderror"
                            required>

                            <option value="">

                                -- Pilih Prioritas --

                            </option>

                            <option
                                value="Rendah"
                                {{ old('prioritas')=='Rendah' ? 'selected' : '' }}>

                                Rendah

                            </option>

                            <option
                                value="Sedang"
                                {{ old('prioritas')=='Sedang' ? 'selected' : '' }}>

                                Sedang

                            </option>

                            <option
                                value="Tinggi"
                                {{ old('prioritas')=='Tinggi' ? 'selected' : '' }}>

                                Tinggi

                            </option>

                        </select>

                        @error('prioritas')

                            <div class="invalid-feedback d-block">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">

                            Status

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Belum Dibaca"
                            readonly>

                        <small class="text-muted">

                            Status dibuat otomatis oleh sistem setelah disposisi disimpan.

                        </small>

                    </div>

                    {{-- Tanggal Disposisi --}}
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold">

                            Tanggal Disposisi

                        </label>

                        <input
                            type="date"
                            name="tanggal_disposisi"
                            value="{{ old('tanggal_disposisi', now()->format('Y-m-d')) }}"
                            class="form-control @error('tanggal_disposisi') is-invalid @enderror"
                            required>

                        @error('tanggal_disposisi')

                            <div class="invalid-feedback d-block">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>

                    {{-- Catatan --}}
                    <div class="col-12 mb-4">

                        <label class="form-label fw-semibold">

                            Catatan Disposisi

                        </label>

                        <textarea
                            name="catatan"
                            rows="6"
                            maxlength="2000"
                            required
                            class="form-control @error('catatan') is-invalid @enderror"
                            placeholder="Masukkan catatan disposisi...">{{ old('catatan') }}</textarea>
                             @error('catatan')

                            <div class="invalid-feedback d-block">

                                {{ $message }}

                            </div>

                        @enderror

                        <small class="text-muted">Maksimal 2.000 karakter. Tuliskan instruksi dan hasil yang diharapkan secara jelas.</small>

                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('admin.disposisi.index') }}"
                        class="btn btn-light border rounded-pill px-4">

                        <i class="bi bi-arrow-left-circle me-2"></i>

                        Batal

                    </a>

                    <button
                        type="submit"
                        {{ $surat->isEmpty() || $pegawai->isEmpty() ? 'disabled' : '' }}
                        class="btn btn-primary rounded-pill px-4">

                        <i class="bi bi-send-check-fill me-2"></i>

                        Kirim Disposisi

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('styles')

<style>

.card{
    border-radius:20px;
}

.card-header{
    border-bottom:1px solid #eef2f7;
}

.form-label{
    color:#334155;
    margin-bottom:8px;
}

.form-control,
.form-select{
    border-radius:12px;
    min-height:48px;
    border:1px solid #d9dee5;
}

.form-control:focus,
.form-select:focus{

    border-color:#0d6efd;

    box-shadow:0 0 0 .2rem rgba(13,110,253,.15);

}

textarea.form-control{

    min-height:160px;

    resize:vertical;

}

select[multiple]{

    min-height:220px;

}

.btn{

    min-width:170px;

}

@media(max-width:768px){

    .btn{

        width:100%;

    }

    .d-flex.justify-content-end{

        flex-direction:column;

    }

}

</style>

@endpush
