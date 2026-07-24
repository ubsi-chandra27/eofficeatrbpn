<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Services\PegawaiStarterDataService;
use App\Services\RegistrationSettings;
use App\Services\UmumStarterDataService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create()
    {
        return view('auth.register', [
            'allowStaffRegistration' => RegistrationSettings::isStaffEnabled(),
        ]);
    }

    /**
     * Menyimpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $allowedRoles = RegistrationSettings::isStaffEnabled()
            ? ['admin', 'pegawai', 'umum']
            : ['umum'];

        $request->validate([
            'role' => ['required', Rule::in($allowedRoles)],
            'name' => ['required', 'string', 'max:100'],
            'nip' => ['nullable', 'required_if:role,admin,pegawai', 'string', 'max:50', 'unique:users,nip', 'unique:pegawai,nip'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'nip' => $request->role === 'umum' ? null : $request->nip,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if ($request->role === 'pegawai') {
                $pegawai = Pegawai::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip,
                    'nama' => $request->name,
                    'email' => $request->email,
                ]);

                app(PegawaiStarterDataService::class)->ensureForPegawai($pegawai);
            }

            if ($request->role === 'umum') {
                app(UmumStarterDataService::class)->ensureForUser($user);
            }

            return $user;
        });

        // Aktifkan jika menggunakan Spatie Permission
        /*
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($request->role);
        }
        */

        event(new Registered($user));

        LogAktivitas::create([
            'user_id' => $user->id,
            'action' => 'Registrasi Akun',
            'description' => 'Membuat akun '.$user->role.' melalui registrasi.',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        // Redirect sesuai role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }

        return redirect()->route('umum.dashboard');
    }
}
