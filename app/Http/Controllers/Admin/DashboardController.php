<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $statusGroups = [
            'diajukan' => ['menunggu', 'diajukan'],
            'diproses' => ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'],
            'perbaikan' => ['dikembalikan', 'ditolak'],
            'selesai' => ['selesai', 'terkirim', 'diarsipkan'],
        ];

        $statusSurat = Surat::where('jenis_surat', 'masuk')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $antrean = [
            'diajukan' => (int) collect($statusGroups['diajukan'])->sum(fn ($status) => $statusSurat[$status] ?? 0),
            'diverifikasi' => (int) ($statusSurat['diverifikasi'] ?? 0),
            'dikembalikan' => (int) collect($statusGroups['perbaikan'])->sum(fn ($status) => $statusSurat[$status] ?? 0),
            'ke_pimpinan' => (int) ($statusSurat['diteruskan_ke_pimpinan'] ?? 0),
        ];

        $stats = [
            'total' => Surat::count(),
            'diajukan' => (int) Surat::whereIn('status', $statusGroups['diajukan'])->count(),
            'diproses' => (int) Surat::whereIn('status', $statusGroups['diproses'])->count(),
            'perbaikan' => (int) Surat::whereIn('status', $statusGroups['perbaikan'])->count(),
            'selesai' => (int) Surat::whereIn('status', $statusGroups['selesai'])->count(),
        ];

        $disposisiStatus = DisposisiTujuan::query()
            ->whereHas('disposisi')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $indikatorDisposisi = [
            'belum_dibaca' => (int) ($disposisiStatus['Belum Dibaca'] ?? 0),
            'sudah_dibaca' => (int) ($disposisiStatus['Sudah Dibaca'] ?? 0),
            'selesai' => (int) ($disposisiStatus['Selesai'] ?? 0),
        ];

        $awal = now()->startOfMonth()->subMonths(5);
        $rekapBulanan = Surat::query()
            ->where('created_at', '>=', $awal)
            ->get(['created_at', 'jenis_surat'])
            ->groupBy(fn(Surat $surat) => $surat->created_at->format('Y-m'))
            ->map(fn($items) => $items->countBy('jenis_surat'));

        $labels = [];
        $grafikMasuk = [];
        $grafikKeluar = [];

        for ($i = 0; $i < 6; $i++) {
            $bulan = $awal->copy()->addMonths($i);
            $periode = $bulan->format('Y-m');
            $labels[] = $bulan->translatedFormat('M Y');
            $baris = $rekapBulanan->get($periode, collect());
            $grafikMasuk[] = (int) $baris->get('masuk', 0);
            $grafikKeluar[] = (int) $baris->get('keluar', 0);
        }

        $aktivitasTerbaru = LogAktivitas::with(['user:id,name', 'surat:id,nomor_surat'])
            ->latest()
            ->take(8)
            ->get();

        $suratTerbaru = Surat::with('user:id,name')
            ->latest()
            ->take(6)
            ->get();

        $dataHealth = [
            'pegawai_tanpa_akun' => Pegawai::whereNull('user_id')->count(),
            'akun_tanpa_profil' => User::where('role', 'pegawai')->whereDoesntHave('pegawai')->count(),
            'pegawai_tanpa_jabatan' => Pegawai::whereNull('jabatan_id')->count(),
            'pegawai_tanpa_unit' => Pegawai::whereNull('unit_kerja_id')->count(),
        ];

        return view('admin.dashboard', [
            'antrean' => $antrean,
            'indikatorDisposisi' => $indikatorDisposisi,
            'totalSurat' => Surat::count(),
            'totalSuratMasuk' => Surat::where('jenis_surat', 'masuk')->count(),
            'totalSuratKeluar' => Surat::where('jenis_surat', 'keluar')->count(),
            'totalDisposisi' => Disposisi::count(),
            'totalPegawai' => Pegawai::count(),
            'suratHariIni' => Surat::whereDate('created_at', Carbon::today())->count(),
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'suratTerbaru' => $suratTerbaru,
            'dataHealth' => $dataHealth,
            'chartLabels' => $labels,
            'chartMasuk' => $grafikMasuk,
            'chartKeluar' => $grafikKeluar,
        ]);
    }
}
