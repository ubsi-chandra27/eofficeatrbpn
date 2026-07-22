<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Surat;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

    public function laporan(Request $request)
    {
        $query = Surat::with('user:id,name')
            ->when($request->jenis_surat, fn ($q, $jenis) => $q->where('jenis_surat', $jenis))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->tanggal_mulai, fn ($q, $tanggal) => $q->whereDate('tanggal_surat', '>=', $tanggal))
            ->when($request->tanggal_selesai, fn ($q, $tanggal) => $q->whereDate('tanggal_surat', '<=', $tanggal))
            ->when($request->keyword, fn ($q, $keyword) => $q->where(function ($sub) use ($keyword) {
                $sub->where('nomor_surat', 'like', "%{$keyword}%")->orWhere('perihal', 'like', "%{$keyword}%");
            }));

        $ringkasan = [
            'total' => (clone $query)->count(),
            'masuk' => (clone $query)->where('jenis_surat', 'masuk')->count(),
            'keluar' => (clone $query)->where('jenis_surat', 'keluar')->count(),
        ];
        $surat = $query->latest('tanggal_surat')->paginate(20)->withQueryString();

        return view('admin.laporan', compact('surat', 'ringkasan'));
    }

    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        return view('admin.settings.index', [
            'jumlahAdmin' => User::where('role', 'admin')->count(),
            'jumlahPegawai' => Pegawai::count(),
            'pegawaiTanpaAkun' => Pegawai::whereNull('user_id')->count(),
            'jumlahSurat' => Surat::count(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function userIndex()
    {
        $users = User::latest()->get();

        $roles = collect(['admin', 'pegawai', 'umum'])->map(fn ($name) => (object) ['name' => $name]);

        return view('admin.users', compact(
            'users',
            'roles'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ROLE USER
    |--------------------------------------------------------------------------
    */

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,pegawai,umum'
        ]);

        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return back()->with(
                'error',
                'Anda tidak dapat mengubah role akun sendiri.'
            );
        }

        if ($request->role !== 'umum' && blank($user->nip)) {
            return back()->with('error', 'Isi NIP pengguna terlebih dahulu sebelum mengubahnya menjadi Admin atau Pegawai.');
        }

        if ($request->role === 'pegawai' && ! $user->pegawai) {
            return back()->with('error', 'Hubungkan akun dengan Data Pegawai sebelum memberikan role Pegawai.');
        }

        /*
        |------------------------------------------------------------
        | Update role pada tabel users
        |------------------------------------------------------------
        */

        $user->update([
            'role' => $request->role
        ]);

        /*
        |------------------------------------------------------------
        | Jika memakai Spatie Permission
        |------------------------------------------------------------
        */

        if (method_exists($user, 'syncRoles') && Role::where('name', $request->role)->exists()) {
            $user->syncRoles([$request->role]);
        }

        return back()->with(
            'success',
            'Role berhasil diperbarui.'
        );
    }

    public function updateUserNip(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'umum') {
            return back()->with('error', 'Akun Umum menggunakan email dan tidak memerlukan NIP.');
        }

        $data = $request->validate([
            'nip' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'nip')->ignore($user->id),
                Rule::unique('pegawai', 'nip')->ignore($user->pegawai?->id),
            ],
        ]);

        $user->update(['nip' => $data['nip']]);
        $user->pegawai?->update(['nip' => $data['nip']]);

        return back()->with('success', 'NIP akun berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    public function resetUserPassword(Request $request, $id)
    {
        $request->validate([

            'password' => [
                'required',
                'confirmed',
                'min:8'
            ]

        ]);

        $user = User::findOrFail($id);

        $user->update([

            'password' => Hash::make(
                $request->password
            )

        ]);

        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS USER
    |--------------------------------------------------------------------------
    */

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {

            return back()->with(
                'error',
                'Anda tidak dapat menghapus akun sendiri.'
            );

        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with(
            'success',
            'User berhasil dihapus.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ABOUT
    |--------------------------------------------------------------------------
    */

    public function about()
    {
        return view('admin.settings.about');
    }

    /*
    |--------------------------------------------------------------------------
    | BACKUP
    |--------------------------------------------------------------------------
    */

    public function backup()
    {
        return view('admin.settings.backup');
    }
}
