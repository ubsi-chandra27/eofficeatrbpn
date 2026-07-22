@extends('layouts.admin')

@section('title', 'Edit Disposisi')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-pencil-square text-warning"></i>

            Edit Disposisi

        </h2>

        <p class="text-muted mb-0">

            Perbarui data disposisi surat.

        </p>

    </div>

    <a href="{{ route('admin.disposisi.index') }}"
       class="btn btn-light border">

        <i class="bi bi-arrow-left-circle me-2"></i>

        Kembali

    </a>

</div>

@if ($errors->any())

<div class="alert alert-warning">

    <h5 class="mb-2">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        Terdapat kesalahan input.

    </h5>

    <ul class="mb-0">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<div class="form-card fade-up">

    <div class="card-header">

        <h4>

            Form Edit Disposisi

        </h4>

    </div>

    <div class="card-body">

        <form action="{{ route('admin.disposisi.update',$disposisi->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                {{-- Surat Masuk --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Surat Masuk

                    </label>

                    <select
                        name="surat_id"
                        required
                        class="form-select @error('surat_id') is-invalid @enderror">

                        <option value="">

                            -- Pilih Surat --

                        </option>

                        @foreach($surat as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ old('surat_id',$disposisi->surat_id)==$item->id ? 'selected' : '' }}>

                                {{ $item->nomor_surat }} - {{ $item->perihal }}

                            </option>

                        @endforeach

                    </select>

                    @error('surat_id')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- Tujuan Disposisi --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tujuan Disposisi

                    </label>

                    <select
                        name="pegawai_id[]"
                        multiple
                        required
                        class="form-select @error('pegawai_id') is-invalid @enderror">

                        @foreach($pegawai as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ in_array($item->id, old('pegawai_id', $pegawaiDipilih)) ? 'selected' : '' }}>

                                {{ $item->nama }}{{ $item->jabatan ? ' — '.$item->jabatan->nama : '' }}{{ $item->unitKerja ? ' ('.$item->unitKerja->nama.')' : '' }}

                            </option>

                        @endforeach

                    </select>

                    <small class="text-muted">

                        Cari nama atau jabatan, lalu pilih satu atau beberapa pegawai penerima.

                    </small>

                    @error('pegawai_id')

                        <div class="invalid-feedback d-block">

                            {{ $message }}

                        </div>

                    @enderror

                </div>
                                {{-- Prioritas --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Prioritas

                    </label>

                    <select
                        name="prioritas"
                        required
                        class="form-select @error('prioritas') is-invalid @enderror">

                        <option value="">-- Pilih Prioritas --</option>

                        <option
                            value="Rendah"
                            {{ old('prioritas',$disposisi->prioritas)=='Rendah' ? 'selected' : '' }}>

                            Rendah

                        </option>

                        <option
                            value="Sedang"
                            {{ old('prioritas',$disposisi->prioritas)=='Sedang' ? 'selected' : '' }}>

                            Sedang

                        </option>

                        <option
                            value="Tinggi"
                            {{ old('prioritas',$disposisi->prioritas)=='Tinggi' ? 'selected' : '' }}>

                            Tinggi

                        </option>

                    </select>

                    @error('prioritas')

                        <div class="invalid-feedback d-block">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- Tanggal Disposisi --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Disposisi

                    </label>

                    <input
                        type="date"
                        name="tanggal_disposisi"
                        required
                        value="{{ old('tanggal_disposisi', optional($disposisi->tanggal_disposisi)->format('Y-m-d')) }}"
                        class="form-control @error('tanggal_disposisi') is-invalid @enderror">

                    @error('tanggal_disposisi')

                        <div class="invalid-feedback d-block">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                {{-- Catatan --}}
                <div class="col-12 mb-4">

                    <label class="form-label">

                        Catatan Disposisi

                    </label>

                    <textarea
                        name="catatan"
                        rows="6"
                        maxlength="2000"
                        required
                        class="form-control @error('catatan') is-invalid @enderror"
                        placeholder="Masukkan catatan disposisi...">{{ old('catatan',$disposisi->catatan) }}</textarea>

                    @error('catatan')

                        <div class="invalid-feedback d-block">

                            {{ $message }}

                        </div>

                    @enderror

                    <small class="text-muted">Maksimal 2.000 karakter. Status baca penerima lama tidak akan ter-reset.</small>

                </div>

                <div class="col-12">

                    <hr>

                    <div class="form-action">

                        <a
                            href="{{ route('admin.disposisi.index') }}"
                            class="btn btn-light border">

                            <i class="bi bi-arrow-left-circle me-2"></i>

                            Batal

                        </a>

                        <button
                            type="submit"
                            class="btn btn-warning text-white">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            Simpan Perubahan

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
    background:#fff;
    border-bottom:1px solid #edf2f7;
    padding:24px 30px;
}

.form-card .card-header h4{
    margin:0;
    font-size:24px;
    font-weight:700;
    color:#1e293b;
}

.form-card .card-body{
    padding:30px;
}

.form-label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#334155;
}

.form-control,
.form-select{
    min-height:48px;
    border:1px solid #dbe2ea;
    border-radius:12px;
    transition:.25s;
}

.form-control:focus,
.form-select:focus{
    border-color:#0F4C81;
    box-shadow:0 0 0 .2rem rgba(15,76,129,.15);
}

textarea.form-control{
    min-height:150px;
    resize:vertical;
}

select[multiple]{
    min-height:220px;
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
    padding:10px 20px;
    font-weight:600;
}

.invalid-feedback{
    display:block;
}

.alert{
    border:none;
    border-radius:16px;
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
