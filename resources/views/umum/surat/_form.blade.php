@php($current = $surat ?? null)

@if($errors->any())
    <div class="alert alert-danger shadow-sm"><div><h6 class="mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan.</h6><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
@endif

<div class="form-card fade-up">
    <div class="form-section-title"><i class="bi bi-send-check-fill text-primary me-2"></i>Informasi Pengajuan</div>

    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Nama Pemohon</label>
            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Email Akun</label>
            <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
        </div>
        <div class="col-md-6">
            <label for="kategori_pengajuan" class="form-label fw-semibold">Kategori Pengajuan <span class="text-danger">*</span></label>
            <select id="kategori_pengajuan" name="kategori_pengajuan" class="form-select @error('kategori_pengajuan') is-invalid @enderror" required><option value="">-- Pilih Kategori --</option>@foreach(['Permohonan Informasi','Permohonan Dokumen','Penyampaian Surat','Pengaduan','Lainnya'] as $category)<option value="{{ $category }}" @selected(old('kategori_pengajuan',$current?->kategori_pengajuan ?? request('kategori'))===$category)>{{ $category }}</option>@endforeach</select>
            @error('kategori_pengajuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="nomor_kontak" class="form-label fw-semibold">Nomor Telepon/WhatsApp <span class="text-danger">*</span></label>
            <input id="nomor_kontak" type="tel" name="nomor_kontak" maxlength="25" value="{{ old('nomor_kontak',$current?->nomor_kontak ?? auth()->user()->phone) }}" class="form-control @error('nomor_kontak') is-invalid @enderror" placeholder="Contoh: 0812 3456 7890" required>
            @error('nomor_kontak')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-12">
            <label for="asal_instansi" class="form-label fw-semibold">Asal Instansi/Organisasi <span class="text-muted fw-normal">(opsional)</span></label>
            <input id="asal_instansi" name="asal_instansi" maxlength="255" value="{{ old('asal_instansi',$current?->asal_instansi ?? auth()->user()->organization) }}" class="form-control @error('asal_instansi') is-invalid @enderror" placeholder="Kosongkan jika mengajukan sebagai perorangan">
            @error('asal_instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-12">
            <label for="perihal" class="form-label fw-semibold">Pokok Pengajuan <span class="text-danger">*</span></label>
            <input id="perihal" name="perihal" maxlength="500" value="{{ old('perihal',$current?->perihal) }}" class="form-control @error('perihal') is-invalid @enderror" placeholder="Tuliskan inti permohonan atau pengaduan" required>
            @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-12">
            <label for="deskripsi" class="form-label fw-semibold">Uraian Pengajuan <span class="text-danger">*</span></label>
            <textarea id="deskripsi" name="deskripsi" rows="7" maxlength="2000" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan kebutuhan, informasi pendukung, dan hasil yang Anda harapkan" required>{{ old('deskripsi',$current?->deskripsi) }}</textarea>
            <small class="text-muted">Maksimal 2.000 karakter. Jangan memasukkan password atau data rahasia.</small>
            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-12">
            <label for="file_path" class="form-label fw-semibold">Dokumen Pendukung <span class="text-muted fw-normal">(opsional)</span></label>
            <input id="file_path" type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <small class="text-muted">Format PDF, DOC, DOCX, JPG, atau PNG.@if($editing && $current?->file_path) Lampiran lama tetap digunakan jika tidak memilih file baru.@endif</small>
            @error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <hr class="my-4">
    <div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i><span>Nomor dan tanggal pengajuan dibuat otomatis. Data tidak dapat diubah selama pemeriksaan admin dan dapat diperbaiki apabila dikembalikan.</span></div>

    <div class="d-flex justify-content-between align-items-center mt-4 form-actions">
        <a href="{{ route('umum.surat.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
        <div class="d-flex gap-2 action-buttons">
            @unless($editing)<button type="reset" class="btn btn-warning"><i class="bi bi-arrow-clockwise me-2"></i>Reset</button>@endunless
            <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-2"></i>{{ $editing ? 'Kirim Perbaikan' : 'Kirim Pengajuan' }}</button>
        </div>
    </div>
</div>

<style>
.public-page-header,.public-letter-form{width:100%;max-width:none!important;margin-left:auto;margin-right:auto}.public-page-header{margin-bottom:24px}.public-page-header h3{font-size:28px;font-weight:700;margin:0}.public-page-header p{color:#6c757d;margin:5px 0 0}.form-card{background:#fff;border-radius:22px;padding:35px;box-shadow:0 10px 30px rgba(0,0,0,.05);border:1px solid #edf2f7;margin-bottom:30px}.form-section-title{font-size:20px;font-weight:700;color:#1e293b;margin-bottom:25px;padding-bottom:14px;border-bottom:1px solid #e5e7eb}.public-letter-form .form-label{font-size:14px;margin-bottom:8px;color:#334155}.public-letter-form .form-control,.public-letter-form .form-select{min-height:50px;border-radius:12px;border:1px solid #dbe2ea;padding:12px 15px;font-size:14px}.public-letter-form textarea.form-control{min-height:150px}.public-letter-form .form-control:focus,.public-letter-form .form-select:focus{border-color:#2563eb;box-shadow:0 0 0 .2rem rgba(37,99,235,.15)}.public-letter-form .form-control[readonly]{background:#f8fafc;color:#64748b}.public-letter-form .btn,.public-page-header .btn{border-radius:12px;padding:10px 22px;font-weight:600}.public-letter-form .alert{border-radius:14px;padding:16px 18px}.public-letter-form small{font-size:12px;display:block;margin-top:6px}@media(max-width:768px){.form-card{padding:20px}.public-page-header{align-items:flex-start!important;flex-direction:column}.public-page-header .btn{width:100%}.form-actions{align-items:stretch!important;flex-direction:column}.action-buttons{flex-direction:column}.form-actions .btn{width:100%}}
</style>
