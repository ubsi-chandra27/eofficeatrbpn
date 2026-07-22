<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            // Menambahkan 'umum' ke dalam daftar validasi
            'login_as' => ['required', 'string', 'in:admin,pegawai,umum'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Email atau NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'login_as.required' => 'Pilih jenis akun untuk masuk.',
            'login_as.in'       => 'Jenis akses tidak dikenali.',
        ];
    }

    public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $role = $this->string('login_as')->lower()->toString();
    $identifier = trim((string) $this->input('identifier'));
    $field = $role === 'umum' ? 'email' : 'nip';
    $credentials = [
        $field => $field === 'email' ? Str::lower($identifier) : $identifier,
        'password' => $this->input('password'),
        'role' => $this->input('login_as'),
    ];

    if (! Auth::attempt($credentials, $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => ($role === 'umum' ? 'Email' : 'NIP').', password, atau jenis akun tidak sesuai.',
        ]);
    }

    RateLimiter::clear($this->throttleKey());
}

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identifier')).'|'.$this->ip());
    }
}
