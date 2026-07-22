@extends('layouts.pegawai')

@section('title','Edit Surat Keluar')

@section('content')

<div class="container-fluid">

    {{-- ===========================
        HEADER
    ============================ --}}
    <div class="page-header fade-up">

        <div>

            <h2>

                <i class="bi bi-pencil-square text-warning me-2"></i>

                Edit Surat Keluar

            </h2>

            <p class="text-muted mb-0">

                Perbarui data surat keluar sebelum diproses.

            </p>

        </div>

        <a href="{{ route('pegawai.surat-keluar.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-2"></i>

            Kembali

        </a>

    </div>



    {{-- ===========================
        VALIDASI
    ============================ --}}
    @if ($errors->any())

        <div class="alert alert-danger shadow-sm">

            <h6>

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                Terjadi Kesalahan

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

        <form
            action="{{ route('pegawai.surat-keluar.update',$surat->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

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
                        value="{{ old('nomor_surat',$surat->nomor_surat) }}"
                        required>

                </div>



                {{-- Tanggal Surat --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Tanggal Surat

                    </label>

                    <input
                        type="date"
                        name="tanggal_surat"
                        class="form-control"
                        value="{{ old('tanggal_surat',$surat->tanggal_surat) }}"
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
                        value="{{ old('perihal',$surat->perihal) }}"
                        required>

                </div>
                                {{-- ===========================
                    TUJUAN SURAT
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Tujuan Surat

                    </label>

                    <input
                        type="text"
                        name="tujuan_surat"
                        class="form-control"
                        value="{{ old('tujuan_surat',$surat->tujuan_surat) }}"
                        placeholder="Masukkan tujuan surat">

                </div>


                {{-- ===========================
                    JABATAN PIMPINAN
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Jabatan Pimpinan

                    </label>

                    <select
                        name="jabatan_pimpinan_id"
                        id="jabatan_pimpinan"
                        class="form-select">

                        <option value="">

                            -- Pilih Jabatan --

                        </option>

                        @foreach($jabatans as $jabatan)

                            <option
                                value="{{ $jabatan->id }}"
                                data-nama="{{ $jabatan->nama_pimpinan ?? '' }}"
                                {{ old('jabatan_pimpinan_id',$surat->jabatan_pimpinan_id)==$jabatan->id ? 'selected' : '' }}>

                                {{ $jabatan->nama }}

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
                        value="{{ old('nama_pimpinan',$surat->nama_pimpinan) }}"
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
                    STATUS
                ============================ --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">

                        Status Surat

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $surat->status }}"
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
                        placeholder="Tuliskan isi surat...">{{ old('deskripsi',$surat->deskripsi) }}</textarea>

                </div>



                {{-- ===========================
                    FILE LAMA
                ============================ --}}
                <div class="col-md-12">

                    <label class="form-label fw-semibold">

                        Lampiran Saat Ini

                    </label>

                    @if($surat->file_path)

                        <div class="alert alert-light border d-flex justify-content-between align-items-center">

                            <div>

                                <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>

                                {{ basename($surat->file_path) }}

                            </div>

                            <a
                                href="{{ asset('storage/'.$surat->file_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-eye-fill me-1"></i>

                                Lihat File

                            </a>

                        </div>

                    @else

                        <div class="alert alert-warning mb-0">

                            Belum ada lampiran.

                        </div>

                    @endif

                </div>



                {{-- ===========================
                    UPLOAD FILE BARU
                ============================ --}}
                <div class="col-md-12">

                    <label class="form-label fw-semibold">

                        Ganti Lampiran (Opsional)

                    </label>

                    <input
                        type="file"
                        name="file_path"
                        class="form-control"
                        accept=".pdf,.doc,.docx">

                    <small class="text-muted">

                        Kosongkan apabila tidak ingin mengganti file.

                    </small>

                </div>

            </div>

            <hr class="my-4">

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

                        Update Surat

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

    box-shadow:0 10px 35px rgba(0,0,0,.05);

    border:1px solid #edf2f7;

    animation:fadeUp .4s ease;

}

.form-label{

    font-weight:600;

    color:#334155;

    margin-bottom:8px;

}

.form-control,
.form-select{

    border-radius:12px;

    border:1px solid #dbe2ea;

    min-height:48px;

    transition:.25s;

}

.form-control:focus,
.form-select:focus{

    border-color:#2563EB;

    box-shadow:0 0 0 .2rem rgba(37,99,235,.15);

}

textarea.form-control{

    min-height:180px;

}

.btn{

    border-radius:12px;

    padding:10px 22px;

    font-weight:600;

    transition:.25s;

}

.btn:hover{

    transform:translateY(-2px);

}

.alert{

    border-radius:14px;

}

.fade-up{

    animation:fadeUp .4s ease;

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

@media(max-width:768px){

    .d-flex.justify-content-between{

        flex-direction:column;

        gap:15px;

    }

    .d-flex.gap-2{

        width:100%;

        flex-direction:column;

    }

    .btn{

        width:100%;

    }

}

</style>

@endpush