<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\LogAktivitas;
use App\Models\Surat;
use App\Models\User;
use App\Models\DisposisiTujuan;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        if ($request->user()->role === 'umum') {
            $base = Surat::where('user_id', $request->user()->id);
            $statistik = [
                'total' => (clone $base)->count(),
                'aktif' => (clone $base)->whereNotIn('status', ['selesai', 'terkirim', 'diarsipkan'])->count(),
                'selesai' => (clone $base)->whereIn('status', ['selesai', 'terkirim', 'diarsipkan'])->count(),
            ];
            return view('profile.umum', ['user' => $request->user(), 'statistik' => $statistik]);
        }

        if ($request->user()->role === 'pegawai') {
            $pegawai = $request->user()->pegawai?->load(['jabatan', 'unitKerja']);
            $statistik = [
                'surat' => Surat::where('user_id', $request->user()->id)->count(),
                'disposisi_aktif' => $pegawai ? DisposisiTujuan::where('pegawai_id', $pegawai->id)->whereIn('status', ['Belum Dibaca', 'Sudah Dibaca'])->count() : 0,
                'disposisi_selesai' => $pegawai ? DisposisiTujuan::where('pegawai_id', $pegawai->id)->where('status', 'Selesai')->count() : 0,
            ];
            return view('profile.pegawai', compact('pegawai', 'statistik') + ['user' => $request->user()]);
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'layout' => match ($request->user()->role) {
                'pegawai' => 'layouts.pegawai',
                'umum' => 'layouts.umum',
                default => 'layouts.admin',
            },
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $pegawai = $user->pegawai;

            if ($pegawai) {
                validator($validated, [
                    'email' => [
                        'required',
                        Rule::unique('pegawai', 'email')->ignore($pegawai->id),
                    ],
                ])->validate();

                $pegawai->update([
                    'nama' => $validated['name'],
                    'email' => $validated['email'],
                    'no_hp' => $validated['phone'] ?? null,
                    'alamat' => $validated['address'] ?? null,
                ]);
            }

            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        });

        LogAktivitas::create(['user_id' => $user->id, 'action' => 'Perbarui Profil', 'description' => 'Informasi profil akun diperbarui.']);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /** Update the authenticated user's password. */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        LogAktivitas::create(['user_id' => $request->user()->id, 'action' => 'Ubah Password', 'description' => 'Password akun berhasil diperbarui.']);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $user = $request->user();
        $oldPhoto = $user->profile_photo_path;
        $path = $data['photo']->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);
        if ($oldPhoto) Storage::disk('public')->delete($oldPhoto);
        LogAktivitas::create(['user_id' => $user->id, 'action' => 'Ubah Foto Profil', 'description' => 'Foto profil akun berhasil diperbarui.']);

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
            LogAktivitas::create(['user_id' => $user->id, 'action' => 'Hapus Foto Profil', 'description' => 'Foto profil akun dihapus.']);
        }

        return Redirect::route('profile.edit')->with('status', 'photo-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak dapat menonaktifkan akunnya.');
        }

        LogAktivitas::create(['user_id' => $user->id, 'action' => 'Nonaktifkan Akun', 'description' => 'Pengguna menonaktifkan akun melalui halaman profil.']);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
