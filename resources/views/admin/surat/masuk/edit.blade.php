@extends('layouts.admin')

@section('title','Edit Surat Masuk')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-pencil-square text-warning"></i>

            Edit Surat Masuk

        </h2>

        <p class="text-muted mb-0">

            Perbarui informasi surat masuk.

        </p>

    </div>

    <a
        href="{{ route('admin.surat.masuk.index') }}"
        class="btn btn-light border">

        <i class="bi bi-arrow-left-circle me-2"></i>

        Kembali

    </a>

</div>

<div class="form-card fade-up">

    <div class="card-header">

        <h4>

            Form Edit Surat Masuk

        </h4>

    </div>

    <div class="card-body">

        <form
            action="{{ route('admin.surat.masuk.update',$surat->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="row">

                {{-- Nomor Surat --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Nomor Surat

                    </label>

                    <input
                        type="text"
                        name="nomor_surat"
                        class="form-control @error('nomor_surat') is-invalid @enderror"
                        value="{{ old('nomor_surat',$surat->nomor_surat) }}"
                        required>

                    @error('nomor_surat')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- Tanggal Surat --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Surat

                    </label>

                    <input
                        type="date"
                        name="tanggal_surat"
                        class="form-control @error('tanggal_surat') is-invalid @enderror"
                        value="{{ old('tanggal_surat', $surat->tanggal_surat?->format('Y-m-d')) }}"
                        required>

                    @error('tanggal_surat')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- Perihal --}}
                <div class="col-12 mb-4">

                    <label class="form-label">

                        Perihal

                    </label>

                    <input
                        type="text"
                        name="perihal"
                        class="form-control @error('perihal') is-invalid @enderror"
                        value="{{ old('perihal',$surat->perihal) }}"
                        required>

                    @error('perihal')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                                {{-- =========================================== --}}
                {{-- ASAL SURAT --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Asal Surat

                    </label>

                    <input
                        type="text"
                        name="asal_surat"
                        class="form-control @error('asal_surat') is-invalid @enderror"
                        value="{{ old('asal_surat', $surat->asal_surat) }}"
                        placeholder="Instansi / Pengirim"
                        required>

                    @error('asal_surat')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- =========================================== --}}
                {{-- NOMOR AGENDA --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Nomor Agenda

                    </label>

                    <input
                        type="text"
                        name="nomor_agenda"
                        class="form-control @error('nomor_agenda') is-invalid @enderror"
                        value="{{ old('nomor_agenda', $surat->nomor_agenda) }}"
                        placeholder="Nomor Agenda">

                    @error('nomor_agenda')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- =========================================== --}}
                {{-- METODE --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Metode Pengiriman

                    </label>

                    <select
                        name="metode"
                        class="form-select @error('metode') is-invalid @enderror"
                        required>

                        <option value="">-- Pilih Metode --</option>

                        <option value="Email"
                            {{ old('metode', $surat->metode) == 'Email' ? 'selected' : '' }}>
                            Email
                        </option>

                        <option value="Kurir"
                            {{ old('metode', $surat->metode) == 'Kurir' ? 'selected' : '' }}>
                            Kurir
                        </option>

                        <option value="Pos"
                            {{ old('metode', $surat->metode) == 'Pos' ? 'selected' : '' }}>
                            Pos
                        </option>

                        <option value="Langsung"
                            {{ old('metode', $surat->metode) == 'Langsung' ? 'selected' : '' }}>
                            Langsung
                        </option>

                    </select>

                </div>

                {{-- =========================================== --}}
                {{-- STATUS --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Status Surat

                    </label>

                    <input class="form-control bg-light" value="{{ $surat->status_label }}" readonly>
                    <small class="text-muted">Status diubah melalui proses verifikasi dan disposisi.</small>

                </div>

                {{-- =========================================== --}}
                {{-- PRIORITAS --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label d-block">

                        Prioritas

                    </label>

                    <div class="form-check form-switch mt-2">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="priority"
                            name="is_priority"
                            value="1"
                            {{ old('is_priority', $surat->is_priority) ? 'checked' : '' }}>

                        <label
                            class="form-check-label"
                            for="priority">

                            Tandai sebagai surat prioritas

                        </label>

                    </div>

                </div>

                {{-- =========================================== --}}
                {{-- FILE SURAT --}}
                {{-- =========================================== --}}

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Upload File Baru

                    </label>

                    <input
                        type="file"
                        name="file_path"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="form-control @error('file_path') is-invalid @enderror">

                    @error('file_path')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                    @if($surat->file_path)

                        <div class="mt-3">

                            <small class="text-muted d-block mb-2">

                                File saat ini:

                            </small>

                            <a
                                href="{{ route('surat.lampiran', $surat) }}"
                                target="_blank"
                                class="btn btn-outline-primary btn-sm">

                                <i class="bi bi-file-earmark-pdf me-2"></i>

                                Lihat File

                            </a>

                        </div>

                    @endif

                </div>

                                {{-- =========================================== --}}
                {{-- DESKRIPSI --}}
                {{-- =========================================== --}}

                <div class="col-12 mb-4">

                    <label class="form-label">

                        Deskripsi

                    </label>

                    <textarea
                        name="deskripsi"
                        rows="5"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Tambahkan deskripsi surat...">{{ old('deskripsi', $surat->deskripsi) }}</textarea>

                    @error('deskripsi')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- =========================================== --}}
                {{-- BUTTON --}}
                {{-- =========================================== --}}

                <div class="col-12">

                    <hr>

                    <div class="form-action">

                        <a
                            href="{{ route('admin.surat.masuk.index') }}"
                            class="btn btn-light border">

                            <i class="bi bi-arrow-left-circle me-2"></i>

                            Batal

                        </a>

                        <button
                            type="submit"
                            class="btn btn-warning text-white">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            Update Surat

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection

@push('styles')

<style>

.form-card{

    background:#fff;

    border-radius:20px;

    overflow:hidden;

    box-shadow:0 10px 30px rgba(15,76,129,.08);

}

.form-card .card-header{

    padding:24px 30px;

    border-bottom:1px solid #edf2f7;

    background:#fff;

}

.form-card .card-header h4{

    margin:0;

    font-weight:700;

    color:#1e293b;

}

.form-card .card-body{

    padding:30px;

}

.form-label{

    font-weight:600;

    color:#334155;

    margin-bottom:8px;

}

.form-control,

.form-select{

    min-height:48px;

    border-radius:12px;

    border:1px solid #dbe2ea;

}

.form-control:focus,

.form-select:focus{

    border-color:#0F4C81;

    box-shadow:0 0 0 .2rem rgba(15,76,129,.15);

}

textarea.form-control{

    min-height:130px;

    resize:vertical;

}

.form-check-input{

    width:48px;

    height:24px;

}

.form-action{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

}

.form-action .btn{

    min-width:180px;

    border-radius:12px;

    padding:10px 18px;

}

@media(max-width:768px){

    .form-card .card-body{

        padding:20px;

    }

    .form-action{

        flex-direction:column;

        align-items:stretch;

    }

    .form-action .btn{

        width:100%;

        min-width:unset;

    }

}

</style>

@endpush
