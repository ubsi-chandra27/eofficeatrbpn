<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminJabatanController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));
        $penggunaan = in_array($request->input('penggunaan'), ['terpakai', 'kosong'], true)
            ? $request->input('penggunaan')
            : null;
        $totalJabatan = Jabatan::count();
        $jabatanTerpakai = Jabatan::has('pegawai')->count();
        $jabatanKosong = $totalJabatan - $jabatanTerpakai;

        $jabatan = Jabatan::withCount('pegawai')
            ->when($keyword, fn ($query) => $query->where(function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('kode', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            }))
            ->when($penggunaan === 'terpakai', fn ($query) => $query->has('pegawai'))
            ->when($penggunaan === 'kosong', fn ($query) => $query->doesntHave('pegawai'))
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
        $this->normalizeRequest($request);
        $data = $request->validate($this->rules(), $this->messages());
        $data = $this->normalize($data);
        $jabatan = Jabatan::create($data);
        $this->log('Tambah Jabatan', "Menambahkan jabatan {$jabatan->nama}.");

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function show(Jabatan $jabatan)
    {
        $jabatan->loadCount('pegawai');
        $pegawai = $jabatan->pegawai()
            ->with(['unitKerja', 'user'])
            ->orderBy('nama')
            ->paginate(10, ['*'], 'pegawai_page');

        return view('admin.jabatan.show', compact('jabatan', 'pegawai'));
    }

    public function edit(Jabatan $jabatan)
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $this->normalizeRequest($request);
        $data = $request->validate($this->rules($jabatan->id), $this->messages());
        $data = $this->normalize($data);
        $jabatan->update($data);
        $this->log('Perbarui Jabatan', "Memperbarui jabatan {$jabatan->nama}.");

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        $result = DB::transaction(function () use ($jabatan) {
            $locked = Jabatan::lockForUpdate()->findOrFail($jabatan->id);
            $jumlahPegawai = $locked->pegawai()->count();
            if ($jumlahPegawai > 0) {
                return $jumlahPegawai;
            }

            $locked->delete();
            return 0;
        });

        if ($result > 0) {
            return back()->with('error', "Jabatan masih digunakan oleh {$result} pegawai dan tidak dapat dihapus.");
        }

        $nama = $jabatan->nama;
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

    private function normalize(array $data): array
    {
        $data['nama'] = trim($data['nama']);
        $data['kode'] = filled($data['kode'] ?? null) ? strtoupper(trim($data['kode'])) : null;
        $data['deskripsi'] = filled($data['deskripsi'] ?? null) ? trim($data['deskripsi']) : null;

        return $data;
    }

    private function normalizeRequest(Request $request): void
    {
        $request->merge([
            'nama' => trim((string) $request->input('nama')),
            'kode' => filled($request->input('kode')) ? strtoupper(trim((string) $request->input('kode'))) : null,
            'deskripsi' => filled($request->input('deskripsi')) ? trim((string) $request->input('deskripsi')) : null,
        ]);
    }

    private function log(string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => $action, 'description' => $description]);
    }
}
