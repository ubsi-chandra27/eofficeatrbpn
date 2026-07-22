<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiController extends Controller
{
    /**
     * Daftar disposisi pegawai
     */
    public function index(Request $request)
    {
        $pegawai = $this->pegawaiLogin();
        $query = DisposisiTujuan::with(['disposisi.surat', 'disposisi.pengirim'])
            ->where('pegawai_id', $pegawai->id);
        $base = DisposisiTujuan::where('pegawai_id', $pegawai->id);
        $stats = [
            'total' => (clone $base)->count(),
            'belum' => (clone $base)->where('status', 'Belum Dibaca')->count(),
            'dibaca' => (clone $base)->where('status', 'Sudah Dibaca')->count(),
            'selesai' => (clone $base)->where('status', 'Selesai')->count(),
        ];

        // Pencarian
        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            $query->whereHas('disposisi.surat', function ($q) use ($keyword) {

                $q->where('nomor_surat', 'like', "%{$keyword}%")
                  ->orWhere('perihal', 'like', "%{$keyword}%");

            });
        }

        // Filter Status
        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }

        $disposisi = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'pegawai.disposisi.index',
            compact('disposisi', 'stats')
        );
    }

    /**
     * Detail disposisi
     */
    public function show(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        if ($disposisi->isBelumDibaca()) {
            $disposisi->update([
                'status' => 'Sudah Dibaca',
                'dibaca_pada' => now(),
            ]);
        }

        return view(
            'pegawai.disposisi.show',
            compact('disposisi')
        );
    }

    /**
     * Cetak disposisi
     */
    public function cetak(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        return view(
            'pegawai.disposisi.cetak',
            compact('disposisi')
        );
    }

    /** Tandai disposisi milik pegawai sebagai telah dibaca. */
    public function dibaca(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        if ($disposisi->isBelumDibaca()) {
            $disposisi->update(['status' => 'Sudah Dibaca', 'dibaca_pada' => now()]);
        }

        return back()->with('success', 'Disposisi ditandai sudah dibaca.');
    }

    /** Selesaikan disposisi milik pegawai. */
    public function selesai(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        $disposisi->update([
            'status' => 'Selesai',
            'dibaca_pada' => $disposisi->dibaca_pada ?? now(),
            'selesai_pada' => now(),
        ]);

        return back()->with('success', 'Disposisi ditandai selesai.');
    }

    private function disposisiMilikPegawai(int $id): DisposisiTujuan
    {
        return DisposisiTujuan::with(['disposisi.surat', 'disposisi.pengirim'])
            ->where('pegawai_id', $this->pegawaiLogin()->id)
            ->findOrFail($id);
    }

    private function pegawaiLogin(): Pegawai
    {
        return Pegawai::where('user_id', Auth::id())->firstOrFail();
    }
}
