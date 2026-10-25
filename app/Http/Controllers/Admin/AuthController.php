<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login khusus panel admin.
     */
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi login administrator.
     */
    public function login(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email administrator wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::guard('admin')->attempt($credentials + ['role' => 'admin'], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password administrator salah.',
            ]);
        }

        $user = Auth::guard('admin')->user();

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang di Panel Admin Bakso Pesona, '.$user->name.'!');
    }

    /**
     * Proses logout dari panel admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->regenerate();

        return redirect()->route('admin.login')
            ->with('success', 'Kamu telah berhasil keluar dari panel admin.');
    }
}
