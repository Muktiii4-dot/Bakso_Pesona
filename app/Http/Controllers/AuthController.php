<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::guard('customer')->check()) {
            return $this->authenticatedRedirect();
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            return $this->authenticatedRedirect();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('customer')->login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di Bakso Pesona!');
    }

    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return $this->authenticatedRedirect();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            return $this->authenticatedRedirect();
        }

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Normalisasi email agar spasi di awal/akhir dan kapitalisasi tidak menggagalkan login.
        $credentials['email'] = strtolower(trim($credentials['email']));

        if (! Auth::guard('customer')->attempt($credentials + ['role' => 'customer'], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang kamu masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Berhasil login. Selamat datang kembali!');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Kamu berhasil logout.');
    }

    private function authenticatedRedirect()
    {
        return redirect()->route('account.profile');
    }
}
