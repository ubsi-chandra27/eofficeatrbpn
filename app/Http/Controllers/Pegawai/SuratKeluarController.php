<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class SuratKeluarController extends Controller
{


    public function index(Request $request)
    {
        $query = Surat::where('user_id', Auth::id())
            ->where('jenis_surat', 'keluar');
        $base = clone $query;
        $stats = [
            'total' => (clone $base)->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'diajukan' => (clone $base)->whereIn('status', ['diajukan', 'diverifikasi'])->count(),
            'selesai' => (clone $base)->whereIn('status', ['terkirim', 'selesai', 'diarsipkan'])->count(),
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
                );

            });

        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );

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

        $request->validate([

            'nomor_surat'          => 'required|unique:surats,nomor_surat',

            'tanggal_surat'        => 'required|date',

            'perihal'              => 'required',

            'tujuan_surat'         => 'required',

            'pimpinan_pegawai_id'  => 'required|exists:pegawai,id',

            'deskripsi'            => 'nullable',

            'file_path'            => 'nullable|mimes:pdf,doc,docx|max:5120',

            'status'               => 'nullable|in:draft,diajukan',

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

        $file = null;

        if ($request->hasFile('file_path')) {

            $file = $request
                ->file('file_path')
                ->store(
                    'surat-keluar',
                    'public'
                );

        }



        $surat = Surat::create([

            'user_id' => Auth::id(),

            'jenis_surat' => 'keluar',

            'nomor_surat' => $request->nomor_surat,

            'tanggal_surat' => $request->tanggal_surat,

            'perihal' => $request->perihal,

            'tujuan_surat' => $request->tujuan_surat,

            'jabatan_pimpinan_id'
                => $pimpinan->jabatan_id,

            'nama_pimpinan'
                => $pimpinan->nama,

            'deskripsi'
                => $request->deskripsi,

            'file_path'
                => $file,

            'status'
                => $request->input('status', 'draft'),

        ]);


        LogAktivitas::create([

            'user_id' => Auth::id(),

            'surat_id' => $surat->id,

            'action' => 'Membuat Surat Keluar',

            'description'
                => 'Membuat Surat Keluar '
                . $surat->nomor_surat,

        ]);


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
            ->load('jabatanPimpinan');

        return view(
            'pegawai.surat.keluar.show',
            compact('surat')
        );
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

        $request->validate([

            'nomor_surat'
                => 'required|unique:surats,nomor_surat,' . $surat->id,

            'tanggal_surat'
                => 'required|date',

            'perihal'
                => 'required',

            'tujuan_surat'
                => 'required',

            'pimpinan_pegawai_id'
                => 'required|exists:pegawai,id',

            'deskripsi'
                => 'nullable',

            'file_path'
                => 'nullable|mimes:pdf,doc,docx|max:5120',

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

        if ($request->hasFile('file_path')) {

            if ($surat->file_path) {

                Storage::disk('public')
                    ->delete(
                        $surat->file_path
                    );

            }

            $surat->file_path =
                $request
                    ->file('file_path')
                    ->store(
                        'surat-keluar',
                        'public'
                    );
        }


        $surat->update([

            'nomor_surat'
                => $request->nomor_surat,

            'tanggal_surat'
                => $request->tanggal_surat,

            'perihal'
                => $request->perihal,

            'tujuan_surat'
                => $request->tujuan_surat,

            'jabatan_pimpinan_id'
                => $pimpinan->jabatan_id,

            'nama_pimpinan'
                => $pimpinan->nama,

            'deskripsi'
                => $request->deskripsi,

        ]);


        LogAktivitas::create([

            'user_id' => Auth::id(),

            'surat_id' => $surat->id,

            'action' => 'Mengubah Surat Keluar',

            'description'
                => 'Mengubah Surat '
                . $surat->nomor_surat,

        ]);


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

        if ($surat->file_path) {

            Storage::disk('public')
                ->delete(
                    $surat->file_path
                );

        }

        LogAktivitas::create([

            'user_id' => Auth::id(),

            'surat_id' => $surat->id,

            'action' => 'Menghapus Surat Keluar',

            'description'
                => 'Menghapus Surat '
                . $surat->nomor_surat,

        ]);

        $surat->delete();

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

        $surat->update([

            'status' => 'diajukan'

        ]);


        LogAktivitas::create([

            'user_id' => Auth::id(),

            'surat_id' => $surat->id,

            'action' => 'Mengirim Surat Keluar',

            'description'
                => 'Mengirim Surat '
                . $surat->nomor_surat,

        ]);


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
