<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminPegawaiController extends Controller
{
    /**
     * Menampilkan daftar pegawai
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $pegawai = Pegawai::with(['jabatan', 'unitKerja'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('nip', 'like', "%{$keyword}%");
            })
            ->orderBy('nama')
            ->paginate(10);

        return view('admin.pegawai.index', compact('pegawai'));
    }

    /**
     * Form tambah pegawai
     */
    public function create()
    {
        $jabatan = Jabatan::orderBy('nama')->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();

        return view('admin.pegawai.create', compact(
    'jabatan',
    'unitKerja'
));
    }

    /**
     * Simpan data pegawai
     */
    public function store(Request $request)
    {
        $request->validate([
        'nip' => 'required|unique:pegawai,nip|unique:users,nip',
        'nama' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email|unique:pegawai,email',
        'password' => 'required|string|min:8|confirmed',
        'no_hp' => 'nullable|max:20',
        'alamat' => 'nullable',
        'jabatan_id' => 'required|exists:jabatan,id',
        'unit_kerja_id' => 'required|exists:unit_kerja,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pegawai',
            ]);

            Pegawai::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'jabatan_id' => $request->jabatan_id,
                'unit_kerja_id' => $request->unit_kerja_id,
            ]);
        });

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * Detail pegawai
     */
    public function show(Pegawai $pegawai)
{
    $pegawai->load(['jabatan', 'unitKerja']);

    return view('admin.pegawai.show', compact('pegawai'));
}
    /**
     * Form edit pegawai
     */
    public function edit(Pegawai $pegawai)
    {
        $jabatan = Jabatan::orderBy('nama')->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();

        return view('admin.pegawai.edit', compact(
    'pegawai',
    'jabatan',
    'unitKerja'
));
    }

    /**
     * Update data pegawai
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama'            => 'required|string|max:100',
            'email'           => [
                'required', 'email',
                Rule::unique('pegawai', 'email')->ignore($pegawai->id),
                Rule::unique('users', 'email')->ignore($pegawai->user_id),
            ],
            'nip' => [
                'required',
                Rule::unique('pegawai', 'nip')->ignore($pegawai->id),
                Rule::unique('users', 'nip')->ignore($pegawai->user_id),
            ],
            'no_hp'           => 'nullable|max:20',
            'alamat'          => 'nullable',
            'jabatan_id'      => 'required|exists:jabatan,id',
            'unit_kerja_id'   => 'required|exists:unit_kerja,id',
        ]);

        DB::transaction(function () use ($request, $pegawai) {
            $user = $pegawai->user;

            if (!$user) {
                $user = User::create([
                    'name' => $request->nama,
                    'nip' => $request->nip,
                    'email' => $request->email,
                    'password' => Hash::make($request->nip),
                    'role' => 'pegawai',
                ]);
            } else {
                $user->update([
                    'name' => $request->nama,
                    'nip' => $request->nip,
                    'email' => $request->email,
                    'role' => 'pegawai',
                ]);
            }

            $pegawai->update([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'jabatan_id' => $request->jabatan_id,
                'unit_kerja_id' => $request->unit_kerja_id,
            ]);
        });

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Hapus pegawai
     */
    public function destroy(Pegawai $pegawai)
    {
        DB::transaction(function () use ($pegawai) {
            $pegawai->delete();
            $pegawai->user?->delete();
        });

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
