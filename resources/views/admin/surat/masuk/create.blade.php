@extends('layouts.admin')

@section('title', 'Catat Surat Masuk')

@section('content')
<div class="page-header fade-up">
    <div>
        <h2><i class="bi bi-inbox-fill text-primary me-2"></i>Catat Surat Masuk</h2>
        <p class="text-muted mb-0">Registrasikan surat yang telah diterima oleh bagian administrasi.</p>
    </div>
    <a href="{{ route('admin.surat.masuk.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger shadow-sm">
        <strong><i class="bi bi-exclamation-circle me-2"></i>Periksa kembali data berikut:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('admin.surat.masuk.store') }}" method="POST" enctype="multipart/form-data" class="admin-letter-form fade-up">
    @csrf

    <section class="form-section">
        <div class="section-heading">
            <span class="section-icon"><i class="bi bi-envelope-paper"></i></span>
            <div><h4>Identitas Surat</h4><p>Data yang tercantum pada dokumen asli.</p></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                <input name="nomor_surat" value="{{ old('nomor_surat') }}" class="form-control @error('nomor_surat') is-invalid @enderror" placeholder="Contoh: 012/UND/VII/2026" required>
                @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat') }}" class="form-control @error('tanggal_surat') is-invalid @enderror" required>
                @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Perihal <span class="text-danger">*</span></label>
                <input name="perihal" value="{{ old('perihal') }}" class="form-control @error('perihal') is-invalid @enderror" placeholder="Ringkasan pokok surat" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Instansi/Pengirim <span class="text-danger">*</span></label>
                <input name="asal_surat" value="{{ old('asal_surat') }}" class="form-control @error('asal_surat') is-invalid @enderror" placeholder="Nama instansi atau pengirim" required>
                @error('asal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Agenda</label>
                <input name="nomor_agenda" value="{{ old('nomor_agenda') }}" class="form-control @error('nomor_agenda') is-invalid @enderror" placeholder="Opsional, sesuai buku agenda">
                @error('nomor_agenda')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="section-heading">
            <span class="section-icon"><i class="bi bi-journal-check"></i></span>
            <div><h4>Informasi Penerimaan</h4><p>Diisi admin berdasarkan cara surat diterima.</p></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Metode Penerimaan <span class="text-danger">*</span></label>
                <select name="metode" class="form-select @error('metode') is-invalid @enderror" required>
                    <option value="">Pilih metode penerimaan</option>
                    @foreach(['Email', 'Kurir', 'Pos', 'Langsung'] as $metode)
                        <option value="{{ $metode }}" @selected(old('metode') === $metode)>{{ $metode }}</option>
                    @endforeach
                </select>
                @error('metode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <small class="form-text">Dropdown dapat dicari dan daftar dibatasi agar tetap ringkas.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Petugas Pencatat</label>
                <input class="form-control bg-light" value="{{ auth()->user()->name }} — Administrator" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Berkas Surat</label>
                <input type="file" name="file_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control @error('file_path') is-invalid @enderror">
                <small class="form-text">PDF, DOC, DOCX, JPG, atau PNG; maksimal 5 MB.</small>
                @error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div class="priority-box w-100">
                    <div><strong>Surat prioritas</strong><small>Aktifkan jika perlu segera ditindaklanjuti.</small></div>
                    <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" id="priority" name="is_priority" value="1" @checked(old('is_priority'))></div>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan Administrasi</label>
                <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Kondisi berkas, jumlah lampiran, atau informasi penerimaan lainnya">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <div class="workflow-note">
        <i class="bi bi-info-circle-fill"></i>
        <div><strong>Alur admin</strong><span>Surat langsung tercatat sebagai <b>Diajukan</b> untuk diverifikasi. Tujuan pimpinan dipilih saat membuat disposisi, bukan pada form penerimaan ini.</span></div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.surat.masuk.index') }}" class="btn btn-light border">Batal</a>
        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-2"></i>Simpan dan Catat</button>
    </div>
</form>
@endsection

@push('styles')
<style>
.admin-letter-form{max-width:1100px;margin:0 auto}.form-section{background:#fff;border:1px solid #e5eaf0;border-radius:18px;padding:26px;margin-bottom:18px;box-shadow:0 8px 24px rgba(15,76,129,.05)}
.section-heading{display:flex;gap:14px;align-items:center;padding-bottom:18px;margin-bottom:20px;border-bottom:1px solid #edf2f7}.section-heading h4{font-size:18px;margin:0 0 3px;color:#17233b}.section-heading p{margin:0;color:#64748b;font-size:14px}.section-icon{width:42px;height:42px;border-radius:12px;background:#e8f2fb;color:#0f4c81;display:grid;place-items:center;font-size:19px}
.form-label{font-weight:650;color:#334155;margin-bottom:7px}.form-control,.form-select{min-height:48px;border:1px solid #dbe2ea;border-radius:11px;padding:10px 13px}.form-text{display:block;color:#718096;margin-top:6px}.priority-box{min-height:72px;padding:13px 16px;border:1px solid #dbe2ea;border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:12px}.priority-box small{display:block;color:#718096;margin-top:3px}.priority-box .form-check-input{width:44px;height:23px}
.workflow-note{display:flex;gap:13px;padding:16px 18px;border-radius:14px;background:#eaf4ff;color:#174b75;margin-bottom:18px}.workflow-note i{font-size:20px}.workflow-note strong,.workflow-note span{display:block}.workflow-note span{font-size:14px;margin-top:2px}.form-actions{display:flex;justify-content:flex-end;gap:10px;padding:4px 0 20px}.form-actions .btn{min-width:145px;border-radius:11px;padding:10px 18px}
.admin-letter-form .choices{margin:0}.admin-letter-form .choices[data-type*=select-one] .choices__inner{height:48px!important;min-height:48px!important}.admin-letter-form .choices__list--dropdown,.admin-letter-form .choices__list[aria-expanded]{max-height:270px!important}.admin-letter-form .choices__list--dropdown .choices__list,.admin-letter-form .choices__list[aria-expanded] .choices__list{max-height:220px!important;overflow-y:auto!important}.admin-letter-form .choices__list--dropdown .choices__item,.admin-letter-form .choices__list[aria-expanded] .choices__item{padding:9px 13px!important;min-height:38px!important;height:auto!important}
@media(max-width:767px){.form-section{padding:20px}.form-actions{flex-direction:column-reverse}.form-actions .btn{width:100%}}
</style>
@endpush
