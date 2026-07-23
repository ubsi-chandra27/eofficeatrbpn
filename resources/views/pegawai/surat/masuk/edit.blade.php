@extends('layouts.pegawai')

@section('title','Edit Surat Masuk')

@section('content')

<div class="page-header fade-up">

    <div>

        <h2>

            <i class="bi bi-pencil-square text-warning me-2"></i>

            Edit Surat Masuk

        </h2>

        <p class="text-muted mb-0">

            Perbarui data surat masuk sebelum dikirim.

        </p>

    </div>


    <a href="{{ route('pegawai.surat-masuk.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left me-2"></i>

        Kembali

    </a>

</div>



<div class="card shadow-sm border-0 fade-up">

<div class="card-body p-4">


<form
action="{{ route('pegawai.surat-masuk.update',$surat->id) }}"
method="POST"
enctype="multipart/form-data">


@csrf

@method('PUT')



{{-- NOMOR SURAT --}}
<div class="mb-3">

<label class="form-label fw-semibold">

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




{{-- TANGGAL SURAT --}}
<div class="mb-3">


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





{{-- ASAL SURAT --}}
<div class="mb-3">


<label class="form-label fw-semibold">

Asal Surat

</label>


<input
type="text"
name="asal_surat"
class="form-control"
value="{{ old('asal_surat',$surat->asal_surat) }}"
required>


</div>





{{-- TUJUAN SURAT --}}
<div class="mb-3">


<label class="form-label fw-semibold">

Tujuan Surat

</label>


<input
type="text"
name="tujuan_surat"
class="form-control"
value="{{ old('tujuan_surat',$surat->tujuan_surat) }}"
required>


</div>





{{-- PERIHAL --}}
<div class="mb-3">


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





{{-- DESKRIPSI --}}
<div class="mb-3">


<label class="form-label fw-semibold">

Deskripsi

</label>


<textarea
name="deskripsi"
rows="5"
class="form-control">{{ old('deskripsi',$surat->deskripsi) }}</textarea>


</div>





{{-- FILE LAMA --}}
@if($surat->file_path)


<div class="mb-3">


<label class="form-label fw-semibold">

File Saat Ini

</label>


<div>


<a
href="{{ route('surat.lampiran', $surat) }}"
target="_blank"
class="btn btn-outline-primary btn-sm">


<i class="bi bi-file-earmark-text"></i>

Lihat File


</a>


</div>


</div>


@endif





{{-- UPLOAD FILE --}}
<div class="mb-4">


<label class="form-label fw-semibold">

Ganti File Surat
<span class="text-muted">

(optional)

</span>

</label>


<input
type="file"
name="file_path"
class="form-control">


<small class="text-muted">

Format PDF/DOC/JPG maksimal 5 MB

</small>


</div>





<div class="d-flex justify-content-end gap-2">


<a
href="{{ route('pegawai.surat-masuk.index') }}"
class="btn btn-light">

Batal

</a>



<button
type="submit"
class="btn btn-primary">


<i class="bi bi-save me-2"></i>

Simpan Perubahan


</button>


</div>




</form>


</div>

</div>


@endsection



@push('styles')

<style>


.page-header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;

}



.card{

border-radius:18px;

}



.form-control{

border-radius:12px;
padding:12px;

}



label{

color:#334155;

}



.btn{

border-radius:12px;

}



</style>

@endpush
