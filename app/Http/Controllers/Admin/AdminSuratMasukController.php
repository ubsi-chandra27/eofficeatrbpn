<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Surat;
use App\Models\LogAktivitas;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminSuratMasukController extends Controller
{
    /**
     * Daftar Surat Masuk
     */
    public function index(Request $request)
    {
        $statusTersedia = [
            'diajukan', 'menunggu', 'diverifikasi', 'dikembalikan',
            'diproses', 'diteruskan_ke_pimpinan', 'selesai', 'diarsipkan',
        ];
        $status = in_array($request->string('status')->toString(), $statusTersedia, true)
            ? $request->string('status')->toString()
            : null;
        $keyword = trim($request->string('keyword')->toString());
        $baseQuery = Surat::where('jenis_surat', 'masuk')->where('status', '!=', 'draft');

        $totalSurat = (clone $baseQuery)->count();
        $menunggu = (clone $baseQuery)->whereIn('status', ['diajukan', 'menunggu'])->count();
        $disetujui = (clone $baseQuery)->where('status', 'diverifikasi')->count();
        $ditolak = (clone $baseQuery)->whereIn('status', ['dikembalikan', 'ditolak'])->count();
        $diproses = (clone $baseQuery)->whereIn('status', ['diproses', 'diteruskan_ke_pimpinan'])->count();
        $selesai = (clone $baseQuery)->whereIn('status', ['selesai', 'diarsipkan'])->count();

        $surat = (clone $baseQuery)
            ->withCount('disposisi')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nomor_surat', 'like', "%{$keyword}%")
                        ->orWhere('nomor_agenda', 'like', "%{$keyword}%")
                        ->orWhere('perihal', 'like', "%{$keyword}%")
                        ->orWhere('asal_surat', 'like', "%{$keyword}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('tanggal_surat')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.surat.masuk.index', compact(
            'surat', 'totalSurat', 'menunggu', 'disetujui', 'ditolak',
            'diproses', 'selesai', 'statusTersedia'
        ));
    }
    public function create()
    {
        return view('admin.surat.masuk.create');
    }

    /**
     * Simpan
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-masuk', 'local')
            : null;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }

        $data['jenis_surat'] = 'masuk';
        $data['status'] = Setting::getValue('incoming_default_status', 'diajukan');
        $data['user_id'] = auth()->id();
        $data['is_priority'] = $request->boolean('is_priority');

        try {
            DB::transaction(function () use ($data) {
                $surat = Surat::create($data);
                LogAktivitas::create([
                    'user_id' => auth()->id(),
                    'surat_id' => $surat->id,
                    'action' => 'Tambah Surat Masuk',
                    'description' => 'Menambahkan surat '.$surat->nomor_surat,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.surat.masuk.index')
            ->with('success','Surat berhasil ditambahkan');
    }

    /**
     * Detail
     */
    public function show($id)
    {
        $surat = $this->suratMasuk($id);

        return view('admin.surat.masuk.show', compact('surat'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $surat = $this->suratMasuk($id);

        return view('admin.surat.masuk.edit', compact('surat'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $surat = $this->suratMasuk($id);
        $data = $request->validate($this->rules($surat));
        $oldPath = $surat->file_path;
        $oldDisk = $oldPath ? $surat->attachmentDisk() : null;
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-masuk', 'local')
            : null;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }
        $data['is_priority'] = $request->boolean('is_priority');

        try {
            DB::transaction(function () use ($surat, $data) {
                $surat->update($data);
                LogAktivitas::create([
                    'user_id' => auth()->id(),
                    'surat_id' => $surat->id,
                    'action' => 'Update Surat',
                    'description' => 'Admin memperbarui data surat '.$surat->nomor_surat,
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

        return redirect()->route('admin.surat.masuk.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    /**
     * Setujui surat (lolos verifikasi)
     */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $surat = $this->suratMasuk($id);

        if ($surat->status !== 'diajukan') {
            return back()->with('error', 'Hanya surat yang telah diajukan yang dapat diverifikasi.');
        }

        $surat->update([
            'status'        => 'diverifikasi',
            'catatan_admin' => $request->catatan_admin,
        ]);

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'surat_id'    => $surat->id,
            'action'      => 'Verifikasi',
            'description' => 'Surat ' . $surat->nomor_surat . ' disetujui oleh Admin.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id'     => $surat->user_id,
                'surat_id'    => $surat->id,
                'action'      => 'Disetujui',
                'description' => 'Surat Anda telah disetujui.' . ($surat->catatan_admin ? ' Catatan: ' . $surat->catatan_admin : ''),
            ]);
        }

        return back()->with('success', 'Surat berhasil disetujui.');
    }

    /**
     * Tolak surat (gagal verifikasi)
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:1000',
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $surat = $this->suratMasuk($id);

        if ($surat->status !== 'diajukan') {
            return back()->with('error', 'Hanya surat yang telah diajukan yang dapat dikembalikan.');
        }

        $surat->update([
            'status'        => 'dikembalikan',
            'catatan_admin' => $request->catatan_admin,
        ]);

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'surat_id'    => $surat->id,
            'action'      => 'Verifikasi',
            'description' => 'Surat ' . $surat->nomor_surat . ' ditolak oleh Admin.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id'     => $surat->user_id,
                'surat_id'    => $surat->id,
                'action'      => 'Ditolak',
                'description' => 'Surat Anda ditolak. Catatan: ' . $surat->catatan_admin,
            ]);
        }

        return back()->with('success', 'Surat dikembalikan kepada pegawai untuk diperbaiki.');
    }

    /** Catat penyaluran administratif ke pimpinan (tanpa akun/modul pimpinan). */
    public function teruskanKePimpinan(Request $request, $id)
    {
        $request->validate([
            'catatan_pengantar' => 'nullable|string|max:2000',
            'metode_penerusan' => 'required|in:fisik,email,lainnya',
        ]);

        $surat = $this->suratMasuk($id);

        if ($surat->status !== 'diverifikasi') {
            return back()->with('error', 'Surat harus diverifikasi sebelum diteruskan ke pimpinan.');
        }

        if (!$surat->jabatan_pimpinan_id && !$surat->nama_pimpinan) {
            return back()->with('error', 'Tujuan pimpinan pada surat belum diisi.');
        }

        $surat->update([
            'status' => 'diteruskan_ke_pimpinan',
            'diteruskan_oleh' => auth()->id(),
            'diteruskan_pada' => now(),
            'catatan_pengantar' => $request->catatan_pengantar,
            'metode_penerusan' => $request->metode_penerusan,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'surat_id' => $surat->id,
            'action' => 'Diteruskan ke Pimpinan',
            'description' => 'Admin meneruskan surat ' . $surat->nomor_surat
                . ' kepada ' . ($surat->nama_pimpinan ?: optional($surat->jabatanPimpinan)->nama)
                . ' melalui ' . $request->metode_penerusan . '.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id' => $surat->user_id,
                'surat_id' => $surat->id,
                'action' => 'Diteruskan ke Pimpinan',
                'description' => 'Surat Anda telah diteruskan oleh admin ke pimpinan.',
            ]);
        }

        return back()->with('success', 'Penerusan surat ke pimpinan berhasil dicatat.');
    }

    /**
     * Hapus
     */
    public function destroy($id)
    {
        $surat = $this->suratMasuk($id);

        if (!in_array($surat->status, ['draft', 'dikembalikan'], true) || $surat->disposisi()->exists()) {
            return back()->with('error', 'Surat yang sudah diproses atau memiliki disposisi tidak dapat dihapus.');
        }

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'surat_id'    => $surat->id,
            'action'      => 'Hapus Surat Masuk',
            'description' => 'Menghapus surat '.$surat->nomor_surat,
        ]);

        $surat->delete();

        return redirect()
            ->route('admin.surat.masuk.index')
            ->with('success','Surat berhasil dipindahkan dari daftar aktif.');
    }
    private function suratMasuk(int $id): Surat
    {
        return Surat::where('jenis_surat', 'masuk')->findOrFail($id);
    }

    private function rules(?Surat $surat = null): array
    {
        return [
            'nomor_surat' => [
                'required', 'string', 'max:100',
                Rule::unique('surats', 'nomor_surat')->ignore($surat?->id),
            ],
            'perihal' => 'required|string|max:500',
            'tanggal_surat' => 'required|date',
            'asal_surat' => 'required|string|max:255',
            'nomor_agenda' => 'nullable|string|max:100',
            'metode' => 'required|in:Email,Kurir,Pos,Langsung',
            'deskripsi' => 'nullable|string|max:2000',
            'is_priority' => 'nullable|boolean',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
        ];
    }
}
