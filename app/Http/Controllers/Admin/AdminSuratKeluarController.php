<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminSuratKeluarController extends Controller
{
    private const STATUS = ['draft', 'diajukan', 'diverifikasi', 'diteruskan_ke_pimpinan', 'terkirim', 'diarsipkan'];

    public function index(Request $request)
    {
        $base = Surat::where('jenis_surat', 'keluar');
        $stats = (clone $base)->selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total', 'status');

        $surat = $base
            ->when($request->keyword, fn ($q, $keyword) => $q->where(function ($sub) use ($keyword) {
                $sub->where('nomor_surat', 'like', "%{$keyword}%")
                    ->orWhere('perihal', 'like', "%{$keyword}%")
                    ->orWhere('tujuan_surat', 'like', "%{$keyword}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.surat.keluar.index', compact('surat', 'stats'));
    }

    public function create()
    {
        return view('admin.surat.keluar.create', ['defaultStatus' => Setting::getValue('outgoing_default_status', 'draft')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('surat-keluar', 'public');
        }
        $data['user_id'] = auth()->id();
        $data['jenis_surat'] = 'keluar';
        $data['status'] = $request->input('status', Setting::getValue('outgoing_default_status', 'draft'));
        $data['is_priority'] = $request->boolean('is_priority');
        $surat = Surat::create($data);
        $this->log($surat, 'Tambah Surat Keluar', 'Menambahkan surat ' . $surat->nomor_surat);
        return redirect()->route('admin.surat.keluar.index')->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function show($id) { return view('admin.surat.keluar.show', ['surat' => Surat::findOrFail($id)]); }
    public function edit($id) { return view('admin.surat.keluar.edit', ['surat' => Surat::findOrFail($id)]); }

    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        $data = $request->validate($this->rules($surat->id));
        if ($request->hasFile('file_path')) {
            if ($surat->file_path) Storage::disk('public')->delete($surat->file_path);
            $data['file_path'] = $request->file('file_path')->store('surat-keluar', 'public');
        }
        $data['status'] = $request->input('status', $surat->status);
        $data['is_priority'] = $request->boolean('is_priority');
        $surat->update($data);
        $this->log($surat, 'Update Surat Keluar', 'Mengubah surat ' . $surat->nomor_surat);
        return redirect()->route('admin.surat.keluar.index')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);
        if ($surat->status !== 'draft') {
            return back()->with('error', 'Hanya surat draft yang dapat dihapus. Surat lain tetap disimpan sebagai histori.');
        }
        if ($surat->file_path) Storage::disk('public')->delete($surat->file_path);
        $this->log($surat, 'Hapus Surat Keluar', 'Mengarsipkan surat ' . $surat->nomor_surat);
        $surat->delete();
        return redirect()->route('admin.surat.keluar.index')->with('success', 'Draft surat berhasil dihapus.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'nomor_surat' => ['required', 'string', 'max:100', Rule::unique('surats', 'nomor_surat')->ignore($id)],
            'tanggal_surat' => 'required|date',
            'tanggal_kirim' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'tujuan_surat' => 'required|string|max:255',
            'penandatangan' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'nomor_agenda' => 'nullable|string|max:100',
            'metode' => 'required|in:Email,Kurir,Pos,Langsung',
            'deskripsi' => 'nullable|string',
            'is_priority' => 'nullable|boolean',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
            'status' => ['nullable', Rule::in(self::STATUS)],
        ];
    }

    private function log(Surat $surat, string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'surat_id' => $surat->id, 'action' => $action, 'description' => $description]);
    }
}
