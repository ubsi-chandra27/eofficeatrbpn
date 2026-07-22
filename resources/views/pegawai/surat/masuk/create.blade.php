@extends('layouts.pegawai')

@section('title','Tambah Surat Keluar')

@section('content')

<div class="container-fluid">

    {{-- ===========================
        HEADER
    ============================ --}}
    <div class="page-header fade-up">

        <div>

            <h2>

                <i class="bi bi-send-plus-fill text-primary me-2"></i>

                Tambah Surat Keluar

            </h2>

            <p class="text-muted mb-0">

                Buat surat keluar baru kemudian kirim kepada pimpinan yang dituju.

            </p>

        </div>

        <a href="{{ route('pegawai.surat-keluar.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-2"></i>

            Kembali

        </a>

    </div>


    {{-- ===========================
        VALIDASI ERROR
    ============================ --}}
    @if ($errors->any())

        <div class="alert alert-danger shadow-sm">

            <h6 class="mb-3">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                Terjadi kesalahan.

            </h6>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ===========================
        FORM
    ============================ --}}
    <div class="form-card fade-up">

        <form action="{{ route('pegawai.surat-keluar.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="row g-4">

                {{-- Nomor Surat --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Nomor Surat

                    </label>

                    <input
                        type="text"
                        name="nomor_surat"
                        class="form-control"
                        value="{{ old('nomor_surat') }}"
                        placeholder="Masukkan nomor surat"
                        required>

                </div>


                {{-- Tanggal --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Tanggal Surat

                    </label>

                    <input
                        type="date"
                        name="tanggal_surat"
                        class="form-control"
                        value="{{ old('tanggal_surat', date('Y-m-d')) }}"
                        required>

                </div>


                {{-- Perihal --}}
                <div class="col-md-12">

                    <label class="form-label fw-semibold">

                        Perihal

                    </label>

                    <input
                        type="text"
                        name="perihal"
                        class="form-control"
                        value="{{ old('perihal') }}"
                        placeholder="Masukkan perihal surat"
                        required>

                </div>


                {{-- Tujuan Surat --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Tujuan Surat

                    </label>

                    <input
                        type="text"
                        name="tujuan_surat"
                        class="form-control"
                        value="{{ old('tujuan_surat') }}"
                        placeholder="Contoh : Kepala Kantor ATR/BPN"
                        required>

                </div>


                {{-- Jabatan Pimpinan --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Jabatan Pimpinan

                    </label>

                    <select
                        name="jabatan_pimpinan_id"
                        id="jabatan_pimpinan"
                        class="form-select"
                        required>

                        <option value="">

                            -- Pilih Jabatan --

                        </option>

                        @foreach($jabatan as $item)

                            <option
                                value="{{ $item->id }}"
                                data-nama="{{ $item->nama_pimpinan }}"
                                {{ old('jabatan_pimpinan_id') == $item->id ? 'selected' : '' }}>

                                {{ $item->nama }}

                            </option>

                        @endforeach

                    </select>

                </div>

                                {{-- ===========================
                    NAMA PIMPINAN
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Nama Pimpinan

                    </label>

                    <input
                        type="text"
                        id="nama_pimpinan"
                        name="nama_pimpinan"
                        class="form-control"
                        value="{{ old('nama_pimpinan') }}"
                        placeholder="Nama pimpinan akan muncul otomatis"
                        readonly>

                </div>


                {{-- ===========================
                    PENGIRIM
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Pengirim

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ Auth::user()->name }}"
                        readonly>

                </div>


                {{-- ===========================
                    DESKRIPSI
                ============================ --}}
                <div class="col-md-12">

                    <label class="form-label fw-semibold">

                        Isi / Deskripsi Surat

                    </label>

                    <textarea
                        name="deskripsi"
                        rows="8"
                        class="form-control"
                        placeholder="Tuliskan isi surat...">{{ old('deskripsi') }}</textarea>

                </div>


                {{-- ===========================
                    FILE
                ============================ --}}
                <div class="col-md-12">

                    <label class="form-label fw-semibold">

                        Lampiran Surat

                    </label>

                    <input
                        type="file"
                        name="file_path"
                        class="form-control"
                        accept=".pdf,.doc,.docx">

                    <small class="text-muted">

                        Format:
                        PDF, DOC, DOCX

                        Maksimal 5 MB.

                    </small>

                </div>


                {{-- ===========================
                    STATUS
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Status

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="Menunggu"
                        readonly>

                </div>


                {{-- ===========================
                    TANGGAL INPUT
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Tanggal Input

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ now()->format('d F Y H:i') }}"
                        readonly>

                </div>

            </div>

            <hr class="my-4">

            <div class="alert alert-info">

                <i class="bi bi-info-circle-fill me-2"></i>

                Setelah surat disimpan, status akan menjadi
                <strong>Menunggu</strong>.
                Surat dapat diedit sebelum dikirim ke pimpinan.

            </div>

                        {{-- ===========================
                BUTTON
            ============================ --}}
            <div class="d-flex justify-content-between mt-4">

                <a href="{{ route('pegawai.surat-keluar.index') }}"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-2"></i>

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

                        <i class="bi bi-save-fill me-2"></i>

                        Simpan Surat

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded',function(){

    const jabatan =
        document.getElementById('jabatan_pimpinan');

    const pimpinan =
        document.getElementById('nama_pimpinan');

    function isiPimpinan(){

        let selected =
            jabatan.options[jabatan.selectedIndex];

        pimpinan.value =
            selected.dataset.nama ?? '';

    }

    isiPimpinan();

    jabatan.addEventListener(
        'change',
        isiPimpinan
    );

});

</script>

@endpush


@push('styles')

<style>

.form-card{

    background:#fff;

    border-radius:22px;

    padding:35px;

    box-shadow:0 10px 30px rgba(0,0,0,.05);

    border:1px solid #edf2f7;

}

.form-label{

    margin-bottom:8px;

    color:#334155;

}

.form-control,
.form-select{

    border-radius:12px;

    border:1px solid #dbe2ea;

    padding:12px 15px;

}

.form-control:focus,
.form-select:focus{

    border-color:#2563EB;

    box-shadow:0 0 0 .2rem rgba(37,99,235,.15);

}

.btn{

    border-radius:12px;

    padding:10px 22px;

    font-weight:600;

}

.alert{

    border-radius:14px;

}

</style>

@endpush

            {{-- ===========================
                BUTTON
            ============================ --}}
            <div class="d-flex justify-content-between mt-4">

                <a href="{{ route('pegawai.surat-keluar.index') }}"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-2"></i>

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

                        <i class="bi bi-save-fill me-2"></i>

                        Simpan Surat

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded',function(){

    const jabatan =
        document.getElementById('jabatan_pimpinan');

    const pimpinan =
        document.getElementById('nama_pimpinan');

    function isiPimpinan(){

        let selected =
            jabatan.options[jabatan.selectedIndex];

        pimpinan.value =
            selected.dataset.nama ?? '';

    }

    isiPimpinan();

    jabatan.addEventListener(
        'change',
        isiPimpinan
    );

});

</script>

@endpush


@push('styles')

<style>

.form-card{

    background:#fff;

    border-radius:22px;

    padding:35px;

    box-shadow:0 10px 30px rgba(0,0,0,.05);

    border:1px solid #edf2f7;

}

.form-label{

    margin-bottom:8px;

    color:#334155;

}

.form-control,
.form-select{

    border-radius:12px;

    border:1px solid #dbe2ea;

    padding:12px 15px;

}

.form-control:focus,
.form-select:focus{

    border-color:#2563EB;

    box-shadow:0 0 0 .2rem rgba(37,99,235,.15);

}

.btn{

    border-radius:12px;

    padding:10px 22px;

    font-weight:600;

}

.alert{

    border-radius:14px;

}

</style>

@endpush