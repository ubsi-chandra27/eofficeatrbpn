<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminDisposisiController extends Controller
{
    /**
     * ==========================================================
     * DAFTAR DISPOSISI
     * ==========================================================
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $disposisi = Disposisi::with([
                'surat',
                'pengirim',
                'tujuans.pegawai'
            ])
            ->when($keyword, function ($query) use ($keyword) {

                $query->whereHas('surat', function ($q) use ($keyword) {

                    $q->where('nomor_surat', 'like', "%{$keyword}%")
                      ->orWhere('judul_surat', 'like', "%{$keyword}%")
                      ->orWhere('perihal', 'like', "%{$keyword}%");

                });

            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statistik = DisposisiTujuan::query()
            ->selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view(
            'admin.disposisi.index',
            compact('disposisi', 'statistik')
        );
    }

    /**
     * ==========================================================
     * FORM TAMBAH DISPOSISI
     * ==========================================================
     */
    public function create()
    {
        $surat = Surat::where('jenis_surat', 'masuk')
                    ->whereIn('status', ['diverifikasi', 'diteruskan_ke_pimpinan'])
                    ->orderByDesc('tanggal_surat')
                    ->get();

        $pegawai = Pegawai::with(['jabatan', 'unitKerja'])
                    ->whereNotNull('user_id')
                    ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
                    ->orderBy('nama')
                    ->get();

        return view(
            'admin.disposisi.create',
            compact(
                'surat',
                'pegawai'
            )
        ); 
    }
        /**
     * ==========================================================
     * SIMPAN DISPOSISI BARU
     * ==========================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_id'            => 'required|exists:surats,id',
            'pegawai_id'          => 'required|array|min:1',
            'pegawai_id.*'        => ['distinct', Rule::exists('pegawai', 'id')->whereNotNull('user_id')],
            'catatan'             => 'required|string|max:2000',
            'prioritas'           => 'required|in:Rendah,Sedang,Tinggi',
            'tanggal_disposisi'   => 'required|date',
        ],[
            'surat_id.required'          => 'Surat wajib dipilih.',
            'pegawai_id.required'        => 'Minimal satu pegawai dipilih.',
            'pegawai_id.array'           => 'Format pegawai tidak valid.',
            'pegawai_id.*.distinct'      => 'Pegawai tujuan tidak boleh dipilih lebih dari sekali.',
            'pegawai_id.*.exists'        => 'Pegawai tujuan tidak valid atau belum memiliki akun.',
            'catatan.required'           => 'Catatan disposisi wajib diisi.',
            'catatan.max'                => 'Catatan disposisi maksimal 2.000 karakter.',
            'tanggal_disposisi.required' => 'Tanggal disposisi wajib diisi.',
        ]);

        // Hanya surat yang sudah disetujui yang boleh didisposisikan
        $suratDisposisi = Surat::findOrFail($request->surat_id);

        if (!in_array($suratDisposisi->status, ['diverifikasi', 'diteruskan_ke_pimpinan'], true)) {
            return back()
                ->withInput()
                ->with('error', 'Hanya surat yang telah diverifikasi atau diteruskan ke pimpinan yang dapat didisposisikan.');
        }

        DB::beginTransaction();

        try {

            // Simpan disposisi
            $disposisi = Disposisi::create([

                'surat_id'           => $request->surat_id,
                'pengirim_id'        => Auth::id(),
                'catatan'            => $request->catatan,
                'prioritas'          => $request->prioritas,
                'tanggal_disposisi'  => $request->tanggal_disposisi,

            ]);

            // Simpan tujuan disposisi
            foreach ($request->pegawai_id as $pegawai) {

                DisposisiTujuan::create([

                    'disposisi_id' => $disposisi->id,
                    'pegawai_id'   => $pegawai,
                    'status'       => 'Belum Dibaca',

                ]);
            }

            // Update status surat menjadi diproses
            $disposisi->surat()->update([

                'status' => 'diproses'

            ]);

            DB::commit();

            return redirect()
                    ->route('admin.disposisi.index')
                    ->with(
                        'success',
                        'Disposisi berhasil dikirim kepada pegawai.'
                    );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                    ->withInput()
                    ->with('error', 'Disposisi gagal disimpan. Silakan periksa data lalu coba kembali.');

        }
        
    }
            /**
     * ==========================================================
     * DETAIL DISPOSISI
     * ==========================================================
     */
    public function show($id)
    {
        $disposisi = Disposisi::with([
            'surat',
            'pengirim',
            'tujuans.pegawai'
        ])->findOrFail($id);

        return view(
            'admin.disposisi.show',
            compact('disposisi')
        );
    }

    /**
     * ==========================================================
     * FORM EDIT DISPOSISI
     * ==========================================================
     */
    public function edit($id)
    {
        $disposisi = Disposisi::with([
            'tujuans'
        ])->findOrFail($id);

        $surat = Surat::where('jenis_surat', 'masuk')
                    ->where(function ($query) use ($disposisi) {
                        $query->whereIn('status', ['diverifikasi', 'diteruskan_ke_pimpinan'])
                            ->orWhere('id', $disposisi->surat_id);
                    })
                    ->orderByDesc('tanggal_surat')
                    ->get();

        $pegawai = Pegawai::with(['jabatan', 'unitKerja'])
                    ->whereNotNull('user_id')
                    ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
                    ->orderBy('nama')
                    ->get();

        // Pegawai yang sudah menjadi tujuan disposisi
        $pegawaiDipilih = $disposisi->tujuans
            ->pluck('pegawai_id')
            ->toArray();

        return view(
            'admin.disposisi.edit',
            compact(
                'disposisi',
                'surat',
                'pegawai',
                'pegawaiDipilih'
            )
        );
    }


    /**
     * ==========================================================
     * UPDATE DISPOSISI
     * ==========================================================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'surat_id'            => 'required|exists:surats,id',
            'pegawai_id'          => 'required|array|min:1',
            'pegawai_id.*'        => ['distinct', Rule::exists('pegawai', 'id')->whereNotNull('user_id')],
            'catatan'             => 'required|string|max:2000',
            'prioritas'           => 'required|in:Rendah,Sedang,Tinggi',
            'tanggal_disposisi'   => 'required|date',
        ], [
            'surat_id.required' => 'Surat wajib dipilih.',
            'pegawai_id.required' => 'Minimal satu pegawai harus dipilih.',
            'pegawai_id.*.distinct' => 'Pegawai tujuan tidak boleh dipilih lebih dari sekali.',
            'pegawai_id.*.exists' => 'Pegawai tujuan tidak valid atau belum memiliki akun.',
            'catatan.required' => 'Catatan disposisi wajib diisi.',
            'catatan.max' => 'Catatan disposisi maksimal 2.000 karakter.',
            'prioritas.required' => 'Prioritas wajib dipilih.',
            'tanggal_disposisi.required' => 'Tanggal disposisi wajib diisi.',
        ]);

        DB::beginTransaction();

        try {

            $disposisi = Disposisi::findOrFail($id);

            if ($disposisi->tujuans()->where('status', '!=', 'Belum Dibaca')->exists()) {
                DB::rollBack();
                return back()->with('error', 'Disposisi yang sudah dibaca atau selesai tidak dapat diubah.');
            }

            $suratDipilih = Surat::where('jenis_surat', 'masuk')->findOrFail($request->surat_id);
            $bolehDipilih = in_array($suratDipilih->status, ['diverifikasi', 'diteruskan_ke_pimpinan'], true)
                || $suratDipilih->id === $disposisi->surat_id;

            if (!$bolehDipilih) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Surat yang dipilih tidak dapat didisposisikan.');
            }

            $disposisi->update([
                'surat_id'          => $request->surat_id,
                'catatan'           => $request->catatan,
                'prioritas'         => $request->prioritas,
                'tanggal_disposisi' => $request->tanggal_disposisi,
            ]);

            // Hapus hanya penerima yang dibatalkan; status penerima lama tetap terjaga.
            DisposisiTujuan::where('disposisi_id', $disposisi->id)
                ->whereNotIn('pegawai_id', $request->pegawai_id)
                ->delete();

            foreach ($request->pegawai_id as $pegawai) {
                DisposisiTujuan::firstOrCreate([
                    'disposisi_id' => $disposisi->id,
                    'pegawai_id'   => $pegawai,
                ], [
                    'status'       => 'Belum Dibaca',
                ]);

            }

            DB::commit();

            return redirect()
                ->route('admin.disposisi.index')
                ->with('success', 'Disposisi berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Disposisi gagal diperbarui. Silakan coba kembali.');

        }
    }

    /**
     * ==========================================================
     * HAPUS DISPOSISI
     * ==========================================================
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $disposisi = Disposisi::findOrFail($id);

            if ($disposisi->tujuans()->where('status', '!=', 'Belum Dibaca')->exists()) {
                DB::rollBack();
                return back()->with('error', 'Disposisi yang sudah dibaca atau selesai tidak dapat dihapus.');
            }

            // Soft delete menjaga histori surat dan penerima.
            $disposisi->delete();

            DB::commit();

            return redirect()
                ->route('admin.disposisi.index')
                ->with('success', 'Disposisi berhasil dihapus.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Gagal menghapus disposisi. ' . $e->getMessage());

        }
    }
}
    
