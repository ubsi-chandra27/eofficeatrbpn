@php($tujuan = $disposisiTujuan ?? $disposisi ?? null)

@if($tujuan)
    <div class="d-flex flex-wrap gap-2" aria-label="Tindak lanjut disposisi">
        @if($tujuan->status === 'Belum Dibaca')
            <form method="POST" action="{{ route('pegawai.disposisi.dibaca', $tujuan) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-primary">
                    <i class="bi bi-envelope-open me-1"></i>Tandai Dibaca
                </button>
            </form>
        @endif

        @if($tujuan->status !== 'Selesai')
            <form method="POST" action="{{ route('pegawai.disposisi.selesai', $tujuan) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-success" onclick="return confirm('Tandai disposisi ini sebagai selesai?')">
                    <i class="bi bi-check-circle me-1"></i>Selesaikan
                </button>
            </form>
        @else
            <span class="badge text-bg-success align-self-center">
                <i class="bi bi-check-circle-fill me-1"></i>Selesai
                @if($tujuan->selesai_pada)
                    {{ $tujuan->selesai_pada->translatedFormat('d M Y H:i') }}
                @endif
            </span>
        @endif

        <a href="{{ route('pegawai.disposisi.cetak', $tujuan) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Cetak
        </a>
    </div>
@endif
