@extends('layouts.umum')

@section('title','Detail Surat')

@section('content')

<div class="container py-4">
    @php
        $processStage = match ($surat->status) {
            'menunggu', 'diajukan' => 'Menunggu verifikasi Admin',
            'diverifikasi' => 'Sudah diverifikasi Admin',
            'diproses' => 'Sedang ditindaklanjuti',
            'diteruskan_ke_pimpinan' => 'Diteruskan ke pimpinan',
            'dikembalikan', 'ditolak' => 'Harus diperbaiki dan dikirim ulang',
            'selesai', 'terkirim', 'diarsipkan' => 'Proses telah selesai',
            default => $surat->status_label,
        };
    @endphp

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <h4 class="mb-0">

                Detail Surat

            </h4>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="220">Nomor Surat</th>

                    <td>{{ $surat->nomor_surat }}</td>

                </tr>

                <tr>

                    <th>Kategori Pengajuan</th>

                    <td>{{ $surat->kategori_pengajuan ?? 'Pengajuan Umum' }}</td>

                </tr>

                <tr>
                    <th>Nomor Kontak</th>
                    <td>{{ $surat->nomor_kontak ?: '-' }}</td>
                </tr>

                <tr>
                    <th>Asal Instansi/Organisasi</th>
                    <td>{{ $surat->asal_instansi ?: 'Perorangan' }}</td>
                </tr>

                <tr>

                    <th>Tanggal Surat</th>

                    <td>

                        {{ $surat->tanggal_surat?->format('d-m-Y') }}

                    </td>

                </tr>

                <tr>

                    <th>Pokok Pengajuan</th>

                    <td>{{ $surat->perihal }}</td>

                </tr>

                <tr>

                    <th>Uraian Pengajuan</th>

                    <td>{{ $surat->deskripsi }}</td>

                </tr>

                <tr>

                    <th>Status</th>

                    <td>

                        <span class="badge bg-{{ $surat->status_badge }}">{{ $surat->status_label }}</span>

                    </td>

                </tr>
                <tr>
                    <th>Tahap Proses</th>
                    <td>{{ $processStage }}</td>
                </tr>
                <tr>
                    <th>Catatan Admin</th>
                    <td>
                        @if($surat->catatan_admin)
                            <div class="alert alert-warning py-2 mb-0">{{ $surat->catatan_admin }}</div>
                        @else
                            <span class="text-muted">Belum ada catatan dari Admin.</span>
                        @endif
                    </td>
                </tr>

                @if($surat->file_path)

                <tr>

                    <th>File Surat</th>

                    <td>

                        <a href="{{ route('umum.surat.download', $surat->id) }}"
                           class="btn btn-success btn-sm">

                            <i class="bi bi-download me-1"></i> Unduh Lampiran

                        </a>

                    </td>

                </tr>

                @endif

            </table>

        </div>

    </div>

    <div class="card shadow-sm mt-4">

        <div class="card-header">

            <h5 class="mb-0">

                Tracking Aktivitas Surat

            </h5>

        </div>

        <div class="card-body">

            @forelse($surat->logs as $log)

                <div class="border-start border-4 border-primary ps-3 mb-4">

                    <h6 class="fw-bold">

                        {{ $log->action }}

                    </h6>

                    <p class="mb-1">

                        {{ $log->description }}

                    </p>

                    <small class="text-muted">

                        {{ $log->created_at->format('d M Y H:i') }}

                    </small>

                </div>

            @empty

                <div class="alert alert-secondary mb-0">

                    Belum ada aktivitas pada surat ini.

                </div>

            @endforelse

        </div>

    </div>

    <div class="mt-3">

        <a href="{{ route('umum.surat.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>
        @if(in_array($surat->status, ['menunggu', 'dikembalikan', 'ditolak']))
            <a href="{{ route('umum.surat.edit', $surat->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i>Perbaiki Pengajuan
            </a>
        @endif

    </div>

</div>

@endsection
