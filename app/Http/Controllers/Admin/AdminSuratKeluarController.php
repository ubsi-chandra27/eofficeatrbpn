<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminSuratKeluarController extends Controller
{
    private const STATUS = ['draft', 'diajukan', 'diverifikasi', 'diteruskan_ke_pimpinan', 'terkirim', 'diarsipkan'];

    public function index(Request $request)
    {
        $base = Surat::where('jenis_surat', 'keluar');
        $stats = (clone $base)->selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total', 'status');
        $status = in_array($request->string('status')->toString(), self::STATUS, true)
            ? $request->string('status')->toString()
            : null;
        $keyword = trim($request->string('keyword')->toString());

        $surat = $base
            ->when($keyword !== '', fn ($q) => $q->where(function ($sub) use ($keyword) {
                $sub->where('nomor_surat', 'like', "%{$keyword}%")
                    ->orWhere('nomor_agenda', 'like', "%{$keyword}%")
                    ->orWhere('perihal', 'like', "%{$keyword}%")
                    ->orWhere('tujuan_surat', 'like', "%{$keyword}%");
            }))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('tanggal_surat')
            ->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('admin.surat.keluar.index', compact('surat', 'stats'));
    }

    public function create()
    {
        return view('admin.surat.keluar.create', ['defaultStatus' => Setting::getValue('outgoing_default_status', 'draft')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-keluar', 'local')
            : null;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }
        $data['user_id'] = auth()->id();
        $data['jenis_surat'] = 'keluar';
        $data['status'] = $request->input('status', Setting::getValue('outgoing_default_status', 'draft'));
        $data['is_priority'] = $request->boolean('is_priority');
        try {
            DB::transaction(function () use ($data) {
                $surat = Surat::create($data);
                $this->log($surat, 'Tambah Surat Keluar', 'Menambahkan surat '.$surat->nomor_surat);
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        return redirect()->route('admin.surat.keluar.index')->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function show($id) { return view('admin.surat.keluar.show', ['surat' => $this->suratKeluar($id)]); }
    public function edit($id) { return view('admin.surat.keluar.edit', ['surat' => $this->suratKeluar($id)]); }

    public function update(Request $request, $id)
    {
        $surat = $this->suratKeluar($id);
        $data = $request->validate($this->rules($surat->id));
        $oldPath = $surat->file_path;
        $oldDisk = $oldPath ? $surat->attachmentDisk() : null;
        $newPath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('surat-keluar', 'local')
            : null;
        if ($newPath) {
            $data['file_path'] = $newPath;
        }
        $data['status'] = $request->input('status', $surat->status);
        $data['is_priority'] = $request->boolean('is_priority');
        try {
            DB::transaction(function () use ($surat, $data) {
                $surat->update($data);
                $this->log($surat, 'Update Surat Keluar', 'Mengubah surat '.$surat->nomor_surat);
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

        return redirect()->route('admin.surat.keluar.index')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = $this->suratKeluar($id);
        if ($surat->status !== 'draft') {
            return back()->with('error', 'Hanya surat draft yang dapat dihapus. Surat lain tetap disimpan sebagai histori.');
        }
        $this->log($surat, 'Hapus Surat Keluar', 'Mengarsipkan surat ' . $surat->nomor_surat);
        $surat->delete();
        return redirect()->route('admin.surat.keluar.index')->with('success', 'Draft surat berhasil dihapus.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'nomor_surat' => ['required', 'string', 'max:100', Rule::unique('surats', 'nomor_surat')->ignore($id)],
            'tanggal_surat' => 'required|date',
            'tanggal_kirim' => 'nullable|date|after_or_equal:tanggal_surat',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_surat',
            'tujuan_surat' => 'required|string|max:255',
            'penandatangan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'nomor_agenda' => 'nullable|string|max:100',
            'metode' => 'required|in:Email,Kurir,Pos,Langsung',
            'deskripsi' => 'nullable|string|max:2000',
            'is_priority' => 'nullable|boolean',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
            'status' => ['nullable', Rule::in(self::STATUS)],
        ];
    }

    private function log(Surat $surat, string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'surat_id' => $surat->id, 'action' => $action, 'description' => $description]);
    }

    private function suratKeluar(int $id): Surat
    {
        return Surat::where('jenis_surat', 'keluar')->findOrFail($id);
    }
}
