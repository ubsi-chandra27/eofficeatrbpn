<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Setting;
use App\Models\Surat;
use App\Services\PegawaiStarterDataService;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    /**
     * Daftar Surat Masuk
     */
    public function index(Request $request)
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();
        $starterData = app(PegawaiStarterDataService::class);
        if ($pegawai && $starterData->needsStarterData($pegawai)) {
            $starterData->ensureForPegawai($pegawai);
        }

        $query = Surat::where('jenis_surat', 'masuk')
            ->where('user_id', Auth::id());
        $base = clone $query;
        $stats = [
            'total' => (clone $base)->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'menunggu_verifikasi' => (clone $base)->whereIn('status', ['diajukan', 'menunggu'])->count(),
            'disetujui' => (clone $base)->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan', 'selesai'])->count(),
            'perbaikan' => (clone $base)->whereIn('status', ['dikembalikan', 'ditolak'])->count(),
        ];

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_surat', 'like', '%' . $request->keyword . '%')
                  ->orWhere('nomor_agenda', 'like', '%' . $request->keyword . '%')
                  ->orWhere('asal_surat', 'like', '%' . $request->keyword . '%')
                  ->orWhere('tujuan_surat', 'like', '%' . $request->keyword . '%')
                  ->orWhere('perihal', 'like', '%' . $request->keyword . '%')
                  ->orWhere('catatan_admin', 'like', '%' . $request->keyword . '%');
            });
        }


        $allowedStatuses = ['draft', 'diajukan', 'menunggu', 'diverifikasi', 'dikembalikan', 'ditolak', 'diproses', 'diteruskan_ke_pimpinan', 'selesai', 'diarsipkan'];
        if ($request->filled('status') && in_array($request->status, $allowedStatuses, true)) {
            $query->where('status', $request->status);
        }

        $suratMasuk = $query
            ->orderByDesc('tanggal_surat')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pegawai.surat.masuk.index', compact('suratMasuk', 'stats'));
    }

    /**
     * Form tambah surat
     */
    public function create()
    {
        return view('pegawai.surat.masuk.create');
    }

    /**
     * Simpan surat
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surats,nomor_surat',
            'nomor_agenda' => 'nullable|string|max:100',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:500',
            'asal_surat' => 'required|string|max:255',
            'tujuan_surat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
            'submit_action' => 'required|in:draft,submit',
        ]);
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-masuk', 'local')
            : null;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }
        $langsungDikirim = $data['submit_action'] === 'submit';
        unset($data['submit_action']);
        $data['user_id'] = Auth::id();
        $data['jenis_surat'] = 'masuk';
        $data['status'] = $langsungDikirim ? 'diajukan' : 'draft';

        try {
            $surat = DB::transaction(function () use ($data, $langsungDikirim) {
                $surat = Surat::create($data);
                LogAktivitas::create([
                    'user_id' => Auth::id(),
                    'surat_id' => $surat->id,
                    'action' => $langsungDikirim ? 'Dikirim untuk Verifikasi' : 'Draft Surat Masuk',
                    'description' => $langsungDikirim
                        ? 'Surat '.$surat->nomor_surat.' dikirim ke Admin untuk diverifikasi.'
                        : 'Surat '.$surat->nomor_surat.' disimpan sebagai draft.',
                ]);

                return $surat;
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        if ($langsungDikirim) {
            app(SystemNotificationService::class)->notifyAdmins(
                'Surat masuk pegawai baru',
                'Surat '.$surat->nomor_surat.' dari '.Auth::user()->name.' menunggu verifikasi.',
                route('admin.surat.masuk.show', $surat->id),
                'warning',
                'bi-envelope-plus-fill'
            );
        }

        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('success', $langsungDikirim
                ? 'Surat berhasil dikirim ke Admin dan menunggu verifikasi.'
                : 'Surat berhasil disimpan sebagai draft.');
    }

    /**
     * Detail surat
     */
    public function show($id)
    {
        $surat = $this->suratMilikPegawai($id)->load(['logs.user']);

        return view('pegawai.surat.masuk.show', compact('surat'));
    }

    public function cetak(int $id)
    {
        return view('pegawai.surat.masuk.cetak', [
            'surat' => $this->suratMilikPegawai($id),
        ]);
    }

    /**
     * Form edit
     */
    public function edit($id)
{
    $surat = $this->suratMilikPegawai($id);

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
    $surat = $this->suratMilikPegawai($id);

    if (!in_array($surat->status, ['draft', 'dikembalikan'], true)) {
        return redirect()
            ->route('pegawai.surat-masuk.index')
            ->with('error', 'Surat sudah tidak dapat diperbarui.');
    }

    $data = $request->validate([
        'nomor_surat'   => 'required|string|max:100|unique:surats,nomor_surat,' . $surat->id,
        'nomor_agenda'  => 'nullable|string|max:100',
        'tanggal_surat' => 'required|date',
        'asal_surat'    => 'required|string|max:255',
        'tujuan_surat'  => 'required|string|max:255',
        'perihal'       => 'required|string|max:500',
        'deskripsi'     => 'nullable|string|max:2000',
        'file_path'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
    ]);

    $oldPath = $surat->file_path;
    $oldDisk = $oldPath ? $surat->attachmentDisk() : null;
    $newPath = $request->hasFile('file_path')
        ? $request->file('file_path')->store('surat-masuk', 'local')
        : null;
    if ($newPath) {
        $data['file_path'] = $newPath;
    }

    try {
        DB::transaction(function () use ($surat, $data) {
            $surat->update($data);
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'surat_id' => $surat->id,
                'action' => 'Perbarui Surat Masuk',
                'description' => 'Surat '.$surat->nomor_surat.' diperbarui oleh pegawai.',
            ]);
        });
    } catch (\Throwable $exception) {
        if ($newPath) {
            Storage::disk('local')->delete($newPath);
        }
        throw $exception;
    }
    if ($newPath && $oldPath) {
        $oldDisk?->delete($oldPath);
    }

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

        DB::transaction(function () use ($surat) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'surat_id' => $surat->id,
                'action' => 'Hapus Surat Masuk',
                'description' => 'Surat '.$surat->nomor_surat.' dipindahkan ke sampah.',
            ]);

            app(\App\Services\SystemNotificationService::class)->notifyAdmins(
                'Surat Masuk Dihapus',
                'Surat masuk ' . $surat->nomor_surat . ' dari ' . $surat->user->name . ' telah dihapus.',
                route('admin.surat.masuk.index'),
                'warning',
                'bi-trash'
            );

            $surat->delete();
        });

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
            'catatan_admin' => null,
        ]);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'surat_id' => $surat->id,
            'action' => 'Dikirim untuk Verifikasi',
            'description' => 'Surat '.$surat->nomor_surat.' dikirim ke Admin untuk diverifikasi.',
        ]);

        app(SystemNotificationService::class)->notifyAdmins(
            'Surat masuk pegawai dikirim',
            'Surat '.$surat->nomor_surat.' dari '.Auth::user()->name.' menunggu verifikasi.',
            route('admin.surat.masuk.show', $surat->id),
            'warning',
            'bi-send-fill'
        );

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
