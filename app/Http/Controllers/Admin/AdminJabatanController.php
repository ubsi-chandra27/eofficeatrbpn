<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminJabatanController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));
        $totalJabatan = Jabatan::count();
        $jabatanTerpakai = Jabatan::has('pegawai')->count();
        $jabatanKosong = $totalJabatan - $jabatanTerpakai;

        $jabatan = Jabatan::withCount('pegawai')
            ->when($keyword, fn ($query) => $query->where(function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('kode', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            }))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jabatan.index', compact('jabatan', 'totalJabatan', 'jabatanTerpakai', 'jabatanKosong'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $data['nama'] = trim($data['nama']);
        $data['kode'] = filled($data['kode'] ?? null) ? strtoupper(trim($data['kode'])) : null;
        $jabatan = Jabatan::create($data);
        $this->log('Tambah Jabatan', "Menambahkan jabatan {$jabatan->nama}.");

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $jabatan = Jabatan::withCount('pegawai')->with(['pegawai' => fn ($query) => $query->orderBy('nama')])->findOrFail($id);
        return view('admin.jabatan.show', compact('jabatan'));
    }

    public function edit($id)
    {
        return view('admin.jabatan.edit', ['jabatan' => Jabatan::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $data = $request->validate($this->rules($jabatan->id), $this->messages());
        $data['nama'] = trim($data['nama']);
        $data['kode'] = filled($data['kode'] ?? null) ? strtoupper(trim($data['kode'])) : null;
        $jabatan->update($data);
        $this->log('Perbarui Jabatan', "Memperbarui jabatan {$jabatan->nama}.");

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::withCount('pegawai')->findOrFail($id);
        if ($jabatan->pegawai_count > 0) {
            return back()->with('error', "Jabatan masih digunakan oleh {$jabatan->pegawai_count} pegawai dan tidak dapat dihapus.");
        }
        $nama = $jabatan->nama;
        $jabatan->delete();
        $this->log('Hapus Jabatan', "Menghapus jabatan {$nama} yang tidak digunakan.");

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil dihapus.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:150', Rule::unique('jabatan', 'nama')->ignore($id)],
            'kode' => ['nullable', 'string', 'max:30', 'alpha_dash', Rule::unique('jabatan', 'kode')->ignore($id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'nama.required' => 'Nama jabatan wajib diisi.', 'nama.unique' => 'Nama jabatan sudah digunakan.',
            'kode.unique' => 'Kode jabatan sudah digunakan.', 'kode.alpha_dash' => 'Kode hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'deskripsi.max' => 'Deskripsi maksimal 1.000 karakter.',
        ];
    }

    private function log(string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => $action, 'description' => $description]);
    }
}
