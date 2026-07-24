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
use App\Services\SystemNotificationService;

class AdminSuratMasukController extends Controller
{
    /**
     * Daftar Surat Masuk
     */
    public function index(Request $request)
    {
        $statusGroups = [
            'diajukan' => ['diajukan', 'menunggu'],
            'diproses' => ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'],
            'perbaikan' => ['dikembalikan', 'ditolak'],
            'selesai' => ['selesai', 'terkirim', 'diarsipkan'],
        ];
        $statusTersedia = [
            'diajukan', 'menunggu', 'diverifikasi', 'dikembalikan',
            'ditolak', 'diproses', 'diteruskan_ke_pimpinan', 'selesai',
            'terkirim', 'diarsipkan',
        ];
        $status = $request->string('status')->toString();
        $statusFilter = $statusGroups[$status] ?? (
            in_array($status, $statusTersedia, true) ? [$status] : null
        );
        $keyword = trim($request->string('keyword')->toString());
        $baseQuery = Surat::where('jenis_surat', 'masuk')->where('status', '!=', 'draft');

        $totalSurat = (clone $baseQuery)->count();
        $menunggu = (clone $baseQuery)->whereIn('status', $statusGroups['diajukan'])->count();
        $disetujui = (clone $baseQuery)->where('status', 'diverifikasi')->count();
        $ditolak = (clone $baseQuery)->whereIn('status', $statusGroups['perbaikan'])->count();
        $diproses = (clone $baseQuery)->whereIn('status', $statusGroups['diproses'])->count();
        $selesai = (clone $baseQuery)->whereIn('status', $statusGroups['selesai'])->count();

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
            ->when($statusFilter, fn ($query) => $query->whereIn('status', $statusFilter))
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

            app(SystemNotificationService::class)->notifyUser(
                $surat->user,
                'Surat disetujui',
                'Surat '.$surat->nomor_surat.' telah diverifikasi admin.',
                $this->ownerUrl($surat),
                'success',
                'bi-check-circle-fill'
            );
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

            app(SystemNotificationService::class)->notifyUser(
                $surat->user,
                'Surat perlu perbaikan',
                'Surat '.$surat->nomor_surat.' dikembalikan admin. Cek catatan perbaikan.',
                $this->ownerUrl($surat),
                'danger',
                'bi-exclamation-circle-fill'
            );
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

        $tujuanPimpinan = $surat->nama_pimpinan
            ?: optional($surat->jabatanPimpinan)->nama
            ?: 'pimpinan terkait';

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
                . ' kepada ' . $tujuanPimpinan
                . ' melalui ' . $request->metode_penerusan . '.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id' => $surat->user_id,
                'surat_id' => $surat->id,
                'action' => 'Diteruskan ke Pimpinan',
                'description' => 'Surat Anda telah diteruskan oleh admin ke pimpinan.',
            ]);

            app(SystemNotificationService::class)->notifyUser(
                $surat->user,
                'Surat diteruskan',
                'Surat '.$surat->nomor_surat.' telah diteruskan ke pimpinan terkait.',
                $this->ownerUrl($surat),
                'info',
                'bi-send-check-fill'
            );
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

        if ($surat->user_id) {
            $pemilik = \App\Models\User::find($surat->user_id);
            app(\App\Services\SystemNotificationService::class)->notifyUser(
                $pemilik,
                'Surat Masuk Dihapus',
                'Surat masuk Anda (' . $surat->nomor_surat . ') telah dihapus oleh Admin.',
                route('pegawai.surat-masuk.index'),
                'danger',
                'bi-trash'
            );
        }

        $surat->delete();

        return redirect()
            ->route('admin.surat.masuk.index')
            ->with('success','Surat berhasil dipindahkan dari daftar aktif.');
    }
    private function suratMasuk(int $id): Surat
    {
        return Surat::where('jenis_surat', 'masuk')->findOrFail($id);
    }

    private function ownerUrl(Surat $surat): ?string
    {
        return match ($surat->user?->role) {
            'umum' => route('umum.surat.show', $surat->id),
            'pegawai' => route('pegawai.surat-masuk.show', $surat->id),
            default => null,
        };
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
