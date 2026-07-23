<?php

namespace App\Http\Controllers\Umum;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $base = Surat::where('user_id', $userId)
            ->where('jenis_surat', 'masuk');
        $statistik = [
            'total' => (clone $base)->count(),
            'diajukan' => (clone $base)->whereIn('status', ['menunggu', 'diajukan'])->count(),
            'diproses' => (clone $base)->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'])->count(),
            'perbaikan' => (clone $base)->whereIn('status', ['dikembalikan', 'ditolak'])->count(),
            'selesai' => (clone $base)->whereIn('status', ['selesai', 'terkirim', 'diarsipkan'])->count(),
        ];
        $suratTerbaru = (clone $base)->latest('updated_at')->limit(5)->get();
        $aktivitas = LogAktivitas::with('surat')
            ->where(function ($query) use ($userId) {
                $query->where(function ($accountLog) use ($userId) {
                    $accountLog->where('user_id', $userId)->whereNull('surat_id');
                })->orWhereHas('surat', fn ($surat) => $surat
                    ->where('user_id', $userId)
                    ->where('jenis_surat', 'masuk'));
            })
            ->latest()
            ->limit(8)
            ->get();
        $informasiLayanan = [
            'judul_pengumuman' => Setting::getValue('public_announcement_title', 'Informasi Layanan'),
            'isi_pengumuman' => Setting::getValue('public_announcement_message', 'Pastikan data pengajuan dan dokumen pendukung sudah benar sebelum dikirim.'),
            'jam_layanan' => Setting::getValue('public_service_hours', 'Senin–Jumat, 08.00–16.00'),
            'email_bantuan' => Setting::getValue('public_help_email', ''),
            'telepon_bantuan' => Setting::getValue('public_help_phone', ''),
            'maksimal_lampiran' => (int) Setting::getValue('max_upload_mb', 5),
        ];

        return view('umum.dashboard', compact('statistik', 'suratTerbaru', 'aktivitas', 'informasiLayanan'));
    }
}
