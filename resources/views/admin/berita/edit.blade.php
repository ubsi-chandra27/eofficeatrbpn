@extends('layouts.admin')
@section('title', 'Edit Berita')
@section('content')
<div class="page-header"><div><h2><i class="bi bi-pencil-square text-warning me-2"></i>Edit Berita</h2><p class="text-muted mb-0">Perbarui konten berita atau pengumuman.</p></div><a href="{{ route('admin.berita.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Kembali</a></div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Judul</label><input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" class="form-control @error('judul') is-invalid @enderror" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Kategori</label><select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required><option value="pengumuman" @selected(old('kategori', $berita->kategori)==='pengumuman')>Pengumuman</option><option value="berita" @selected(old('kategori', $berita->kategori)==='berita')>Berita</option></select>@error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select name="is_published" class="form-select @error('is_published') is-invalid @enderror"><option value="0" @selected(old('is_published', $berita->is_published)!=='1')>Draft</option><option value="1" @selected(old('is_published', $berita->is_published)==='1')>Publikasikan</option></select>@error('is_published')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Publikasi</label><input type="datetime-local" name="published_at" value="{{ old('published_at', $berita->published_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('published_at') is-invalid @enderror">@error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label fw-semibold">Isi</label><textarea name="isi" rows="10" class="form-control @error('isi') is-invalid @enderror" required>{{ old('isi', $berita->isi) }}</textarea>@error('isi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label fw-semibold">Gambar / Lampiran (opsional)</label>@if($berita->file_path)<div class="mb-2"><img src="{{ asset('storage/'.$berita->file_path) }}" alt="" style="max-height:120px" class="rounded border"></div>@endif<input type="file" name="file_path" class="form-control @error('file_path') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf"><small class="text-muted">Kosongkan jika tidak ingin mengganti lampiran.</small>@error('file_path')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-light border rounded-pill px-4"><i class="bi bi-arrow-left-circle me-2"></i>Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
