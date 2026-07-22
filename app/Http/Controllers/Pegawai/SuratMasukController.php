<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    /**
     * Daftar Surat Masuk
     */
    public function index(Request $request)
    {
        $query = Surat::where('jenis_surat', 'masuk')
            ->where('user_id', Auth::id());
        $base = clone $query;
        $stats = [
            'total' => (clone $base)->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'diajukan' => (clone $base)->whereIn('status', ['diajukan', 'diverifikasi', 'diteruskan_ke_pimpinan'])->count(),
            'perbaikan' => (clone $base)->where('status', 'dikembalikan')->count(),
        ];

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_surat', 'like', '%' . $request->keyword . '%')
                  ->orWhere('asal_surat', 'like', '%' . $request->keyword . '%')
                  ->orWhere('perihal', 'like', '%' . $request->keyword . '%');
            });
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suratMasuk = $query->latest()->paginate(10);

        return view('pegawai.surat.masuk.index', compact('suratMasuk', 'stats'));
    }

    /**
     * Form tambah surat
     */
    public function create()
    {
        $jabatan = Jabatan::orderBy('nama')->get();

        return view('pegawai.surat.masuk.create', compact('jabatan'));
    }

    /**
     * Simpan surat
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat'   => 'required|unique:surats',
            'tanggal_surat' => 'required|date',
            'perihal'       => 'required',
            'asal_surat'    => 'required',
            'tujuan_surat'  => 'required',
            'jabatan_pimpinan_id' => 'nullable|exists:jabatan,id',
            'nama_pimpinan' => 'nullable|string|max:255',
            'deskripsi'     => 'nullable',
            'file_path'     => 'nullable|file|max:5120',
        ]);

        $file = null;

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path')->store('surat', 'public');
        }

        Surat::create([
            'user_id'        => Auth::id(),
            'jenis_surat'    => 'masuk',
            'nomor_surat'    => $request->nomor_surat,
            'tanggal_surat'  => $request->tanggal_surat,
            'perihal'        => $request->perihal,
            'asal_surat'     => $request->asal_surat,
            'tujuan_surat'   => $request->tujuan_surat,
            'deskripsi'      => $request->deskripsi,
            'file_path'      => $file,
            'jabatan_pimpinan_id' => $request->jabatan_pimpinan_id,
            'nama_pimpinan'  => $request->nama_pimpinan,
            'status'         => 'draft',
        ]);

        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('success', 'Surat berhasil disimpan.');
    }

    /**
     * Detail surat
     */
    public function show($id)
    {
        $surat = $this->suratMilikPegawai($id);

        return view('pegawai.surat.masuk.show', compact('surat'));
    }

    /**
     * Form edit
     */
    public function edit($id)
{
    $surat = Surat::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if (!in_array($surat->status, ['draft', 'dikembalikan'], true)) {
        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('error', 'Surat yang sudah diproses tidak dapat diedit.');
    }

    return view('pegawai.surat.masuk.edit', compact('surat'));
}
    /**
     * Update surat
     */
    public function update(Request $request, $id)
{
    $surat = Surat::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if (!in_array($surat->status, ['draft', 'dikembalikan'], true)) {
        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('error', 'Surat sudah tidak dapat diperbarui.');
    }

    $request->validate([
        'nomor_surat'   => 'required|unique:surats,nomor_surat,' . $surat->id,
        'tanggal_surat' => 'required|date',
        'asal_surat'    => 'required',
        'tujuan_surat'  => 'required',
        'perihal'       => 'required',
        'deskripsi'     => 'nullable',
        'file_path'     => 'nullable|file|max:5120',
    ]);

    if ($request->hasFile('file_path')) {
        $file = $request->file('file_path')->store('surat', 'public');
        $surat->file_path = $file;
    }

    $surat->nomor_surat   = $request->nomor_surat;
    $surat->tanggal_surat = $request->tanggal_surat;
    $surat->asal_surat    = $request->asal_surat;
    $surat->tujuan_surat  = $request->tujuan_surat;
    $surat->perihal       = $request->perihal;
    $surat->deskripsi     = $request->deskripsi;

    $surat->save();

    return redirect()
        ->route('pegawai.surat-masuk.index')
        ->with('success', 'Surat berhasil diperbarui.');
}

    /**
     * Hapus surat
     */
    public function destroy($id)
    {
        $surat = $this->suratMilikPegawai($id);

        if (!in_array($surat->status, ['draft', 'dikembalikan'], true)) {
            return back()->with('error', 'Surat tidak dapat dihapus.');
        }

        $surat->delete();

        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('success', 'Surat berhasil dihapus.');
    }

    /**
     * Kirim surat ke antrean verifikasi admin
     */
    public function kirim($id)
    {
        $surat = $this->suratMilikPegawai($id);

        if (!in_array($surat->status, ['draft', 'dikembalikan'], true)) {
            return back()->with('error', 'Surat sudah diproses.');
        }

        $surat->update([
            'status' => 'diajukan',
        ]);

        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('success', 'Surat berhasil dikirim dan masuk antrean verifikasi.');
    }

    private function suratMilikPegawai(int $id): Surat
    {
        return Surat::where('user_id', Auth::id())
            ->where('jenis_surat', 'masuk')
            ->findOrFail($id);
    }
}
