@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="page-header fade-up mb-4">
        <div>
            <h2><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard Administrator</h2>
            <p class="text-muted mb-0">Ringkasan antrean dan pekerjaan yang perlu ditindaklanjuti.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['Diajukan', $antrean['diajukan'], 'diajukan', 'warning', 'bi-hourglass-split'],
                ['Siap ke Pimpinan', $antrean['diverifikasi'], 'diverifikasi', 'success', 'bi-check-circle'],
                ['Perlu Perbaikan', $antrean['dikembalikan'], 'dikembalikan', 'danger', 'bi-arrow-counterclockwise'],
                ['Ke Pimpinan', $antrean['ke_pimpinan'], 'diteruskan_ke_pimpinan', 'primary', 'bi-send-check'],
            ];
        @endphp
        @foreach($cards as [$label, $value, $status, $color, $icon])
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('admin.surat.masuk.index', ['status' => $status]) }}" class="text-decoration-none">
                    <div class="card operational-card border-0 shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><small class="text-muted">{{ $label }}</small><h2 class="fw-bold text-dark mb-0">{{ $value }}</h2></div>
                            <span class="icon-box bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi {{ $icon }}"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">Surat 6 Bulan Terakhir</h5></div>
                <div class="card-body chart-container"><canvas id="chartSurat"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">Status Disposisi</h5></div>
                <div class="card-body">
                    <div class="status-row"><span><i class="bi bi-envelope-exclamation text-warning"></i> Belum Dibaca</span><b>{{ $indikatorDisposisi['belum_dibaca'] }}</b></div>
                    <div class="status-row"><span><i class="bi bi-envelope-open text-info"></i> Sudah Dibaca</span><b>{{ $indikatorDisposisi['sudah_dibaca'] }}</b></div>
                    <div class="status-row"><span><i class="bi bi-check2-circle text-success"></i> Selesai</span><b>{{ $indikatorDisposisi['selesai'] }}</b></div>
                    <hr>
                    <div class="row text-center g-2">
                        <div class="col-4"><small class="text-muted d-block">Surat</small><b>{{ $totalSurat }}</b></div>
                        <div class="col-4"><small class="text-muted d-block">Disposisi</small><b>{{ $totalDisposisi }}</b></div>
                        <div class="col-4"><small class="text-muted d-block">Pegawai</small><b>{{ $totalPegawai }}</b></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">Aktivitas Terbaru</h5></div>
                <div class="card-body pt-2">
                    @forelse($aktivitasTerbaru as $log)
                        <div class="activity-row">
                            <span class="activity-icon"><i class="bi bi-clock-history"></i></span>
                            <div class="flex-grow-1"><b>{{ $log->action }}</b><div class="small text-muted">{{ $log->description }}</div></div>
                            <small class="text-muted text-nowrap">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">Kesehatan Data</h5></div>
                <div class="card-body">
                    @foreach([
                        'Pegawai tanpa akun' => $dataHealth['pegawai_tanpa_akun'],
                        'Akun tanpa profil' => $dataHealth['akun_tanpa_profil'],
                        'Pegawai tanpa jabatan' => $dataHealth['pegawai_tanpa_jabatan'],
                        'Pegawai tanpa unit' => $dataHealth['pegawai_tanpa_unit'],
                    ] as $label => $jumlah)
                        <div class="status-row"><span>{{ $label }}</span><span class="badge {{ $jumlah ? 'bg-danger' : 'bg-success' }}">{{ $jumlah }}</span></div>
                    @endforeach
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-primary w-100 mt-3">Kelola Data Pegawai</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">Surat Terbaru</h5></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nomor</th><th>Perihal</th><th>Pembuat</th><th>Status</th><th>Tanggal</th></tr></thead>
                <tbody>
                @forelse($suratTerbaru as $surat)
                    <tr><td>{{ $surat->nomor_surat }}</td><td>{{ $surat->perihal }}</td><td>{{ $surat->user->name ?? 'Admin' }}</td><td><span class="badge bg-{{ $surat->status_badge }}">{{ $surat->status_label }}</span></td><td>{{ optional($surat->tanggal_surat)->format('d M Y') }}</td></tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada surat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.operational-card{transition:.2s}.operational-card:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(15,76,129,.12)!important}
.icon-box{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;font-size:23px}
.status-row{display:flex;justify-content:space-between;align-items:center;padding:13px 0;border-bottom:1px solid #eef2f7}.status-row:last-child{border-bottom:0}
.activity-row{display:flex;gap:12px;align-items:flex-start;padding:13px 0;border-bottom:1px solid #eef2f7}.activity-row:last-child{border-bottom:0}
.activity-icon{width:38px;height:38px;border-radius:10px;background:#e8f1fa;color:#0f4c81;display:grid;place-items:center;flex:none}
.chart-container{position:relative;min-height:320px}
@media(max-width:767.98px){.chart-container{min-height:260px}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    let attempts = 0;

    const renderChart = () => {
        const canvas = document.getElementById('chartSurat');
        if (!canvas || canvas.dataset.rendered === 'true') return;

        if (typeof window.Chart === 'undefined') {
            if (attempts++ < 50) window.setTimeout(renderChart, 100);
            return;
        }

        canvas.dataset.rendered = 'true';
        new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {label: 'Surat Masuk', data: @json($chartMasuk), backgroundColor: '#0F4C81', borderRadius: 6},
                    {label: 'Surat Keluar', data: @json($chartKeluar), backgroundColor: '#4BA3E3', borderRadius: 6}
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {legend: {position: 'bottom'}},
                scales: {y: {beginAtZero: true, ticks: {precision: 0, stepSize: 1}}}
            }
        });
    };

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', renderChart)
        : renderChart();
})();
</script>
@endpush
