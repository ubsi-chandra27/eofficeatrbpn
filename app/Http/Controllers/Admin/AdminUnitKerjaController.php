<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminUnitKerjaController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));
        $penggunaan = in_array($request->input('penggunaan'), ['terpakai', 'kosong'], true)
            ? $request->input('penggunaan')
            : null;
        $totalUnit = UnitKerja::count();
        $unitTerpakai = UnitKerja::has('pegawai')->count();
        $unitKosong = $totalUnit - $unitTerpakai;

        $unitKerja = UnitKerja::withCount('pegawai')
            ->when($keyword, fn ($query) => $query->where(function ($sub) use ($keyword) {
                $sub->where('kode', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            }))
            ->when($penggunaan === 'terpakai', fn ($query) => $query->has('pegawai'))
            ->when($penggunaan === 'kosong', fn ($query) => $query->doesntHave('pegawai'))
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
        $this->normalizeRequest($request);
        $data = $request->validate($this->rules(), $this->messages());
        $data = $this->normalize($data);
        $unit = UnitKerja::create($data);
        $this->log('Tambah Unit Kerja', "Menambahkan unit kerja {$unit->nama}.");

        return redirect()->route('admin.unit.kerja.index')->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function show(UnitKerja $unitkerja)
    {
        $unitKerja = $unitkerja->loadCount('pegawai');
        $pegawai = $unitKerja->pegawai()
            ->with(['jabatan', 'user'])
            ->orderBy('nama')
            ->paginate(10, ['*'], 'pegawai_page');

        return view('admin.unitkerja.show', compact('unitKerja', 'pegawai'));
    }

    public function edit(UnitKerja $unitkerja)
    {
        return view('admin.unitkerja.edit', ['unitKerja' => $unitkerja]);
    }

    public function update(Request $request, UnitKerja $unitkerja)
    {
        $unitKerja = $unitkerja;
        $this->normalizeRequest($request);
        $data = $request->validate($this->rules($unitKerja->id), $this->messages());
        $unitKerja->update($this->normalize($data));
        $this->log('Perbarui Unit Kerja', "Memperbarui unit kerja {$unitKerja->nama}.");

        return redirect()->route('admin.unit.kerja.index')->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitkerja)
    {
        $unitKerja = $unitkerja;
        $nama = $unitKerja->nama;
        $result = DB::transaction(function () use ($unitKerja) {
            $locked = UnitKerja::lockForUpdate()->findOrFail($unitKerja->id);
            $jumlahPegawai = $locked->pegawai()->count();
            if ($jumlahPegawai > 0) {
                return $jumlahPegawai;
            }

            $locked->delete();
            return 0;
        });

        if ($result > 0) {
            return back()->with('error', "Unit kerja masih digunakan oleh {$result} pegawai dan tidak dapat dihapus.");
        }

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

    private function normalizeRequest(Request $request): void
    {
        $request->merge([
            'kode' => strtoupper(trim((string) $request->input('kode'))),
            'nama' => trim((string) $request->input('nama')),
            'deskripsi' => filled($request->input('deskripsi')) ? trim((string) $request->input('deskripsi')) : null,
        ]);
    }

    private function log(string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => $action, 'description' => $description]);
    }
}
