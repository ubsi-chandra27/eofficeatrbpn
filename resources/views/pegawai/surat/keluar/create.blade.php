@extends('layouts.pegawai')

@section('title','Tambah Surat Keluar')

@section('content')

<div class="container-fluid">

    {{-- =========================
        PAGE HEADER
    ========================== --}}
    <div class="page-header fade-up">

        <div>

            <h2>

                <i class="bi bi-send-plus-fill text-primary me-2"></i>

                Tambah Surat Keluar

            </h2>

            <p class="text-muted mb-0">

                Buat surat keluar baru untuk dikirim kepada pimpinan atau instansi tujuan.

            </p>

        </div>

        <a href="{{ route('pegawai.surat-keluar.index') }}"
           class="btn btn-light shadow-sm">

            <i class="bi bi-arrow-left-circle me-2"></i>

            Kembali

        </a>

    </div>


    {{-- =========================
        ALERT
    ========================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>

                <i class="bi bi-x-circle-fill me-2"></i>

                Terjadi kesalahan.

            </strong>

            <hr>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================
        FORM CARD
    ========================== --}}

    <form
        action="{{ route('pegawai.surat-keluar.store') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        <div class="form-card fade-up">

            <div class="card-header-custom">

                <h4>

                    <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>

                    Informasi Surat

                </h4>

                <small>

                    Lengkapi data surat sebelum dikirim.

                </small>

            </div>


            <div class="card-body-custom">

                <div class="row g-4">

                    {{-- Nomor Surat --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            Nomor Surat

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="nomor_surat"
                            class="form-control @error('nomor_surat') is-invalid @enderror"
                            value="{{ old('nomor_surat') }}"
                            placeholder="Masukkan nomor surat">

                        @error('nomor_surat')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    {{-- Tanggal Surat --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            Tanggal Surat

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_surat"
                            class="form-control @error('tanggal_surat') is-invalid @enderror"
                            value="{{ old('tanggal_surat', date('Y-m-d')) }}">

                        @error('tanggal_surat')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>
                                        {{-- =========================
                        PENGIRIM
                    ========================== --}}

                    <div class="col-md-6">

                        <label class="form-label">

                            Nama Pengirim

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ auth()->user()->name }}"
                            readonly>

                    </div>



                    <div class="col-md-6">

                        <label class="form-label">

                            Jabatan Pengirim

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ auth()->user()->pegawai->jabatan->nama ?? '-' }}"
                            readonly>

                    </div>



                    {{-- =========================
                        TUJUAN SURAT
                    ========================== --}}

                    <div class="col-md-6">

                        <label class="form-label">

                            Pimpinan / Penandatangan

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="pimpinan_pegawai_id"
                            size="6"
                            required
                            class="form-select pimpinan-select @error('pimpinan_pegawai_id') is-invalid @enderror">

                            <option value="">

                                -- Pilih nama dan jabatan pimpinan --

                            </option>

                            @foreach($pimpinans as $pimpinan)

                                <option
                                    value="{{ $pimpinan->id }}"
                                    {{ old('pimpinan_pegawai_id')==$pimpinan->id ? 'selected' : '' }}>

                                    {{ $pimpinan->nama }} — {{ $pimpinan->jabatan->nama }}

                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted">Daftar dapat di-scroll. Nama dan jabatan disimpan otomatis.</small>

                        @error('pimpinan_pegawai_id')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    <div class="col-md-6">

                        <label class="form-label">

                            Tujuan Instansi

                        </label>

                        <input
                            type="text"
                            name="tujuan_surat"
                            class="form-control @error('tujuan_surat') is-invalid @enderror"
                            value="{{ old('tujuan_surat') }}"
                            placeholder="Contoh: Kantor Wilayah atau unit tujuan">

                        @error('tujuan_surat')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    {{-- =========================
                        KODE SURAT
                    ========================== --}}

                    <div class="col-md-6">

                        <label class="form-label">

                            Kode Surat

                        </label>

                        <input
                            type="text"
                            name="kode_surat"
                            class="form-control @error('kode_surat') is-invalid @enderror"
                            value="{{ old('kode_surat') }}"
                            placeholder="Contoh : 005/ATR">

                        @error('kode_surat')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    <div class="col-md-6">

                        <label class="form-label">

                            Perihal

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="perihal"
                            class="form-control @error('perihal') is-invalid @enderror"
                            value="{{ old('perihal') }}"
                            placeholder="Masukkan perihal surat">

                        @error('perihal')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>
                                        {{-- =========================
                        ISI / DESKRIPSI SURAT
                    ========================== --}}

                    <div class="col-12">

                        <label class="form-label">

                            Isi / Deskripsi Surat

                        </label>

                        <textarea
                            name="deskripsi"
                            rows="8"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Tuliskan isi surat secara lengkap...">{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    {{-- =========================
                        LAMPIRAN
                    ========================== --}}

                    <div class="col-md-12">

                        <label class="form-label">

                            Lampiran Surat

                        </label>

                        <input
                            type="file"
                            name="file_path"
                            class="form-control @error('file_path') is-invalid @enderror">

                        <small class="text-muted">

                            Format: PDF, DOC, DOCX, JPG, PNG (Maks. 5 MB)

                        </small>

                        @error('file_path')

                            <div class="invalid-feedback d-block">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>

                </div>

            </div>


            {{-- =========================
                FOOTER BUTTON
            ========================== --}}

            <div class="card-footer-custom">

                <a href="{{ route('pegawai.surat-keluar.index') }}"
                   class="btn btn-light">

                    <i class="bi bi-arrow-left-circle me-2"></i>

                    Kembali

                </a>


                <div class="d-flex gap-2">

                    {{-- Simpan Draft --}}
                    <button
                        type="submit"
                        name="status"
                        value="draft"
                        class="btn btn-warning">

                        <i class="bi bi-save2-fill me-2"></i>

                        Simpan Draft

                    </button>


                    {{-- Kirim Surat --}}
                    <button
                        type="submit"
                        name="status"
                        value="diajukan"
                        class="btn btn-primary">

                        <i class="bi bi-send-fill me-2"></i>

                        Kirim Surat

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


