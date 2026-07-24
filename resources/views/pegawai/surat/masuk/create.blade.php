@extends('layouts.pegawai')

@section('title', 'Tambah Surat Masuk')

@section('content')
<div class="page-header fade-up">
    <div>
        <h2><i class="bi bi-inbox-fill text-primary me-2"></i>Tambah Surat Masuk</h2>
        <p class="text-muted mb-0">Catat surat yang diterima, lalu kirim ke Admin untuk diverifikasi.</p>
    </div>
    <a href="{{ route('pegawai.surat-masuk.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data surat masuk.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('pegawai.surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="incoming-employee-form fade-up">
    @csrf

    <section class="incoming-section">
        <div class="section-heading">
            <span><i class="bi bi-envelope-paper"></i></span>
            <div>
                <h4>Identitas Surat Masuk</h4>
                <p>Isi informasi sesuai dokumen surat yang diterima.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nomor Surat <b class="text-danger">*</b></label>
                <input name="nomor_surat" value="{{ old('nomor_surat') }}" maxlength="100" class="form-control @error('nomor_surat') is-invalid @enderror" placeholder="Contoh: 001/SM/VII/2026" required>
                @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Agenda</label>
                <input name="nomor_agenda" value="{{ old('nomor_agenda') }}" maxlength="100" class="form-control @error('nomor_agenda') is-invalid @enderror" placeholder="Opsional, contoh: AGD-001">
                @error('nomor_agenda')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Surat <b class="text-danger">*</b></label>
                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', now()->format('Y-m-d')) }}" class="form-control @error('tanggal_surat') is-invalid @enderror" required>
                @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Asal Surat / Pengirim <b class="text-danger">*</b></label>
                <input name="asal_surat" value="{{ old('asal_surat') }}" maxlength="255" class="form-control @error('asal_surat') is-invalid @enderror" placeholder="Nama instansi atau pengirim surat" required>
                @error('asal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Perihal <b class="text-danger">*</b></label>
                <input name="perihal" value="{{ old('perihal') }}" maxlength="500" class="form-control @error('perihal') is-invalid @enderror" placeholder="Masukkan perihal surat masuk" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Tujuan Surat <b class="text-danger">*</b></label>
                <input name="tujuan_surat" value="{{ old('tujuan_surat', 'Kantor ATR/BPN') }}" maxlength="255" class="form-control @error('tujuan_surat') is-invalid @enderror" placeholder="Unit atau kantor penerima surat" required>
                @error('tujuan_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="incoming-section">
        <div class="section-heading">
            <span><i class="bi bi-paperclip"></i></span>
            <div>
                <h4>Lampiran dan Catatan</h4>
                <p>Tambahkan berkas surat dan catatan internal bila diperlukan.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Deskripsi / Catatan</label>
                <textarea name="deskripsi" rows="5" maxlength="2000" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Ringkasan isi surat atau catatan tambahan">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Lampiran Surat</label>
                <input type="file" name="file_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control @error('file_path') is-invalid @enderror">
                <small class="text-muted">PDF, DOC, DOCX, JPG, JPEG, atau PNG sesuai batas unggahan sistem.</small>
                @error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <div class="verification-note">
        <i class="bi bi-shield-check"></i>
        <div>
            <strong>Alur verifikasi surat masuk</strong>
            <span>Simpan sebagai draft untuk dilanjutkan nanti, atau langsung kirim ke Admin. Surat yang dikirim akan masuk antrean verifikasi Admin.</span>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('pegawai.surat-masuk.index') }}" class="btn btn-light border">Batal</a>
        <button name="submit_action" value="draft" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark me-1"></i>Simpan Draft
        </button>
        <button name="submit_action" value="submit" class="btn btn-primary">
            <i class="bi bi-send-check me-1"></i>Simpan &amp; Kirim ke Admin
        </button>
    </div>
</form>
@endsection

@push('styles')
<style>
.incoming-employee-form{max-width:1100px;margin:auto}.incoming-section{background:#fff;border:1px solid #e5eaf0;border-radius:18px;padding:25px;margin-bottom:18px;box-shadow:0 8px 24px rgba(15,76,129,.05)}.section-heading{display:flex;gap:13px;align-items:center;padding-bottom:16px;margin-bottom:18px;border-bottom:1px solid #edf2f7}.section-heading>span{width:42px;height:42px;border-radius:12px;background:#e8f2fb;color:#0f4c81;display:grid;place-items:center;font-size:19px}.section-heading h4{margin:0;font-size:18px}.section-heading p{margin:2px 0 0;color:#64748b;font-size:14px}.incoming-employee-form .form-label{font-weight:650;color:#334155}.incoming-employee-form .form-control,.incoming-employee-form .form-select{min-height:48px;border-radius:11px;border-color:#dbe2ea}.verification-note{display:flex;gap:13px;padding:16px 18px;background:#eaf4ff;color:#174b75;border-radius:14px;margin-bottom:18px}.verification-note i{font-size:22px}.verification-note strong,.verification-note span{display:block}.verification-note span{font-size:14px}.form-actions{display:flex;justify-content:flex-end;gap:10px;padding-bottom:20px}@media(max-width:767px){.incoming-section{padding:20px}.form-actions{flex-direction:column-reverse}.form-actions .btn{width:100%}}
</style>
@endpush
