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
        $keyword = trim($request->string('keyword')->toString());
        $jabatanId = $request->integer('jabatan_id') ?: null;
        $unitKerjaId = $request->integer('unit_kerja_id') ?: null;
        $statusAkun = in_array($request->string('status_akun')->toString(), ['aktif', 'belum'], true)
            ? $request->string('status_akun')->toString()
            : null;

        $base = Pegawai::query();
        $statistik = [
            'total' => (clone $base)->count(),
            'akun_aktif' => (clone $base)->whereNotNull('user_id')->whereHas('user')->count(),
            'tanpa_akun' => (clone $base)->where(fn ($query) => $query
                ->whereNull('user_id')->orWhereDoesntHave('user'))->count(),
            'profil_lengkap' => (clone $base)->whereNotNull('jabatan_id')->whereNotNull('unit_kerja_id')->count(),
        ];

        $pegawai = Pegawai::with(['jabatan', 'unitKerja', 'user'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($search) use ($keyword) {
                    $search->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('nip', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('no_hp', 'like', "%{$keyword}%");
                });
            })
            ->when($jabatanId, fn ($query) => $query->where('jabatan_id', $jabatanId))
            ->when($unitKerjaId, fn ($query) => $query->where('unit_kerja_id', $unitKerjaId))
            ->when($statusAkun === 'aktif', fn ($query) => $query->whereNotNull('user_id')->whereHas('user'))
            ->when($statusAkun === 'belum', fn ($query) => $query
                ->where(fn ($account) => $account->whereNull('user_id')->orWhereDoesntHave('user')))
            ->orderBy('nama')
            ->paginate(10);

        return view('admin.pegawai.index', [
            'pegawai' => $pegawai,
            'statistik' => $statistik,
            'jabatan' => Jabatan::orderBy('nama')->get(),
            'unitKerja' => UnitKerja::orderBy('nama')->get(),
        ]);
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
        $data = $request->validate([
            'nip' => 'required|string|max:30|unique:pegawai,nip|unique:users,nip',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email|unique:pegawai,email',
            'password' => 'required|string|min:8|confirmed',
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+()\\-\\s]+$/'],
            'alamat' => 'nullable|string|max:1000',
            'jabatan_id' => 'required|exists:jabatan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['nama'],
                'nip' => $data['nip'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'pegawai',
            ]);

            Pegawai::create([
                'user_id' => $user->id,
                ...collect($data)->except('password')->all(),
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
    $pegawai->load(['jabatan', 'unitKerja', 'user'])
        ->loadCount('disposisiTujuans');
    $jumlahSurat = \App\Models\Surat::where('user_id', $pegawai->user_id)->count();

    return view('admin.pegawai.show', compact('pegawai', 'jumlahSurat'));
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
        $data = $request->validate([
            'nama'            => 'required|string|max:100',
            'email'           => [
                'required', 'email', 'max:255',
                Rule::unique('pegawai', 'email')->ignore($pegawai->id),
                Rule::unique('users', 'email')->ignore($pegawai->user_id),
            ],
            'nip' => [
                'required', 'string', 'max:30',
                Rule::unique('pegawai', 'nip')->ignore($pegawai->id),
                Rule::unique('users', 'nip')->ignore($pegawai->user_id),
            ],
            'no_hp'           => ['nullable', 'string', 'max:20', 'regex:/^[0-9+()\\-\\s]+$/'],
            'alamat'          => 'nullable|string|max:1000',
            'jabatan_id'      => 'required|exists:jabatan,id',
            'unit_kerja_id'   => 'required|exists:unit_kerja,id',
            'password'        => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($data, $pegawai) {
            $user = $pegawai->user;

            if (!$user) {
                $user = User::create([
                    'name' => $data['nama'],
                    'nip' => $data['nip'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?: $data['nip']),
                    'role' => 'pegawai',
                ]);
            } else {
                $accountData = [
                    'name' => $data['nama'],
                    'nip' => $data['nip'],
                    'email' => $data['email'],
                    'role' => 'pegawai',
                ];
                if (!empty($data['password'])) {
                    $accountData['password'] = Hash::make($data['password']);
                }
                $user->update($accountData);
            }

            $pegawai->update([
                'user_id' => $user->id,
                ...collect($data)->except(['password'])->all(),
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
