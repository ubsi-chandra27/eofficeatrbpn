@extends('layouts.admin')

@section('title','Detail Disposisi')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold text-primary mb-1">

                <i class="bi bi-file-earmark-text-fill me-2"></i>

                Detail Disposisi

            </h3>

            <p class="text-muted mb-0">

                Informasi lengkap disposisi surat.

            </p>

        </div>

        <a href="{{ route('admin.disposisi.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>

    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-info-circle-fill me-2"></i>

                Informasi Disposisi

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Nomor Surat

                    </label>

                    <div>

                        {{ $disposisi->surat->nomor_surat ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Judul Surat

                    </label>

                    <div>

                        {{ $disposisi->surat->judul_surat ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Pengirim Disposisi

                    </label>

                    <div>

                        {{ $disposisi->pengirim->name ?? '-' }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Prioritas

                    </label>

                    <div>

                        @if($disposisi->prioritas=='Tinggi')

                            <span class="badge bg-danger">

                                Tinggi

                            </span>

                        @elseif($disposisi->prioritas=='Sedang')

                            <span class="badge bg-warning text-dark">

                                Sedang

                            </span>

                        @else

                            <span class="badge bg-success">

                                Rendah

                            </span>

                        @endif

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Tanggal Disposisi

                    </label>

                    <div>

                        {{ optional($disposisi->tanggal_disposisi)->translatedFormat('d F Y') }}

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold text-muted">

                        Dibuat Pada

                    </label>

                    <div>

                        {{ optional($disposisi->created_at)->translatedFormat('d F Y H:i') }}

                    </div>

                </div>

                <div class="col-12 mb-4">

                    <label class="fw-bold text-muted">

                        Catatan Disposisi

                    </label>

                    <div class="border rounded p-3 bg-light">

                        {!! nl2br(e($disposisi->catatan ?? '-')) !!}

                    </div>

                </div>

                <div class="col-12">

                    <h5 class="fw-bold text-primary">

                        <i class="bi bi-people-fill me-2"></i>

                        Pegawai Tujuan

                    </h5>

                    <hr>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th width="60">

                                        No

                                    </th>

                                    <th>

                                        Nama Pegawai

                                    </th>

                                    <th>

                                        Jabatan

                                    </th>

                                    <th>

                                        Status

                                    </th>

                                    <th>

                                        Dibaca

                                    </th>

                                    <th>

                                        Selesai

                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            @forelse($disposisi->tujuans as $tujuan)

<tr>

    <td>

        {{ $loop->iteration }}

    </td>

    <td>

        <strong>

            {{ $tujuan->pegawai->nama ?? '-' }}

        </strong>

    </td>

    <td>

        {{ $tujuan->pegawai->jabatan->nama ?? '-' }}

    </td>

    <td>

        @if($tujuan->status=='Belum Dibaca')

            <span class="badge bg-warning text-dark">

                <i class="bi bi-envelope me-1"></i>

                Belum Dibaca

            </span>

        @elseif($tujuan->status=='Sudah Dibaca')

            <span class="badge bg-info">

                <i class="bi bi-eye-fill me-1"></i>

                Sudah Dibaca

            </span>

        @elseif($tujuan->status=='Selesai')

            <span class="badge bg-success">

                <i class="bi bi-check-circle-fill me-1"></i>

                Selesai

            </span>

        @else

            <span class="badge bg-secondary">

                {{ $tujuan->status }}

            </span>

        @endif

    </td>

    <td>

        @if($tujuan->dibaca_pada)

            <span class="text-success">

                {{ \Carbon\Carbon::parse($tujuan->dibaca_pada)->format('d-m-Y H:i') }}

            </span>

        @else

            <span class="text-muted">

                -

            </span>

        @endif

    </td>

    <td>

        @if($tujuan->selesai_pada)

            <span class="text-primary">

                {{ \Carbon\Carbon::parse($tujuan->selesai_pada)->format('d-m-Y H:i') }}

            </span>

        @else

            <span class="text-muted">

                -

            </span>

        @endif

    </td>

</tr>

@empty

<tr>

    <td colspan="6" class="text-center py-4">

        <i class="bi bi-inbox display-6 text-muted"></i>

        <br><br>

        Belum ada pegawai tujuan disposisi.

    </td>

</tr>

@endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="card-footer bg-light">

            <div class="d-flex justify-content-between">

                <a href="{{ route('admin.disposisi.index') }}"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left-circle me-1"></i>

                    Kembali

                </a>

                @if($disposisi->is_editable)
                <div>

                    <a href="{{ route('admin.disposisi.edit',$disposisi->id) }}"
                       class="btn btn-warning">

                        <i class="bi bi-pencil-square me-1"></i>

                        Edit Disposisi

                    </a>

                    <form
                        action="{{ route('admin.disposisi.destroy',$disposisi->id) }}"
                        method="POST"
                        class="d-inline">

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Yakin ingin menghapus disposisi ini?')">

                            <i class="bi bi-trash-fill me-1"></i>

                            Hapus

                        </button>

                    </form>

                </div>
                @else
                    <span class="text-muted"><i class="bi bi-lock-fill me-1"></i>Disposisi terkunci karena sudah ditindaklanjuti.</span>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection
