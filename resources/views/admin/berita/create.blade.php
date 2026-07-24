@extends('layouts.admin')
@section('title', 'Tambah Berita')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Berita</h2><p class="text-muted mb-0">Buat konten berita atau pengumuman baru.</p></div><a href="{{ route('admin.berita.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">@csrf
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" value="{{ old('judul') }}" class="form-control @error('judul') is-invalid @enderror" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Kategori</label><select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required><option value="pengumuman" @selected(old('kategori')==='pengumuman')>Pengumuman</option><option value="berita" @selected(old('kategori')==='berita')>Berita</option></select>@error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select name="is_published" class="form-select @error('is_published') is-invalid @enderror"><option value="0" @selected(old('is_published')!=='1')>Draft</option><option value="1" @selected(old('is_published')==='1')>Publikasikan</option></select>@error('is_published')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Publikasi</label><input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" class="form-control @error('published_at') is-invalid @enderror">@error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label fw-semibold">Isi</label><textarea name="isi" rows="10" class="form-control @error('isi') is-invalid @enderror" required>{{ old('isi') }}</textarea>@error('isi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label fw-semibold">Gambar / Lampiran (opsional)</label><input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf"><small class="text-muted">Maksimal 2MB. Format: JPG, PNG, PDF.</small>@error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-light border rounded-pill px-4"><i class="bi bi-arrow-left-circle me-2"></i>Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-2"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