@endsection

@push('styles')

<style>

/* =====================================================
   PAGE HEADER
===================================================== */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:30px;
    animation:fadeUp .45s ease;
}

.page-header h2{
    font-size:32px;
    font-weight:700;
    color:#0f172a;
}

.page-header p{
    color:#64748b;
    margin-bottom:0;
}

/* =====================================================
   CARD
===================================================== */

.form-card{
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    border:1px solid #e5e7eb;
    box-shadow:0 15px 40px rgba(0,0,0,.05);
    animation:fadeUp .5s ease;
}

.card-header-custom{
    padding:25px 30px;
    border-bottom:1px solid #edf2f7;
    background:#ffffff;
}

.card-header-custom h4{
    font-weight:700;
    margin-bottom:4px;
}

.card-header-custom small{
    color:#64748b;
}

.card-body-custom{
    padding:30px;
}

.card-footer-custom{
    padding:22px 30px;
    border-top:1px solid #edf2f7;
    background:#fafafa;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* =====================================================
   LABEL
===================================================== */

.form-label{
    font-weight:600;
    color:#334155;
    margin-bottom:8px;
}

/* =====================================================
   INPUT
===================================================== */

.form-control,
.form-select{

    height:50px;

    border-radius:14px;

    border:1px solid #dbe2ea;

    transition:.25s;

    box-shadow:none;

}

textarea.form-control{

    min-height:180px;

    resize:vertical;

    padding-top:15px;

}

.form-control:focus,
.form-select:focus{

    border-color:#2563EB;

    box-shadow:0 0 0 4px rgba(37,99,235,.08);

}

.form-control[readonly]{

    background:#f8fafc;

}

/* =====================================================
   FILE
===================================================== */

input[type=file]{

    padding:12px;

}

/* =====================================================
   BUTTON
===================================================== */

.btn{

    border-radius:14px;

    padding:11px 22px;

    font-weight:600;

    transition:.25s;

}

.btn:hover{

    transform:translateY(-2px);

}

.btn-primary{

    background:#2563EB;

    border:none;

}

.btn-primary:hover{

    background:#1d4ed8;

}

.btn-warning{

    color:#fff;

    border:none;

    background:#f59e0b;

}

.btn-warning:hover{

    background:#d97706;

}

.btn-light{

    background:#fff;

    border:1px solid #dbe2ea;

}

.btn-light:hover{

    background:#f8fafc;

}

/* =====================================================
   ALERT
===================================================== */

.alert{

    border:none;

    border-radius:16px;

    padding:16px 20px;

    box-shadow:0 10px 25px rgba(0,0,0,.05);

}

/* =====================================================
   INVALID
===================================================== */

.invalid-feedback{

    display:block;

}

/* =====================================================
   ROW SPACING
===================================================== */

.row.g-4>*{

    margin-top:5px;

}

/* =====================================================
   ANIMATION
===================================================== */

.fade-up{

    animation:fadeUp .45s ease;

}

@keyframes fadeUp{

from{

opacity:0;
transform:translateY(15px);

}

to{

opacity:1;
transform:translateY(0);

}

}

/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:992px){

.card-footer-custom{

flex-direction:column;
gap:15px;

}

}

@media(max-width:768px){

.page-header{

flex-direction:column;
align-items:flex-start;

}

.card-header-custom,
.card-body-custom,
.card-footer-custom{

padding:20px;

}

.page-header h2{

font-size:26px;

}

}

@media(max-width:576px){

.btn{

width:100%;

}

.card-footer-custom{

align-items:stretch;

}

.card-footer-custom .d-flex{

width:100%;
flex-direction:column;

}

}

</style>

@endpush
