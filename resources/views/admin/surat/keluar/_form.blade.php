@php($current = $surat ?? null)

<section class="outgoing-section">
    <div class="section-heading">
        <span class="section-icon"><i class="bi bi-envelope-paper"></i></span>
        <div><h4>Identitas Surat</h4><p>Nomor, tanggal, dan pokok surat keluar.</p></div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
            <input name="nomor_surat" value="{{ old('nomor_surat', $current?->nomor_surat) }}" class="form-control @error('nomor_surat') is-invalid @enderror" placeholder="Contoh: 012/ADM/VII/2026" required>
            @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $current?->tanggal_surat?->format('Y-m-d')) }}" class="form-control @error('tanggal_surat') is-invalid @enderror" required>
            @error('tanggal_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">Perihal <span class="text-danger">*</span></label>
            <input name="perihal" value="{{ old('perihal', $current?->perihal) }}" class="form-control @error('perihal') is-invalid @enderror" placeholder="Ringkasan pokok surat" required>
            @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Nomor Agenda</label>
            <input name="nomor_agenda" value="{{ old('nomor_agenda', $current?->nomor_agenda) }}" class="form-control" placeholder="Opsional">
        </div>
        <div class="col-md-6">
            <label class="form-label">Penandatangan <span class="text-danger">*</span></label>
            <input name="penandatangan" value="{{ old('penandatangan', $current?->penandatangan) }}" class="form-control @error('penandatangan') is-invalid @enderror" placeholder="Nama dan jabatan penandatangan" required>
            @error('penandatangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</section>

<section class="outgoing-section">
    <div class="section-heading">
        <span class="section-icon"><i class="bi bi-truck"></i></span>
        <div><h4>Tujuan dan Pengiriman</h4><p>Tentukan penerima, metode, dan waktu pengiriman.</p></div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tujuan Surat <span class="text-danger">*</span></label>
            <input name="tujuan_surat" value="{{ old('tujuan_surat', $current?->tujuan_surat) }}" class="form-control @error('tujuan_surat') is-invalid @enderror" placeholder="Instansi atau penerima surat" required>
            @error('tujuan_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Metode Pengiriman <span class="text-danger">*</span></label>
            <select name="metode" class="form-select @error('metode') is-invalid @enderror" required>
                <option value="">Pilih metode pengiriman</option>
                @foreach(['Email', 'Kurir', 'Pos', 'Langsung'] as $method)
                    <option value="{{ $method }}" @selected(old('metode', $current?->metode) === $method)>{{ $method }}</option>
                @endforeach
            </select>
            @error('metode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Keluar</label>
            <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $current?->tanggal_keluar?->format('Y-m-d')) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Kirim</label>
            <input type="date" name="tanggal_kirim" value="{{ old('tanggal_kirim', $current?->tanggal_kirim?->format('Y-m-d')) }}" class="form-control">
        </div>
    </div>
</section>

<section class="outgoing-section">
    <div class="section-heading">
        <span class="section-icon"><i class="bi bi-sliders"></i></span>
        <div><h4>Berkas dan Status</h4><p>Lampiran, catatan, prioritas, dan tahapan surat.</p></div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Lampiran Surat</label>
            <input type="file" name="file_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control @error('file_path') is-invalid @enderror">
            <small class="form-text">PDF, DOC, DOCX, JPG, atau PNG; maksimal 5 MB.</small>
            @error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if($editing && $current?->file_path)<a href="{{ asset('storage/'.$current->file_path) }}" target="_blank" class="btn btn-sm btn-outline-success mt-2"><i class="bi bi-paperclip me-1"></i>Lihat berkas saat ini</a>@endif
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Surat <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                @php($statuses = $editing ? ['draft'=>'Draft','diajukan'=>'Diajukan','diverifikasi'=>'Diverifikasi','diteruskan_ke_pimpinan'=>'Diteruskan ke Pimpinan','terkirim'=>'Terkirim','diarsipkan'=>'Diarsipkan'] : ['draft'=>'Draft','diajukan'=>'Diajukan'])
                @foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(old('status', $current?->status ?? ($defaultStatus ?? 'draft')) === $value)>{{ $label }}</option>@endforeach
            </select>
            <small class="form-text">Simpan sebagai draft bila surat belum siap diproses.</small>
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div class="priority-box w-100">
                <div><strong>Surat prioritas</strong><small>Memerlukan penanganan lebih cepat.</small></div>
                <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" id="priority" name="is_priority" value="1" @checked(old('is_priority', $current?->is_priority))></div>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Deskripsi/Catatan</label>
            <textarea name="deskripsi" rows="3" class="form-control" placeholder="Catatan tambahan surat">{{ old('deskripsi', $current?->deskripsi) }}</textarea>
        </div>
    </div>
</section>

<div class="workflow-note"><i class="bi bi-info-circle-fill"></i><div><strong>Alur surat keluar</strong><span>Draft dapat diperbaiki atau dihapus. Setelah diajukan, surat dipertahankan sebagai histori dan dilanjutkan melalui tahapan verifikasi sampai pengarsipan.</span></div></div>
<div class="form-actions"><a href="{{ route('admin.surat.keluar.index') }}" class="btn btn-light border">Batal</a><button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>{{ $editing ? 'Simpan Perubahan' : 'Simpan Surat' }}</button></div>

@once
@push('styles')
<style>
.admin-outgoing-form{max-width:1100px;margin:0 auto}.outgoing-section{background:#fff;border:1px solid #e5eaf0;border-radius:18px;padding:26px;margin-bottom:18px;box-shadow:0 8px 24px rgba(15,76,129,.05)}
.admin-outgoing-form .section-heading{display:flex;gap:14px;align-items:center;padding-bottom:18px;margin-bottom:20px;border-bottom:1px solid #edf2f7}.admin-outgoing-form .section-heading h4{font-size:18px;margin:0 0 3px;color:#17233b}.admin-outgoing-form .section-heading p{margin:0;color:#64748b;font-size:14px}.admin-outgoing-form .section-icon{width:42px;height:42px;border-radius:12px;background:#eaf8f0;color:#198754;display:grid;place-items:center;font-size:19px}
.admin-outgoing-form .form-label{font-weight:650;color:#334155;margin-bottom:7px}.admin-outgoing-form .form-control,.admin-outgoing-form .form-select{height:48px;min-height:48px;border:1px solid #dbe2ea;border-radius:11px;padding:10px 13px}.admin-outgoing-form textarea.form-control{height:auto;min-height:94px;resize:vertical}.admin-outgoing-form .form-text{display:block;color:#718096;margin-top:6px}.admin-outgoing-form .priority-box{min-height:76px;padding:13px 16px;border:1px solid #dbe2ea;border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:12px}.admin-outgoing-form .priority-box small{display:block;color:#718096}.admin-outgoing-form .priority-box .form-check-input{width:44px;height:23px}
.admin-outgoing-form .choices{margin:0}.admin-outgoing-form .choices[data-type*=select-one] .choices__inner{height:48px!important;min-height:48px!important;padding:7px 12px!important}.admin-outgoing-form .choices__list--dropdown,.admin-outgoing-form .choices__list[aria-expanded]{max-height:270px!important}.admin-outgoing-form .choices__list--dropdown .choices__list,.admin-outgoing-form .choices__list[aria-expanded] .choices__list{max-height:220px!important;overflow-y:auto!important}.admin-outgoing-form .choices__list--dropdown .choices__item,.admin-outgoing-form .choices__list[aria-expanded] .choices__item{height:auto!important;min-height:38px!important;padding:9px 13px!important}
.admin-outgoing-form .workflow-note{display:flex;gap:13px;padding:16px 18px;border-radius:14px;background:#eef9f2;color:#22603b;margin-bottom:18px}.admin-outgoing-form .workflow-note i{font-size:20px}.admin-outgoing-form .workflow-note strong,.admin-outgoing-form .workflow-note span{display:block}.admin-outgoing-form .workflow-note span{font-size:14px;margin-top:2px}.admin-outgoing-form .form-actions{display:flex;justify-content:flex-end;gap:10px;padding:4px 0 20px}.admin-outgoing-form .form-actions .btn{min-width:145px;border-radius:11px;padding:10px 18px}
@media(max-width:767px){.outgoing-section{padding:20px}.admin-outgoing-form .form-actions{flex-direction:column-reverse}.admin-outgoing-form .form-actions .btn{width:100%}}
</style>
@endpush
@endonce
