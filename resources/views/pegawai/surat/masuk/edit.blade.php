@extends('layouts.pegawai')

@section('title', 'Edit Surat Masuk')

@section('content')
<div class="page-header fade-up">
    <div>
        <h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Surat Masuk</h2>
        <p class="text-muted mb-0">Perbarui data surat masuk draft atau surat yang dikembalikan Admin.</p>
    </div>
    <a href="{{ route('pegawai.surat-masuk.show', $surat) }}" class="btn btn-light border">
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

@if($surat->status === 'dikembalikan' && $surat->catatan_admin)
    <div class="alert alert-danger">
        <strong><i class="bi bi-chat-left-text me-2"></i>Catatan Admin</strong>
        <div class="mt-1">{{ $surat->catatan_admin }}</div>
    </div>
@endif

<form action="{{ route('pegawai.surat-masuk.update', $surat) }}" method="POST" enctype="multipart/form-data" class="incoming-edit-form fade-up">
    @csrf
    @method('PUT')

    <section class="edit-section">
        <div class="section-heading">
            <span><i class="bi bi-envelope-paper"></i></span>
            <div>
                <h4>Identitas Surat Masuk</h4>
                <p>Pastikan informasi sama dengan dokumen yang diterima.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nomor Surat <b class="text-danger">*</b></label>
                <input name="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}" maxlength="100" class="form-control @error('nomor_surat') is-invalid @enderror" required>
                @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Agenda</label>
                <input name="nomor_agenda" value="{{ old('nomor_agenda', $surat->nomor_agenda) }}" maxlength="100" class="form-control @error('nomor_agenda') is-invalid @enderror" placeholder="Opsional">
                @error('nomor_agenda')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Surat <b class="text-danger">*</b></label>
                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $surat->tanggal_surat?->format('Y-m-d')) }}" class="form-control @error('tanggal_surat') is-invalid @enderror" required>
                @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Asal Surat / Pengirim <b class="text-danger">*</b></label>
                <input name="asal_surat" value="{{ old('asal_surat', $surat->asal_surat) }}" maxlength="255" class="form-control @error('asal_surat') is-invalid @enderror" required>
                @error('asal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Perihal <b class="text-danger">*</b></label>
                <input name="perihal" value="{{ old('perihal', $surat->perihal) }}" maxlength="500" class="form-control @error('perihal') is-invalid @enderror" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Tujuan Surat <b class="text-danger">*</b></label>
                <input name="tujuan_surat" value="{{ old('tujuan_surat', $surat->tujuan_surat) }}" maxlength="255" class="form-control @error('tujuan_surat') is-invalid @enderror" required>
                @error('tujuan_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="edit-section">
        <div class="section-heading">
            <span><i class="bi bi-paperclip"></i></span>
            <div>
                <h4>Lampiran dan Catatan</h4>
                <p>Biarkan lampiran kosong jika tidak ingin mengganti berkas.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Deskripsi / Catatan</label>
                <textarea name="deskripsi" rows="5" maxlength="2000" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $surat->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                @if($surat->file_path)
                    <div class="current-file mb-3">
                        <i class="bi bi-file-earmark-check"></i>
                        <div>
                            <strong>Lampiran saat ini</strong>
                            <small>{{ basename($surat->file_path) }}</small>
                        </div>
                        <a href="{{ route('surat.lampiran', $surat) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                    </div>
                @endif
                <label class="form-label">Ganti Lampiran</label>
                <input type="file" name="file_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control @error('file_path') is-invalid @enderror">
                <small class="text-muted">PDF, DOC, DOCX, JPG, JPEG, atau PNG sesuai batas upload sistem.</small>
                @error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <div class="edit-actions">
        <a href="{{ route('pegawai.surat-masuk.index') }}" class="btn btn-light border">Batal</a>
        <button class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
    </div>
</form>
@endsection

@push('styles')
<style>
.incoming-edit-form{max-width:1050px;margin:auto}.edit-section{background:#fff;border:1px solid #e5ebf2;border-radius:18px;padding:25px;margin-bottom:18px;box-shadow:0 8px 24px rgba(15,76,129,.05)}.section-heading{display:flex;align-items:center;gap:13px;padding-bottom:16px;margin-bottom:18px;border-bottom:1px solid #edf1f5}.section-heading>span{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:#eaf3fb;color:#0f4c81}.section-heading h4{font-size:18px;margin:0}.section-heading p{margin:2px 0 0;color:#64748b}.incoming-edit-form .form-label{font-weight:650}.incoming-edit-form .form-control,.incoming-edit-form .form-select{min-height:48px;border-radius:11px}.current-file{display:flex;align-items:center;gap:12px;padding:14px;border-radius:12px;background:#f4f8fc}.current-file>i{font-size:25px;color:#15803d}.current-file>div{flex:1}.current-file strong,.current-file small{display:block}.edit-actions{display:flex;justify-content:flex-end;gap:10px;padding-bottom:22px}@media(max-width:600px){.edit-section{padding:20px}.edit-actions{flex-direction:column-reverse}}
</style>
@endpush
