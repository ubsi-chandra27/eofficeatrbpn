<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View|RedirectResponse
    {
        // Jika sudah login, arahkan sesuai role
        if (Auth::check()) {

            $user = Auth::user();

            switch ($user->role) {

                case 'admin':
                    return redirect()->route('admin.dashboard');

                case 'pegawai':
                    return redirect()->route('pegawai.dashboard');

                case 'umum':
                    return redirect()->route('umum.dashboard');
            }
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autentikasi email & password
        $request->authenticate();

        // Regenerasi session
        $request->session()->regenerate();

        $user = Auth::user();

        // Role yang tersimpan di database
        $databaseRole = strtolower(trim($user->role));

        // Redirect sesuai role
        switch ($databaseRole) {

            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'pegawai':
                return redirect()->route('pegawai.dashboard');

            case 'umum':
                return redirect()->route('umum.dashboard');

            default:

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'identifier' => 'Role pengguna tidak dikenali.',
                ]);
        }
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
