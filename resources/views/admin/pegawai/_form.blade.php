@php($editing = isset($pegawai))
@if($errors->any())<div class="alert alert-danger"><strong>Data belum dapat disimpan.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="employee-form-card">
    <div class="form-section-title"><span><i class="bi bi-person-vcard"></i></span><div><h4>Identitas Pegawai</h4><p>Data ini sekaligus digunakan pada akun login pegawai.</p></div></div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">NIP <b class="text-danger">*</b></label><input name="nip" value="{{ old('nip',$pegawai->nip ?? '') }}" maxlength="30" class="form-control @error('nip') is-invalid @enderror" required>@error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Nama Lengkap <b class="text-danger">*</b></label><input name="nama" value="{{ old('nama',$pegawai->nama ?? '') }}" maxlength="100" class="form-control @error('nama') is-invalid @enderror" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Email <b class="text-danger">*</b></label><input type="email" name="email" value="{{ old('email',$pegawai->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Nomor HP</label><input name="no_hp" value="{{ old('no_hp',$pegawai->no_hp ?? '') }}" maxlength="20" placeholder="Contoh: 081234567890" class="form-control @error('no_hp') is-invalid @enderror">@error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
</div>
<div class="employee-form-card">
    <div class="form-section-title"><span><i class="bi bi-diagram-3"></i></span><div><h4>Penempatan</h4><p>Tentukan jabatan dan unit kerja aktif pegawai.</p></div></div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Jabatan <b class="text-danger">*</b></label><select name="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror" required><option value="">Pilih Jabatan</option>@foreach($jabatan as $row)<option value="{{ $row->id }}" @selected((string)old('jabatan_id',$pegawai->jabatan_id ?? '')===(string)$row->id)>{{ $row->nama }}</option>@endforeach</select>@error('jabatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Unit Kerja <b class="text-danger">*</b></label><select name="unit_kerja_id" class="form-select @error('unit_kerja_id') is-invalid @enderror" required><option value="">Pilih Unit Kerja</option>@foreach($unitKerja as $row)<option value="{{ $row->id }}" @selected((string)old('unit_kerja_id',$pegawai->unit_kerja_id ?? '')===(string)$row->id)>{{ $row->nama }}</option>@endforeach</select>@error('unit_kerja_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="4" maxlength="1000" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat',$pegawai->alamat ?? '') }}</textarea>@error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
</div>
<div class="employee-form-card">
    <div class="form-section-title"><span><i class="bi bi-shield-lock"></i></span><div><h4>Keamanan Akun</h4><p>{{ $editing ? 'Kosongkan password jika tidak ingin menggantinya.' : 'Buat password awal untuk login pegawai.' }}</p></div></div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Password @unless($editing)<b class="text-danger">*</b>@endunless</label><input type="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" @required(!$editing)>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Konfirmasi Password @unless($editing)<b class="text-danger">*</b>@endunless</label><input type="password" name="password_confirmation" autocomplete="new-password" class="form-control" @required(!$editing)></div>
    </div>
</div>
<div class="employee-form-actions"><a href="{{ route('admin.pegawai.index') }}" class="btn btn-light border">Batal</a><button class="btn btn-primary"><i class="bi bi-save me-2"></i>{{ $editing ? 'Simpan Perubahan' : 'Simpan Pegawai' }}</button></div>

