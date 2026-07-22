<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUnitKerjaController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));
        $totalUnit = UnitKerja::count();
        $unitTerpakai = UnitKerja::has('pegawai')->count();
        $unitKosong = $totalUnit - $unitTerpakai;

        $unitKerja = UnitKerja::withCount('pegawai')
            ->when($keyword, fn ($query) => $query->where(function ($sub) use ($keyword) {
                $sub->where('kode', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            }))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.unitkerja.index', compact('unitKerja', 'totalUnit', 'unitTerpakai', 'unitKosong'));
    }

    public function create()
    {
        return view('admin.unitkerja.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $data = $this->normalize($data);
        $unit = UnitKerja::create($data);
        $this->log('Tambah Unit Kerja', "Menambahkan unit kerja {$unit->nama}.");

        return redirect()->route('admin.unit.kerja.index')->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function show($id)
    {
        $unitKerja = UnitKerja::withCount('pegawai')->with(['pegawai' => fn ($query) => $query->orderBy('nama')])->findOrFail($id);
        return view('admin.unitkerja.show', compact('unitKerja'));
    }

    public function edit($id)
    {
        return view('admin.unitkerja.edit', ['unitKerja' => UnitKerja::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $unitKerja = UnitKerja::findOrFail($id);
        $data = $request->validate($this->rules($unitKerja->id), $this->messages());
        $unitKerja->update($this->normalize($data));
        $this->log('Perbarui Unit Kerja', "Memperbarui unit kerja {$unitKerja->nama}.");

        return redirect()->route('admin.unit.kerja.index')->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unitKerja = UnitKerja::withCount('pegawai')->findOrFail($id);
        if ($unitKerja->pegawai_count > 0) {
            return back()->with('error', "Unit kerja masih digunakan oleh {$unitKerja->pegawai_count} pegawai dan tidak dapat dihapus.");
        }
        $nama = $unitKerja->nama;
        $unitKerja->delete();
        $this->log('Hapus Unit Kerja', "Menghapus unit kerja {$nama} yang tidak digunakan.");

        return redirect()->route('admin.unit.kerja.index')->with('success', 'Unit kerja berhasil dihapus.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'kode' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('unit_kerja', 'kode')->ignore($id)],
            'nama' => ['required', 'string', 'max:150', Rule::unique('unit_kerja', 'nama')->ignore($id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'kode.required' => 'Kode unit kerja wajib diisi.', 'kode.unique' => 'Kode unit kerja sudah digunakan.',
            'kode.alpha_dash' => 'Kode hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'nama.required' => 'Nama unit kerja wajib diisi.', 'nama.unique' => 'Nama unit kerja sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 1.000 karakter.',
        ];
    }

    private function normalize(array $data): array
    {
        $data['kode'] = strtoupper(trim($data['kode']));
        $data['nama'] = trim($data['nama']);
        $data['deskripsi'] = filled($data['deskripsi'] ?? null) ? trim($data['deskripsi']) : null;
        return $data;
    }

    private function log(string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => $action, 'description' => $description]);
    }
}
