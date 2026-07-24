<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class SuratKeluarController extends Controller
{


    public function index(Request $request)
    {
        $query = Surat::with('jabatanPimpinan')
            ->where('user_id', Auth::id())
            ->where('jenis_surat', 'keluar');
        $base = clone $query;
        $statusGroups = [
            'diajukan' => ['menunggu', 'diajukan'],
            'diproses' => ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'],
            'perbaikan' => ['dikembalikan', 'ditolak'],
            'selesai' => ['terkirim', 'selesai', 'diarsipkan'],
        ];
        $stats = [
            'total' => (clone $base)->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'diajukan' => (clone $base)->whereIn('status', array_merge($statusGroups['diajukan'], $statusGroups['diproses']))->count(),
            'menunggu' => (clone $base)->whereIn('status', $statusGroups['diajukan'])->count(),
            'diproses' => (clone $base)->whereIn('status', $statusGroups['diproses'])->count(),
            'perbaikan' => (clone $base)->whereIn('status', $statusGroups['perbaikan'])->count(),
            'selesai' => (clone $base)->whereIn('status', $statusGroups['selesai'])->count(),
        ];

        if ($request->filled('keyword')) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'nomor_surat',
                    'like',
                    '%' . $request->keyword . '%'
                )
                ->orWhere(
                    'perihal',
                    'like',
                    '%' . $request->keyword . '%'
                )
                ->orWhere(
                    'tujuan_surat',
                    'like',
                    '%' . $request->keyword . '%'
                )
                ->orWhere(
                    'kode_surat',
                    'like',
                    '%' . $request->keyword . '%'
                )
                ->orWhere(
                    'nama_pimpinan',
                    'like',
                    '%' . $request->keyword . '%'
                );

            });

        }

        $allowedStatuses = [
            'draft', 'diajukan', 'menunggu', 'diverifikasi', 'dikembalikan',
            'ditolak', 'diproses', 'diteruskan_ke_pimpinan', 'terkirim',
            'selesai', 'diarsipkan',
        ];
        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if (isset($statusGroups[$status])) {
                $query->whereIn('status', $statusGroups[$status]);
            } elseif (in_array($status, $allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        $surat = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'pegawai.surat.keluar.index',
            compact('surat', 'stats')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH
    |--------------------------------------------------------------------------
    */

    public function create()
{
    $pimpinans = Pegawai::with('jabatan')
        ->whereNotNull('jabatan_id')
        ->orderBy('nama')
        ->get();

    return view(
        'pegawai.surat.keluar.create',
        compact('pimpinans')
    );
}

    /*
    |--------------------------------------------------------------------------
    | SIMPAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $data = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surats,nomor_surat',
            'tanggal_surat' => 'required|date',
            'kode_surat' => 'nullable|string|max:50',
            'perihal' => 'required|string|max:500',
            'tujuan_surat' => 'required|string|max:255',
            'pimpinan_pegawai_id' => 'required|exists:pegawai,id',
            'deskripsi' => 'nullable|string|max:2000',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
            'status' => 'required|in:draft,diajukan',

        ], [
            'pimpinan_pegawai_id.required' => 'Pimpinan atau penandatangan wajib dipilih.',
            'pimpinan_pegawai_id.exists' => 'Data pimpinan yang dipilih tidak valid.',
            'tujuan_surat.required' => 'Tujuan instansi wajib diisi.',
        ]);



        $pimpinan = Pegawai::with('jabatan')->findOrFail($request->pimpinan_pegawai_id);

        if (!$pimpinan->jabatan) {
            return back()->withInput()->withErrors([
                'pimpinan_pegawai_id' => 'Pegawai yang dipilih belum memiliki jabatan.',
            ]);
        }

        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-keluar', 'local')
            : null;
        unset($data['pimpinan_pegawai_id']);
        $data = array_merge($data, [
            'user_id' => Auth::id(), 'jenis_surat' => 'keluar',
            'jabatan_pimpinan_id' => $pimpinan->jabatan_id,
            'nama_pimpinan' => $pimpinan->nama, 'file_path' => $newPath,
        ]);
        try {
            DB::transaction(function () use ($data) {
                $surat = Surat::create($data);
                LogAktivitas::create([
                    'user_id' => Auth::id(), 'surat_id' => $surat->id,
                    'action' => $surat->status === 'diajukan' ? 'Mengirim Surat Keluar' : 'Membuat Surat Keluar',
                    'description' => 'Surat keluar '.$surat->nomor_surat.($surat->status === 'diajukan' ? ' dikirim ke Admin.' : ' disimpan sebagai draft.'),
                ]);
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }


        return redirect()
            ->route('pegawai.surat-keluar.index')
            ->with(
                'success',
                'Surat keluar berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $surat = $this->suratKeluarMilikPegawai($id)
            ->load(['jabatanPimpinan', 'logs.user']);

        return view(
            'pegawai.surat.keluar.show',
            compact('surat')
        );
    }

    public function cetak(int $id)
    {
        return view('pegawai.surat.keluar.cetak', [
            'surat' => $this->suratKeluarMilikPegawai($id)->load('jabatanPimpinan'),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FORM EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $surat = $this->suratKeluarMilikPegawai($id);

        if (!$this->dapatDiubah($surat)) {

            return back()
                ->with(
                    'error',
                    'Surat yang sudah diproses tidak dapat diedit.'
                );

        }

        $pimpinans = Pegawai::with('jabatan')
            ->whereNotNull('jabatan_id')
            ->orderBy('nama')
            ->get();

        $selectedPimpinanId = Pegawai::where('nama', $surat->nama_pimpinan)
            ->where('jabatan_id', $surat->jabatan_pimpinan_id)
            ->value('id');

        return view(
            'pegawai.surat.keluar.edit',
            compact(
                'surat',
                'pimpinans',
                'selectedPimpinanId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    )
    {

        $surat = $this->suratKeluarMilikPegawai($id);

        if (!$this->dapatDiubah($surat)) {

            return back()
                ->with(
                    'error',
                    'Surat yang sudah diproses tidak dapat diedit.'
                );

        }

        $data = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surats,nomor_surat,' . $surat->id,
            'tanggal_surat' => 'required|date',
            'kode_surat' => 'nullable|string|max:50',
            'perihal' => 'required|string|max:500',
            'tujuan_surat' => 'required|string|max:255',
            'pimpinan_pegawai_id' => 'required|exists:pegawai,id',
            'deskripsi' => 'nullable|string|max:2000',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),

        ], [
            'pimpinan_pegawai_id.required' => 'Pimpinan atau penandatangan wajib dipilih.',
            'pimpinan_pegawai_id.exists' => 'Data pimpinan yang dipilih tidak valid.',
            'tujuan_surat.required' => 'Tujuan instansi wajib diisi.',
        ]);


        $pimpinan = Pegawai::with('jabatan')->findOrFail($request->pimpinan_pegawai_id);

        if (!$pimpinan->jabatan) {
            return back()->withInput()->withErrors([
                'pimpinan_pegawai_id' => 'Pegawai yang dipilih belum memiliki jabatan.',
            ]);
        }

        $oldPath = $surat->file_path;
        $oldDisk = $oldPath ? $surat->attachmentDisk() : null;
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-keluar', 'local')
            : null;
        unset($data['pimpinan_pegawai_id']);
        $data['jabatan_pimpinan_id'] = $pimpinan->jabatan_id;
        $data['nama_pimpinan'] = $pimpinan->nama;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }
        try {
            DB::transaction(function () use ($surat, $data) {
                $surat->update($data);
                LogAktivitas::create([
                    'user_id' => Auth::id(), 'surat_id' => $surat->id,
                    'action' => 'Mengubah Surat Keluar',
                    'description' => 'Mengubah surat '.$surat->nomor_surat,
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
            ->route('pegawai.surat-keluar.index')
            ->with(
                'success',
                'Surat berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $surat = $this->suratKeluarMilikPegawai($id);

        if (!$this->dapatDiubah($surat)) {

            return back()
                ->with(
                    'error',
                    'Surat yang sudah diproses tidak dapat dihapus.'
                );

        }

        DB::transaction(function () use ($surat) {
            LogAktivitas::create([
                'user_id' => Auth::id(), 'surat_id' => $surat->id,
                'action' => 'Menghapus Surat Keluar',
                'description' => 'Surat '.$surat->nomor_surat.' dipindahkan ke sampah.',
            ]);
            $surat->delete();
        });

        return redirect()
            ->route('pegawai.surat-keluar.index')
            ->with(
                'success',
                'Surat berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | KIRIM SURAT
    |--------------------------------------------------------------------------
    */

    public function kirim($id)
    {

        $surat = $this->suratKeluarMilikPegawai($id);

        if (!$this->dapatDiubah($surat)) {

            return back()
                ->with(
                    'error',
                    'Surat sudah diproses.'
                );

        }

        DB::transaction(function () use ($surat) {
            $surat->update(['status' => 'diajukan', 'catatan_admin' => null]);
            LogAktivitas::create([
                'user_id' => Auth::id(), 'surat_id' => $surat->id,
                'action' => 'Mengirim Surat Keluar',
                'description' => 'Mengirim surat '.$surat->nomor_surat.' ke Admin.',
            ]);
        });


        return redirect()
            ->route('pegawai.surat-keluar.index')
            ->with(
                'success',
                'Surat berhasil dikirim.'
            );
    }

    private function suratKeluarMilikPegawai(int $id): Surat
    {
        return Surat::where('user_id', Auth::id())
            ->where('jenis_surat', 'keluar')
            ->findOrFail($id);
    }

    /** Mendukung status baru dan status lama sebelum normalisasi workflow. */
    private function dapatDiubah(Surat $surat): bool
    {
        return in_array($surat->status, ['draft', 'dikembalikan', 'Menunggu'], true);
    }

}
